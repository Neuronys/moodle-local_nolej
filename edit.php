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
 * @author      2024 Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();
require_capability('local/nolej:usenolej', $context);

require_once ($CFG->dirroot . '/local/nolej/classes/api.php');
require_once ($CFG->dirroot . '/local/nolej/classes/module.php');

if (!\local_nolej\api\api::haskey()) {
    // API key missing
    redirect(
        new \moodle_url('/local/nolej/manage.php'),
        get_string('apikeymissing', 'local_nolej'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

$documentid = optional_param('documentid', null, PARAM_ALPHANUMEXT);
$step = empty($documentid) ? null : optional_param('step', null, PARAM_ALPHA);

$PAGE->set_url(
    new \moodle_url(
        '/local/nolej/edit.php',
        [
            'documentid' => $documentid,
            'step' => $step
        ]
    )
);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/nolej/styles.css');


if (
    empty($documentid) ||
    \local_nolej\api\api::lookupdocumentstatus($documentid, $USER->id) <= \local_nolej\api\api::STATUS_CREATION
) {
    // Create a new document
    $PAGE->set_heading(get_string('status_0', 'local_nolej'));
    $PAGE->set_title(get_string('status_0', 'local_nolej'));
    $module = new \local_nolej\module\module();
    $module->creation();

} else {

    // Retrieve document data
    $document = $DB->get_record(
        'nolej_module',
        [
            'document_id' => $documentid,
            'user_id' => $USER->id
        ]
    );

    if (!$document) {
        // Document does not exist
        redirect(
            new \moodle_url('/local/nolej/edit.php'),
            get_string('modulenotfound', 'local_nolej'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }

    $module = new \local_nolej\module\module($document, $step);

    $PAGE->set_heading(get_string('status_' . $document->status, 'local_nolej'));
    $PAGE->set_title(empty($document->title) ? get_string('status_0', 'local_nolej') : $document->title);

    switch ($document->status) {
        case \local_nolej\api\api::STATUS_CREATION_PENDING:
        case \local_nolej\api\api::STATUS_ANALYSIS_PENDING:
        case \local_nolej\api\api::STATUS_ACTIVITIES_PENDING:
            // Document is pending, print info and exit
            \core\notification::add(get_string('status_' . $document->status, 'local_nolej'), \core\output\notification::NOTIFY_INFO);
            echo $OUTPUT->header();
            $module->printinfo();
            echo $OUTPUT->footer();
            return;
    }

    switch ($step) {
        case \local_nolej\module\module::STEP_ANALYSIS:
            // Analysis can be executed only if the document is not yet in revision
            if ($document->status < \local_nolej\api\api::STATUS_REVISION) {
                // Step is set, execute as requested
                $module->analysis();
            } else {
                // Step not valid, execute default
                $module->setstep(\local_nolej\module\module::STEP_CONCEPTS);
                $module->concepts();
            }
            break;

        case \local_nolej\module\module::STEP_CONCEPTS:
        case \local_nolej\module\module::STEP_QUESTIONS:
        case \local_nolej\module\module::STEP_SUMMARY:
        case \local_nolej\module\module::STEP_ACTIVITIES:
            // These revision steps can be executed only if the document is in revision
            if ($document->status >= \local_nolej\api\api::STATUS_REVISION) {
                // Step is set, execute as requested
                $module->$step();
            } else {
                // Step not valid, execute default
                $module->setstep(\local_nolej\module\module::STEP_ANALYSIS);
                $module->analysis();
            }
            break;

        default:
            // Step is not set, execute default depending on the status
            if ($document->status < \local_nolej\api\api::STATUS_REVISION) {
                $module->setstep(\local_nolej\module\module::STEP_ANALYSIS);
                $module->analysis();
            } else {
                $module->setstep(\local_nolej\module\module::STEP_CONCEPTS);
                $module->concepts();
            }
    }
}
