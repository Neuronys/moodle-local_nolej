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
 * Nolej activities management table
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/nolej/classes/api.php');
require_once($CFG->dirroot . '/local/nolej/classes/module.php');
require_once($CFG->dirroot . '/local/nolej/classes/table/activities.php');

use local_nolej\module;

$contextid = optional_param('contextid', SYSCONTEXTID /* Fallback to context system. */, PARAM_INT);
$documentid = required_param('documentid', PARAM_ALPHANUMEXT);

// Get the context instance and course data from the context ID.
[$context, $course] = \local_nolej\utils::get_info_from_context($contextid);

// Perform security checks.
require_login($course);
require_capability('local/nolej:usenolej', $context);

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

// Page configuration.
$PAGE->set_url('/local/nolej/management.php', ['contextid' => $context->id, 'documentid' => $documentid]);
$PAGE->set_pagelayout('standard');

\local_nolej\utils::page_setup($context, $course);
$PAGE->set_title(get_string('library', 'local_nolej'));

// JS dependencies.
$PAGE->requires->js_call_amd('local_nolej/activitydelete');

// Init activities table.
$table = new \local_nolej\table\activities($documentid);
$table->define_baseurl(
        new moodle_url(
        '/local/nolej/management.php',
        [
            'documentid' => $documentid,
            'contextid' => $contextid,
        ]
    )
);

// Get table html.
ob_start();
$table->out(20, false);
$tablehtml = ob_get_contents();
ob_end_clean();

$bulklabel = $label = html_writer::tag(
    'label',
    get_string('withselectedactivities', 'local_nolej'),
    [
        'for' => 'formactionid',
        'class' => 'col-form-label d-inline',
    ]
);
$bulkactions = html_writer::select(
    [
        '#delete' => get_string('delete'),
    ],
    'formaction',
    '',
    ['' => 'choosedots'],
    [
        'id' => 'formactionid',
        'class' => 'ml-2',
        'data-action' => 'toggle',
        'data-togglegroup' => $table->uniqueid,
        'data-toggle' => 'action',
        'disabled' => 'disabled',
    ]
);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageactivitiesofmodule', 'local_nolej', (object) ['title' => $document->title]));
echo $OUTPUT->render_from_template(
    'local_nolej/activitiesmanagement',
    (object) [
        'sesskey' => sesskey(),
        'contextid' => $contextid,
        'documentid' => $documentid,
        'table' => $tablehtml,
        'bulklabel' => $bulklabel,
        'bulkactions' => $bulkactions,
    ]
);
echo $OUTPUT->footer();
