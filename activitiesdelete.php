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
 * Nolej activities deletion.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/nolej/classes/module.php');

use local_nolej\module;
use core\output\notification;

$contextid = required_param('contextid', PARAM_INT);
$documentid = required_param('documentid', PARAM_ALPHANUMEXT);

// Get the context instance from its id.
$context = context::instance_by_id($contextid);
$courseid = null;

// If the context is a course context or a sub-context of a course.
if (($coursecontext = $context->get_course_context(false)) !== false) {
    // We define the current context as being the course context.
    // We don't want to use sub-contexts here.
    $context = $coursecontext;

    // Retrieve the course ID to check if the user is logged in.
    $courseid = $coursecontext->instanceid;
}

// Perform security checks.
require_login($courseid);
require_sesskey();
require_capability('local/nolej:usenolej', $context);

// Delete a single activity or multiple activities.
$activityid = optional_param('activityid', 0, PARAM_INT);
$activityids = optional_param_array('activityids', [], PARAM_INT);

if ($activityid > 0) {
    // A single activity will be deleted.
    $activityids = [$activityid];
} else if (empty($activityids)) {
    // No data.
    redirect(
        new moodle_url('/local/nolej/manage.php', ['contextid' => $context->id]),
        get_string('errdatamissing', 'local_nolej'),
        null,
        notification::NOTIFY_ERROR
    );
}

// Retrieve document data.
$document = $DB->get_record('local_nolej_module', ['document_id' => $documentid, 'user_id' => $USER->id]);
if (!$document) {
    // Document does not exist. Redirect to the library.
    redirect(
        new moodle_url('/local/nolej/manage.php', ['contextid' => $context->id]),
        get_string('modulenotfound', 'local_nolej'),
        null,
        notification::NOTIFY_ERROR
    );
}

$module = new module($context->id, $document);
$success = $module->deleteactivities($activityids, $savedbytes, $errormessage);
$issingle = count($activityids) == 1;
$params = (object) [
    'savedspace' => display_size($savedbytes),
    'failed' => $errormessage,
];

if ($success) {
    // Activities have been deleted.
    redirect(
        new moodle_url(
            '/local/nolej/management.php',
            [
                'contextid' => $context->id,
                'documentid' => $documentid,
            ]
        ),
        get_string($issingle ? 'activitydeleted' : 'activitiesdeleted', 'local_nolej', $params),
        null,
        notification::NOTIFY_SUCCESS
    );
} else {
    // An error (or more) occurred.
    redirect(
        new moodle_url(
            '/local/nolej/management.php',
            [
                'contextid' => $context->id,
                'documentid' => $documentid,
            ]
        ),
        get_string($issingle ? 'activitydeletefail' : 'activitiesdeletefail', 'local_nolej', $params),
        null,
        notification::NOTIFY_ERROR
    );
}
