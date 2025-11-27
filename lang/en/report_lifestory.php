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
 * Plugin strings are defined here.
 *
 * @package     report_lifestory
 * @category    string
 * @copyright   2025 Datacurso
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activity'] = 'Activity';
$string['altlogo'] = 'Datacurso logo';
$string['clearselection'] = 'Clear selection';
$string['course'] = 'Course';
$string['coursetotal'] = 'Course total';
$string['error_ai_service'] = 'AI service error: {$a}';
$string['error_airequest'] = 'Error communicating with AI service: {$a}';
$string['exportcsv'] = 'Export to CSV';
$string['exportingcsv'] = 'Exporting CSV';
$string['feedback'] = 'Feedback';
$string['feedbackfromai'] = 'AI feedback';
$string['generatefeedback'] = 'Generate AI feedback';
$string['generatingfeedback'] = 'Generating feedback';
$string['gradepercent'] = 'Grade (%)';
$string['lifestory'] = 'Student life story';
$string['lifestory:generateaifeedback'] = 'Generate AI feedback for students';
$string['lifestory:view'] = 'View life story report';
$string['noreportdata'] = 'No report data available.';
$string['noresponse'] = 'No response received.';
$string['pluginname'] = 'AI Student Life Story';
$string['privacy:metadata:ai_provider'] = 'Data is sent to the company’s AI system to generate feedback based on the student’s academic history.';
$string['privacy:metadata:ai_provider:context'] = 'The context of the analysis (e.g., academic performance or reflection).';
$string['privacy:metadata:ai_provider:courseids'] = 'List of course IDs in which the user is enrolled.';
$string['privacy:metadata:ai_provider:coursenames'] = 'Names of the courses in which the user is enrolled.';
$string['privacy:metadata:ai_provider:fullname'] = 'The user’s full name to provide context.';
$string['privacy:metadata:ai_provider:userid'] = 'The ID of the user whose academic history is analyzed.';
$string['range'] = 'Range';
$string['report_lifestory:generateaifeedback'] = 'Generate AI feedback on student performance';
$string['searchusers'] = 'Search users';
$string['section'] = 'Section';
$string['select'] = 'Select';
$string['selectuser'] = 'Please select a user to view their life story';
$string['total'] = 'Total';
$string['unexpected_ai_error'] = 'Unexpected AI processing error: {$a}';
$string['response_structure'] = 'Estructura de respuesta';
$string['response_structure_desc'] = 'Personaliza la estructura del prompt que se enviará a la IA para generar la historia de vida del estudiante.';
$string['preferences_saved'] = 'Preferencias guardadas correctamente';
$string['lifestory_settings'] = 'Configuración para historia de vida del estudiante';
$string['response_structure_default'] = '🧠 PROMPT — STUDENT PERFORMANCE PROFILE (MOODLE)

Analyze the following set of grades for a student from Moodle and generate a complete profile divided into stages. Use the data to identify strengths, weaknesses, temporal evolution, and personalized suggestions.

Follow this exact output structure:

1. Initial Stage: General Overview
2. Intermediate Stage: Analysis by Areas or Categories
3. Advanced Stage: Temporal Evolution
4. Final Stage: Student Profile
5. Feedback and Improvement Plan Stage

Rules:
- Use clear, empathetic, and positive language.
- If data is missing, indicate it and suggest what information would be useful to add.
- Maintain structured format by stages with visible subtitles.
- Personalize the analysis based on the provided data.';
