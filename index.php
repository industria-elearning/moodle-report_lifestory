<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Report index page for student_life_story_ai.
 *
 * @package     report_student_life_story_ai
 * @copyright   2025 Datacurso
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/report/lib.php');

use report_student_life_story_ai\api\client;
use report_student_life_story_ai\local\utils;

$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

// Exportar CSV.
if ($userid && $action === 'csv') {
    $payload = utils::build_student_payload($userid);
    $payload = utils::normalize_payload($payload);
    utils::export_to_csv($payload);
    exit;
}

// Configuración de la página.
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url(new moodle_url('/report/student_life_story_ai/index.php', ['userid' => $userid, 'id' => $courseid]));
$PAGE->set_title(get_string('student_life_story', 'report_student_life_story_ai'));
$PAGE->set_heading(get_string('student_life_story', 'report_student_life_story_ai'));

$PAGE->requires->js_call_amd('gradereport_user/user', 'init');
$PAGE->requires->js_call_amd('report_student_life_story_ai/togglecategories', 'init');
$PAGE->requires->js_call_amd('report_student_life_story_ai/button_loader', 'init');
$PAGE->requires->css(new moodle_url('/report/student_life_story_ai/styles/history_student.css'));

echo $OUTPUT->header();

// Selector de estudiantes (solo rol estudiante).
$role = $DB->get_record('role', ['shortname' => 'student']); // Rol "student".
$options = [];

// Agrega una opción inicial "Seleccionar usuario" traducible.
$selectdefault = [
    [
        'id' => 0,
        'name' => get_string('select', 'report_student_life_story_ai'),
        'selected' => ($userid == 0),
    ],
];

if ($role) {
    // Busca todos los contextos donde ese rol está asignado (nivel curso o superior).
    $assignments = $DB->get_records('role_assignments', ['roleid' => $role->id]);

    $userids = array_unique(array_column($assignments, 'userid'));
    list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

    if (!empty($userids)) {
        $students = $DB->get_records_select(
            'user',
            "id $insql AND deleted = 0",
            $inparams,
            'lastname ASC, firstname ASC',
            'id, firstname, lastname'
        );
        foreach ($students as $u) {
            $options[$u->id] = fullname($u);
        }
    }
}

// Convierte a formato para Mustache.
$users = array_map(function ($id, $name) use ($userid) {
    return [
        'id' => $id,
        'name' => $name,
        'selected' => ($id == $userid),
    ];
}, array_keys($options), $options);

// Une la opción "Seleccionar usuario" al inicio del array.
$users = array_merge($selectdefault, $users);

// Historial de calificaciones.
$coursesdata = [];

if ($userid) {
    if ($courseid) {
        $coursesdata[] = [
            'id' => $courseid,
            'fullname' => get_course($courseid)->fullname,
            'reporthtml' => get_report_html($courseid, $userid),
        ];
    } else {
        $courses = enrol_get_users_courses($userid);
        foreach ($courses as $course) {
            $coursesdata[] = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'reporthtml' => get_report_html($course->id, $userid),
            ];
        }
    }
}

// Feedback IA.
$feedbackhtml = null;

if ($userid && $action === 'feedback') {
    $payload = utils::build_student_payload($userid);
    $response = client::send_to_ai($payload);
    $feedbackhtml = '<pre class="bg-light p-3 ai-feedback">' . s($response['reply']) . '</pre>';
}

// Render Mustache.
$renderer = $PAGE->get_renderer('core');
$headerlogo = new \report_student_life_story_ai\output\header_logo();
$logocontext = $headerlogo->export_for_template($renderer);

$templatecontext = [
    'selecturl' => new moodle_url('/report/student_life_story_ai/index.php'),
    'userid' => $userid,
    'users' => $users,
    'hasuser' => (bool)$userid,
    'courses' => $coursesdata,
    'feedback' => $feedbackhtml,
    'showfeedback' => !empty($feedbackhtml),
    'headerlogo' => $logocontext,
];

echo $OUTPUT->render_from_template('report_student_life_story_ai/history_student', $templatecontext);
echo $OUTPUT->footer();


/**
 * Generates the grade report HTML for a given course and user.
 *
 * @param int $courseid The course ID.
 * @param int $userid The user ID.
 * @return string Rendered HTML for the grade report.
 */
function get_report_html($courseid, $userid) {
    global $OUTPUT;

    $coursecontext = context_course::instance($courseid);
    $gpr = new grade_plugin_return([
        'type' => 'report',
        'plugin' => 'student_life_story_ai',
        'courseid' => $courseid,
        'userid' => $userid,
    ]);

    $report = new \gradereport_user\report\user($courseid, $gpr, $coursecontext, $userid);
    $report->showcontributiontocoursetotal = true;
    $report->process_data([]);
    $report->setup_table();

    if ($report->fill_table()) {
        return $report->print_table(true);
    }

    return $OUTPUT->notification(get_string('noreportdata', 'report_student_life_story_ai'), 'warning');
}
