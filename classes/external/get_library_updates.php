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

namespace local_nolej\external;

defined('MOODLE_INTERNAL') || die();

use context;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use local_nolej\module;

require_once("$CFG->libdir/externallib.php"); // Required for Moodle 4.1.

/**
 * Get library updates service.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_library_updates extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        // No parameters needed.
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'Current context ID', VALUE_DEFAULT, SYSCONTEXTID),
        ]);
    }

    /**
     * Fetch and returns updates regarding the Nolej modules.
     * @param int $contextid The current context ID.
     * @return array of updated modules
     */
    public static function execute(int $contextid): array {
        global $DB, $USER;

        // Perform security checks.
        $params = self::validate_parameters(self::execute_parameters(), ['contextid' => $contextid]);

        $context = context::instance_by_id($params['contextid']);
        self::validate_context($context);
        require_capability('local/nolej:usenolej', $context);

        // Fetch the unread notifications.
        $activities = $DB->get_records(
            'local_nolej_activity',
            [
                'user_id' => $USER->id,
                'notified' => 0,
            ],
            'tstamp ASC',
            'id, document_id, action, tstamp, status, error_message'
        );

        $updates = [];
        foreach ($activities as $activity) {
            // Fetch module.
            $module = $DB->get_record(
                'local_nolej_module',
                [
                    'user_id' => $USER->id,
                    'document_id' => $activity->document_id,
                ]
            );

            $messagedata = (object) [
                'title' => $module->title,
                'tstamp' => userdate($activity->tstamp),
                'errormessage' => $activity->error_message,
            ];

            $updates[] = [
                'id' => $module->id,
                'documentid' => $module->document_id,
                'title' => $module->title,
                'status' => module::getstatusname((int) $module->status),
                'lastupdate' => userdate($activity->tstamp),
                'message' => get_string('action_' . $activity->action . '_body', 'local_nolej', $messagedata),
                'success' => $activity->status == 'ok',
            ];

            // Set as notified.
            $DB->update_record(
                'local_nolej_activity',
                (object) [
                    'id' => $activity->id,
                    'notified' => 1,
                ],
            );
        }

        // Return the module updates.
        return ['updates' => $updates];
    }

    /**
     * Return description of method result value
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'updates' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Id of the module'),
                    'documentid' => new external_value(PARAM_ALPHANUMEXT, 'Document id of the module'),
                    'title' => new external_value(PARAM_TEXT, 'Title of the module'),
                    'status' => new external_value(PARAM_TEXT, 'New status of the module'),
                    'lastupdate' => new external_value(PARAM_TEXT, 'Formatted datetime of the last update'),
                    'message' => new external_value(PARAM_TEXT, 'Message to be notified to the user'),
                    'success' => new external_value(PARAM_BOOL, 'The notification is of type success or a failure'),
                ])
            ),
        ]);
    }
}
