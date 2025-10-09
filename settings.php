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
 * Plugin administration pages are defined here.
 *
 * @package     report_history_student_ai
 * @category    admin
 * @copyright   2025 Piero Llanos <piero@datacurso.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Crear una categoría en Administración del sitio > Informes
    $ADMIN->add('reports', new admin_category('report_history_student_ai_cat',
        get_string('pluginname', 'report_history_student_ai')));

    // Agregar sublink dentro de esa categoría
    $ADMIN->add('report_history_student_ai_cat', new admin_externalpage(
        'report_history_student_ai',
        get_string('history_student_ai', 'report_history_student_ai'),
        new moodle_url('/report/history_student_ai/index.php')
    ));
}
