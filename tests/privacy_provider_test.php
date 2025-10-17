<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Privacy provider test for report_lifestory.
 *
 * @package   report_lifestory
 * @category  test
 * @copyright 2025 Datacurso
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_lifestory;

use core_privacy\local\metadata\null_provider;
use core_privacy\tests\provider_testcase;
use report_lifestory\privacy\provider;

/**
 * Unit tests for the privacy provider of report_lifestory.
 *
 * @group report_lifestory
 */
final class privacy_provider_test extends provider_testcase {
    /**
     * Test that the privacy provider implements null_provider.
     */
    public function test_implements_null_provider(): void {
        $this->assertInstanceOf(null_provider::class, new provider());
    }

    /**
     * Test that the get_reason() method returns the correct language string identifier.
     */
    public function test_get_reason_returns_correct_identifier(): void {
        $reason = provider::get_reason();
        $this->assertEquals('privacy:metadata', $reason);
    }
}
