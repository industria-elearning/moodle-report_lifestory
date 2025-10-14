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
 * Client API for report_student_life_story_ai.
 *
 * @package     report_student_life_story_ai
 * @copyright   2025 Datacurso
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_student_life_story_ai\api;

use aiprovider_datacurso\httpclient\ai_services_api;
use report_student_life_story_ai\local\utils;

/**
 * Client to interact with AI services.
 */
class client {
    /**
     * Sends the payload to the AI provider and returns the response.
     *
     * @param array $payload The request payload.
     * @return array The AI response.
     */
    public static function send_to_ai($payload) {
        $payload = utils::normalize_payload($payload);

        $client = new ai_services_api();

        $response = $client->request('POST', '/story/analysis', $payload);

        return $response['reply'];
    }
}
