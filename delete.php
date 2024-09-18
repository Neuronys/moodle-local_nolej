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
 * Nolej module edit steps, from creation to activity generation.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/nolej/classes/module.php');

use local_nolej\module;
use core\output\notification;

$moduleid = required_param('moduleid', PARAM_INT);
$contextid = optional_param('contextid', SYSCONTEXTID, PARAM_INT); // Fallback to context system.

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

$success = module::delete($moduleid);

if ($success) {
    // Module deleted.
    redirect(
        new moodle_url('/local/nolej/manage.php', ['contextid' => $context->id]),
        get_string('moduledeleted', 'local_nolej'),
        null,
        notification::NOTIFY_SUCCESS
    );
} else {
    // Module not found.
    redirect(
        new moodle_url('/local/nolej/manage.php', ['contextid' => $context->id]),
        get_string('modulenotfound', 'local_nolej'),
        null,
        notification::NOTIFY_ERROR
    );
}
