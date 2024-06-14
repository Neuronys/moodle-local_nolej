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
 * Nolej module list
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/nolej/classes/api.php');
require_once($CFG->dirroot . '/local/nolej/classes/module.php');

use local_nolej\module;

require_login();
$context = context_system::instance();
require_capability('local/nolej:usenolej', $context);

$PAGE->set_url(new moodle_url('/local/nolej/manage.php'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

$PAGE->set_heading(get_string('modules', 'local_nolej'));
$PAGE->set_title(get_string('library', 'local_nolej'));

$PAGE->requires->js_call_amd('local_nolej/delete');
$PAGE->requires->css('/local/nolej/styles.css');

$status2form = [
    module::STATUS_CREATION => '',
    module::STATUS_CREATION_PENDING => '',
    module::STATUS_ANALYSIS => 'analysis',
    module::STATUS_ANALYSIS_PENDING => 'analysis',
    module::STATUS_REVISION => 'concepts',
    module::STATUS_REVISION_PENDING => 'concepts',
    module::STATUS_ACTIVITIES => 'activities',
    module::STATUS_ACTIVITIES_PENDING => 'activities',
    module::STATUS_COMPLETED => 'activities',
    module::STATUS_FAILED => '',
];

$modules = $DB->get_records(
    'local_nolej_module',
    ['user_id' => $USER->id],
    'tstamp DESC'
);

$modulearray = [];
foreach ($modules as $module) {

    $moduledata = [
        'title' => $module->title,
        'status' => module::getstatusname((int) $module->status),
        'documentid' => $module->document_id,
        'created' => userdate($module->tstamp),
        'lastupdate' => '-',
        'editurl' => $module->status != module::STATUS_FAILED
            ? (
                new moodle_url(
                    '/local/nolej/edit.php',
                    [
                        'documentid' => $module->document_id,
                        'step' => $status2form[$module->status],
                    ]
                )
            )->out(false)
            : false,
        'deleteurl' => (
            new moodle_url(
                '/local/nolej/delete.php',
                ['documentid' => $module->document_id]
            )
        )->out(false),
        'contextid' => null,
    ];

    // Check last update.
    $activities = $DB->get_records(
        'local_nolej_activity',
        [
            'user_id' => $USER->id,
            'document_id' => $module->document_id,
        ],
        'tstamp DESC',
        '*',
        0,
        1
    );
    $lastactivity = $activities ? reset($activities) : false;
    if ($lastactivity) {
        $moduledata['lastupdate'] = userdate($lastactivity->tstamp);
    }

    // Check last generated activity content bank folder.
    if ($module->status == module::STATUS_COMPLETED) {
        $h5pcontents = $DB->get_records(
            'local_nolej_h5p',
            ['document_id' => $module->document_id],
            'tstamp DESC',
            'content_id',
            0,
            1
        );
        $h5pcontent = $h5pcontents ? reset($h5pcontents) : false;
        if ($h5pcontent) {
            $context = $DB->get_records(
                'contentbank_content',
                ['id' => $h5pcontent->content_id],
                '',
                'contextid',
                0,
                1
            );
            $context = $context ? reset($context) : false;
            if ($context && !empty($context->contextid)) {
                $moduledata['activitiesurl'] = (
                    new moodle_url(
                        '/contentbank/index.php',
                        ['contextid' => $context->contextid]
                    )
                )->out(false);
            }
        }
    }
    $modulearray[] = $moduledata;
}

$templatecontext = (object) [
    'modules' => $modulearray,
    'createurl' => (new moodle_url('/local/nolej/edit.php'))->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_nolej/manage', $templatecontext);
echo $OUTPUT->footer();
