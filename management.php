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
 * Nolej modules management table
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/nolej/classes/api.php');
require_once($CFG->dirroot . '/local/nolej/classes/module.php');
require_once($CFG->dirroot . '/local/nolej/classes/table/modules.php');

use local_nolej\module;

$contextid = optional_param('contextid', SYSCONTEXTID /* Fallback to context system. */, PARAM_INT);

// Get the context instance and course data from the context ID.
[$context, $course] = \local_nolej\utils::get_info_from_context($contextid);

// Perform security checks.
require_login($course);
require_capability('local/nolej:usenolej', $context);

// Page configuration.
$PAGE->set_url('/local/nolej/management.php', ['contextid' => $contextid]);
$PAGE->set_pagelayout('report');

\local_nolej\utils::page_setup($context, $course);
$PAGE->set_title(get_string('managemodules', 'local_nolej'));

// Init activities table.
$table = new \local_nolej\table\modules($contextid);
$table->define_baseurl($PAGE->url);

// Get table html.
ob_start();
$table->out(20, false);
$tablehtml = ob_get_contents();
ob_end_clean();

$libraryurl = new moodle_url('/local/nolej/library.php', ['contextid' => $contextid]);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template(
    'local_nolej/modulesmanagement',
    (object) [
        'libraryurl' => $libraryurl->out(true),
        'table' => $tablehtml,
    ]
);
echo $OUTPUT->footer();
