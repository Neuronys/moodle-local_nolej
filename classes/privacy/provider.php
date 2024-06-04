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
 * Privacy API implementation for the nolej local plugin.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy API implementation for the nolej local plugin.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider
{

    /**
     * Get metadata collection for plugin.
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection
    {

        // Nolej module table.
        $collection->add_database_table(
            'nolej_module',
            [
                'user_id' => 'privacy:metadata:nolej_module:user_id',
                'tstamp' => 'privacy:metadata:nolej_module:tstamp',
            ],
            'privacy:metadata:nolej_module'
        );

        // Nolej activity table.
        $collection->add_database_table(
            'nolej_activity',
            [
                'user_id' => 'privacy:metadata:nolej_activity:user_id',
                'tstamp' => 'privacy:metadata:nolej_activity:tstamp',
                'action' => 'privacy:metadata:nolej_activity:action',
            ],
            'privacy:metadata:nolej_activity'
        );

        // Nolej endpoint.
        $collection->add_external_location_link(
            'endpoint',
            [
                'user_id' => 'privacy:metadata:endpoint:user_id',
            ],
            'privacy:metadata:endpoint'
        );

        // Files uploaded by the user.
        $collection->add_subsystem_link(
            'core_files',
            [],
            'privacy:metadata:core_files'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid): contextlist
    {
        global $DB;

        $contextlist = new contextlist();

        $modules = $DB->get_records(
            'nolej_module',
            ['user_id' => $userid],
        );
        $activities = $DB->get_records(
            'nolej_activity',
            ['user_id' => $userid],
        );

        if (!empty($modules) || !empty($activities)) {
            $contextlist->add_system_context();
        }

        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist)
    {
        global $DB;

        $userid = $contextlist->get_user()->id;
        $contexts = $contextlist->get_contexts();

        foreach ($contexts as $context) {
            if ($context->contextlevel != CONTEXT_SYSTEM) {
                // We only have data at the system level.
                continue;
            }

            $modules = $DB->get_records(
                'nolej_module',
                ['user_id' => $userid],
                'tstamp DESC',
                'user_id, tstamp',
            );

            // Export the nolej_module user's data.
            foreach ($modules as $module) {
                writer::with_context($context)->export_data(
                    'privacy:metadata:nolej_module',
                    $module
                );
            }

            $activities = $DB->get_records(
                'nolej_activity',
                ['user_id' => $userid],
                'tstamp DESC',
                'user_id, tstamp, action',
            );

            // Export the nolej_activity user's data.
            foreach ($activities as $activity) {
                writer::with_context($context)->export_data(
                    'privacy:metadata:nolej_activity',
                    $activity
                );
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context)
    {
        global $DB;

        if ($context->contextlevel != CONTEXT_SYSTEM) {
            // We only have data at the system level.
            return;
        }

        // Delete all data from the nolej_module and nolej_activity tables.
        $DB->delete_records('nolej_module');
        $DB->delete_records('nolej_activity');
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist)
    {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            // Delete all data from the nolej_module and nolej_activity tables.
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $DB->delete_records(
                    'nolej_module',
                    ['user_id' => $userid]
                );
                $DB->delete_records(
                    'nolej_activity',
                    ['user_id' => $userid]
                );
            }
        }
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist)
    {
        global $DB;

        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_SYSTEM) {
            // We only have data at the system level.
            return;
        }

        // Fetch all users who have data in the nolej_module table.
        $modules = $DB->get_records(
            'nolej_module',
            null,
            'user_id',
            'DISTINCT user_id',
        );
        $userlist->add_users(array_keys($modules));

        $activities = $DB->get_records(
            'nolej_activity',
            null,
            'user_id',
            'DISTINCT user_id',
        );
        $userlist->add_users(array_keys($activities));
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist)
    {
        global $DB;

        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_SYSTEM) {
            // We only have data at the system level.
            return;
        }

        $userids = $userlist->get_userids();
        $DB->delete_records_list('nolej_module', 'user_id', $userids);
        $DB->delete_records_list('nolej_activity', 'user_id', $userids);
    }
}
