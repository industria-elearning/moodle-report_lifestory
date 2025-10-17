<?php
// This file is part of Moodle - http://moodle.org/.
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
 * Privacy subsystem implementation for aiprovider_datacurso.
 *
 * @package    report_lifestory
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_lifestory\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;

/**
 * Privacy provider for report_lifestory.
 *
 * @package    report_lifestory
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Describe the types of personal data transmitted by this plugin.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_external_location_link(
            'ai_provider',
            [
                'userid'      => 'privacy:metadata:ai_provider:userid',
                'fullname'    => 'privacy:metadata:ai_provider:fullname',
                'courseids'   => 'privacy:metadata:ai_provider:courseids',
                'coursenames' => 'privacy:metadata:ai_provider:coursenames',
                'context'     => 'privacy:metadata:ai_provider:context',
            ],
            'privacy:metadata:ai_provider'
        );

        return $collection;
    }

    /** @inheritDoc */
    public static function get_contexts_for_userid(int $userid): contextlist {
        return new contextlist();
    }

    /** @inheritDoc */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        // No local data to delete.
    }

    /** @inheritDoc */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        // No local data to delete.
    }

    /** @inheritDoc */
    public static function export_user_data(approved_contextlist $contextlist): void {
        // No local data to export.
    }
}
