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

$contextid = optional_param('contextid', SYSCONTEXTID /* Fallback to context system. */, PARAM_INT);

// Get the context instance and course data from the context ID.
[$context, $course] = \local_nolej\utils::get_info_from_context($contextid);

// Perform security checks.
require_login($course);
require_capability('local/nolej:usenolej', $context);

// Page configuration.
$PAGE->set_url('/local/nolej/manage.php', ['contextid' => $context->id]);
$PAGE->set_pagelayout('standard');

\local_nolej\utils::page_setup($context, $course);
$PAGE->set_title(get_string('library', 'local_nolej'));

// JS and CSS dependencies.
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

    // Actions menu.
    $menu = new action_menu();
    $menu->set_menu_trigger(get_string('actions'));

    // Activities preview link.
    if ($module->status == module::STATUS_COMPLETED) {
        $contentbankurl = module::getcontentbankurl($module->document_id);
        if ($contentbankurl) {
            $menu->add(
                new action_menu_link(
                    $contentbankurl,
                    new pix_icon('i/preview', 'core'),
                    get_string('activities', 'local_nolej'),
                    false
                )
            );
        }
    }

    // Edit link, visible iff module is not failed.
    if ($module->status != module::STATUS_FAILED && $module->status != module::STATUS_CREATION) {
        $editurl = new moodle_url(
            '/local/nolej/edit.php',
            [
                'contextid' => $context->id,
                'documentid' => $module->document_id,
                'step' => $status2form[$module->status],
            ]
        );
        $menu->add(
            new action_menu_link(
                $editurl,
                new pix_icon('i/edit', 'core'),
                get_string('editmodule', 'local_nolej'),
                false
            )
        );
    }

    // Delete module link.
    $menu->add(
        new action_menu_link(
            new moodle_url('#'),
            new pix_icon('i/delete', 'core'),
            get_string('deletemodule', 'local_nolej'),
            false,
            [
                'data-action' => 'delete',
                'data-moduleid' => $module->document_id,
            ]
        )
    );

    $moduledata = [
        'moduleid' => $module->id,
        'title' => $module->title,
        'status' => module::getstatusname((int) $module->status),
        'documentid' => $module->document_id,
        'created' => userdate($module->tstamp),
        'lastupdate' => module::lastupdateof($module->document_id),
        'ispending' => module::isstatuspending($module->status),
        'iscompleted' => $module->status == module::STATUS_COMPLETED,
        'isfailed' => $module->status == module::STATUS_FAILED,
        'actions' => $OUTPUT->render($menu),
    ];

    $modulearray[] = $moduledata;
}

$createurl = new moodle_url('/local/nolej/edit.php', ['contextid' => $context->id]);
$templatecontext = (object) [
    'modules' => $modulearray,
    'createurl' => $createurl->out(false),
];

// Initialize polling.
$interval = max(1, (int) get_config('local_nolej', 'pollinginterval')) * 1000;
$PAGE->requires->js_call_amd('local_nolej/libraryupdate', 'init', [$interval]);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_nolej/manage', $templatecontext);
echo $OUTPUT->footer();
