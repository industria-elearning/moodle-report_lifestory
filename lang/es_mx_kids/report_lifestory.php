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

$string['activity'] = 'Actividad';
$string['altlogo'] = 'Logo Datacurso';
$string['clearselection'] = 'Limpiar';
$string['course'] = 'Curso';
$string['coursetotal'] = 'Total del curso';
$string['error_ai_service'] = 'Error del servicio de IA: {$a}';
$string['error_airequest'] = 'Error al comunicarse con el servicio de IA: {$a}';
$string['exportcsv'] = 'Exportar a CSV';
$string['exportingcsv'] = 'Exportando csv';
$string['feedback'] = 'Retroalimentacion';
$string['feedbackfromai'] = 'Feedback de IA';
$string['generatefeedback'] = 'Genera feedback con IA';
$string['generatingfeedback'] = 'Generando feedback';
$string['gradepercent'] = 'Nota (%)';
$string['lifestory'] = 'Historia de vida del estudiante';
$string['lifestory:generateaifeedback'] = 'Generar retroalimentación con IA para los estudiantes';
$string['lifestory:view'] = 'Vea el informe de la historia de vida';
$string['noreportdata'] = 'No hay datos de informe disponibles.';
$string['noresponse'] = 'No se recibió respuesta.';
$string['pluginname'] = 'Historia de vida del estudiante IA';
$string['privacy:metadata:ai_provider'] = 'Se envían datos al sistema de IA de la empresa para generar retroalimentación basada en el historial académico del estudiante.';
$string['privacy:metadata:ai_provider:context'] = 'El contexto del análisis (por ejemplo, desempeño académico o reflexión).';
$string['privacy:metadata:ai_provider:courseids'] = 'La lista de IDs de cursos en los que el usuario está matriculado.';
$string['privacy:metadata:ai_provider:coursenames'] = 'Los nombres de los cursos en los que el usuario está matriculado.';
$string['privacy:metadata:ai_provider:fullname'] = 'El nombre completo del usuario para proporcionar contexto.';
$string['privacy:metadata:ai_provider:userid'] = 'El ID del usuario cuyo historial académico se analiza.';
$string['range'] = 'Rango';
$string['report_lifestory:generateaifeedback'] = 'Generar retroalimentación con IA sobre el desempeño del estudiante';
$string['searchusers'] = 'Buscar usuarios';
$string['section'] = 'Seccion';
$string['select'] = 'Seleccionar';
$string['selectuser'] = 'Por favor seleccione un usuario para ver su historia de vida';
$string['total'] = 'Total';
$string['unexpected_ai_error'] = 'Error inesperado en el procesamiento de IA: {$a}';
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
