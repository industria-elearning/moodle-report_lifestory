<?php
require('../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/report/lib.php');

use report_history_student_ai\api\client;

$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

// =====================================================
// EXPORTAR CSV - DEBE IR ANTES DE CUALQUIER OUTPUT
// =====================================================
if ($userid && $action === 'csv') {
    $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    $courses = enrol_get_users_courses($userid);

    $csv = "Curso,Actividad,Nota (%),Rango,Feedback\n";

    foreach ($courses as $course) {
        $gradeitems = grade_item::fetch_all(['courseid' => $course->id]);
        if (!$gradeitems) continue;

        foreach ($gradeitems as $item) {
            if (!in_array($item->itemtype, ['mod', 'manual'])) continue;

            $grade = grade_grade::fetch(['itemid' => $item->id, 'userid' => $userid]);
            $finalgrade = $grade ? floatval($grade->finalgrade) : 0.0;
            $range = '0-' . number_format($item->grademax, 2);
            $percentage = $item->grademax > 0 ? round(($finalgrade / $item->grademax) * 100, 2) : 0;
            $feedback = $grade ? strip_tags($grade->feedback) : '';

            $csv .= sprintf(
                "\"%s\",\"%s\",\"%.2f\",\"%s\",\"%s\"\n",
                $course->fullname,
                $item->get_name(),
                $percentage,
                $range,
                str_replace('"', '""', $feedback)
            );
        }
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="historial_' . $user->id . '.csv"');
    header('Content-Length: ' . strlen($csv));
    echo $csv;
    exit;
}

// =====================================================
// CONFIGURACIÓN DE LA PÁGINA
// =====================================================
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url(new moodle_url('/report/history_student_ai/index.php', ['userid' => $userid, 'id' => $courseid]));
$PAGE->set_title(get_string('history_student_ai', 'report_history_student_ai'));
$PAGE->set_heading(get_string('pluginname', 'report_history_student_ai'));

$PAGE->requires->js_call_amd('gradereport_user/user', 'init');
$PAGE->requires->js_call_amd('report_history_student_ai/togglecategories', 'init');

echo $OUTPUT->header();

// =====================================================
// 1️⃣ Selector de estudiantes
// =====================================================
$students = get_users(true, '', true, ['id', 'firstname', 'lastname', 'deleted']);
$options = [];
foreach ($students as $u) {
    if (!$u->deleted) $options[$u->id] = fullname($u);
}

$users = array_map(function ($id, $name) use ($userid) {
    return [
        'id' => $id,
        'name' => $name,
        'selected' => ($id == $userid)
    ];
}, array_keys($options), $options);

// =====================================================
// 2️⃣ Historial de calificaciones
// =====================================================
$coursesdata = [];

if ($userid) {
    if ($courseid) {
        $coursesdata[] = [
            'id' => $courseid,
            'fullname' => get_course($courseid)->fullname,
            'reporthtml' => get_report_html($courseid, $userid)
        ];
    } else {
        $courses = enrol_get_users_courses($userid);
        foreach ($courses as $course) {
            $coursesdata[] = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'reporthtml' => get_report_html($course->id, $userid)
            ];
        }
    }
}

// =====================================================
// 3️⃣ Feedback IA
// =====================================================
$feedbackhtml = null;

if ($userid && $action === 'feedback') {
    global $DB, $CFG;

    $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    $courses = enrol_get_users_courses($userid);

    $payload = [
        'site_id' => md5($CFG->wwwroot),
        'student_id' => (string)$user->id,
        'student_name' => fullname($user),
        'courses' => []
    ];

    foreach ($courses as $course) {
        $coursecontext = context_course::instance($course->id);
        $sections = [];

        // ======================================================
        // A. Categorías (cortes)
        // ======================================================
        $categories = grade_category::fetch_all(['courseid' => $course->id]);
        $hascategories = false;

        if ($categories) {
            foreach ($categories as $cat) {
                if ($cat->is_course_category()) continue;

                $items = grade_item::fetch_all(['categoryid' => $cat->id]);
                if (!$items) continue;

                $hascategories = true;
                $tasks = [];

                foreach ($items as $item) {
                    if (!in_array($item->itemtype, ['mod', 'manual'])) continue;

                    $grade = grade_grade::fetch(['itemid' => $item->id, 'userid' => $userid]);
                    $finalgrade = $grade ? floatval($grade->finalgrade) : 0.0;
                    $range = '0-' . number_format($item->grademax, 2);
                    $percentage = $item->grademax > 0 ? round(($finalgrade / $item->grademax) * 100, 2) : 0.0;
                    $feedback = $grade ? trim(strip_tags($grade->feedback)) : null;

                    // Ponderación real del ítem dentro de la categoría
                    $weight = isset($item->aggregationcoef2) && $item->aggregationcoef2 > 0
                        ? round($item->aggregationcoef2 * 100, 2)
                        : (float)$item->grademax;

                    $tasks[] = [
                        'name' => format_string($item->get_name(), true, ['context' => $coursecontext]),
                        'calculated_weight' => $weight,
                        'grade' => $finalgrade,
                        'range' => $range,
                        'percentage' => $percentage,
                        'feedback' => $feedback,
                        'contribution_to_total' => $percentage
                    ];
                }

                if (!empty($tasks)) {
                    $weights = array_column($tasks, 'calculated_weight');
                    $grades = array_column($tasks, 'grade');
                    $maxweight = max($weights ?: [0]);
                    $totalgrade = array_sum($grades);
                    $totalpercentage = $maxweight > 0 ? round(($totalgrade / (count($grades) * $maxweight)) * 100, 2) : 0;

                    // Ponderación real del corte
                    $catweight = isset($cat->aggregationcoef2) && $cat->aggregationcoef2 > 0
                        ? round($cat->aggregationcoef2 * 100, 2)
                        : null;

                    $sections[] = [
                        'name' => format_string($cat->get_name(), true, ['context' => $coursecontext]),
                        'tasks' => $tasks,
                        'total' => [
                            'calculated_weight' => $catweight ?? array_sum($weights),
                            'grade' => $totalgrade,
                            'range' => '0-' . number_format($maxweight, 2),
                            'percentage' => $totalpercentage,
                            'contribution_to_total' => $catweight
                        ]
                    ];
                }
            }
        }

        // ======================================================
        // B. Sin categorías
        // ======================================================
        if (!$hascategories) {
            $items = grade_item::fetch_all(['courseid' => $course->id]);
            $tasks = [];

            foreach ($items as $item) {
                if ($item->itemtype !== 'mod') continue;
                $grade = grade_grade::fetch(['itemid' => $item->id, 'userid' => $userid]);
                $finalgrade = $grade ? floatval($grade->finalgrade) : 0.0;
                $range = '0-' . number_format($item->grademax, 2);
                $percentage = $item->grademax > 0 ? round(($finalgrade / $item->grademax) * 100, 2) : 0.0;
                $feedback = $grade ? trim(strip_tags($grade->feedback)) : null;

                // Usar ponderación real del ítem
                $weight = isset($item->aggregationcoef2) && $item->aggregationcoef2 > 0
                    ? round($item->aggregationcoef2 * 100, 2)
                    : (float)$item->grademax;

                $tasks[] = [
                    'name' => format_string($item->get_name(), true, ['context' => $coursecontext]),
                    'calculated_weight' => $weight,
                    'grade' => $finalgrade,
                    'range' => $range,
                    'percentage' => $percentage,
                    'feedback' => $feedback,
                    'contribution_to_total' => $percentage
                ];
            }

            if (!empty($tasks)) {
                $weights = array_column($tasks, 'calculated_weight');
                $grades = array_column($tasks, 'grade');
                $maxweight = max($weights ?: [0]);
                $totalgrade = array_sum($grades);
                $totalpercentage = $maxweight > 0 ? round(($totalgrade / (count($grades) * $maxweight)) * 100, 2) : 0;

                $sections[] = [
                    'name' => $course->fullname,
                    'tasks' => $tasks,
                    'total' => [
                        'calculated_weight' => array_sum($weights),
                        'grade' => $totalgrade,
                        'range' => '0-' . number_format($maxweight, 2),
                        'percentage' => $totalpercentage,
                        'contribution_to_total' => null
                    ]
                ];
            }
        }

        // ======================================================
        // C. Total del curso
        // ======================================================
        $allgrades = [];
        $allweights = [];
        foreach ($sections as $sec) {
            foreach ($sec['tasks'] as $t) {
                $allgrades[] = $t['grade'];
                $allweights[] = $t['calculated_weight'];
            }
        }

        $totalgrade = array_sum($allgrades);
        $totalweight = array_sum($allweights);
        $totalpercentage = $totalweight > 0 ? round(($totalgrade / $totalweight) * 100, 2) : 0;

        $courseTotal = [
            'calculated_weight' => $totalweight,
            'grade' => $totalgrade,
            'range' => '0-' . number_format(max($allweights ?: [0]), 2),
            'percentage' => $totalpercentage,
            'contribution_to_total' => null
        ];

        if (count($sections) === 1 && $sections[0]['name'] === $course->fullname) {
            $courseTotal = $sections[0]['total'];
        }

        $payload['courses'][] = [
            'name' => $course->fullname,
            'sections' => array_values($sections),
            'total' => $courseTotal
        ];
    }

    // Enviar a la IA
    $response = client::send_to_ai($payload);
    $feedbackhtml = '<pre class="bg-light p-3">' . s($response['reply']) . '</pre>';
}

// =====================================================
// 4️⃣ Render Mustache
// =====================================================
$templatecontext = [
    'selecturl' => new moodle_url('/report/history_student_ai/index.php'),
    'userid' => $userid,
    'users' => $users,
    'hasuser' => (bool)$userid,
    'courses' => $coursesdata,
    'feedback' => $feedbackhtml,
    'showfeedback' => !empty($feedbackhtml)
];

echo $OUTPUT->render_from_template('report_history_student_ai/history_student', $templatecontext);
echo $OUTPUT->footer();

// =====================================================
// Helper: Render HTML del reporte de calificaciones
// =====================================================
function get_report_html($courseid, $userid)
{
    global $OUTPUT;
    $coursecontext = context_course::instance($courseid);
    $gpr = new grade_plugin_return([
        'type' => 'report',
        'plugin' => 'history_student_ai',
        'courseid' => $courseid,
        'userid' => $userid
    ]);

    $report = new \gradereport_user\report\user($courseid, $gpr, $coursecontext, $userid);
    $report->showcontributiontocoursetotal = true;
    $report->process_data([]);
    $report->setup_table();

    if ($report->fill_table()) {
        return $report->print_table(true);
    }

    return $OUTPUT->notification(get_string('noreportdata', 'report_history_student_ai'), 'warning');
}
