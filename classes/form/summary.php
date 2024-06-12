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
 * Summary edit form
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej\form;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use core\output\notification;
use local_nolej\api;

global $CFG;
require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->dirroot . '/local/nolej/classes/api.php');

/**
 * Summary edit form
 */
class summary extends \moodleform
{

    /**
     * Form definition
     */
    public function definition()
    {
        global $CFG;

        $mform = $this->_form;

        // Document ID.
        $documentid = $this->_customdata['documentid'];
        $mform->addElement('hidden', 'documentid')->setValue($documentid);
        $mform->setType('documentid', PARAM_ALPHANUMEXT);

        // Step.
        $mform->addElement('hidden', 'step')->setValue('summary');
        $mform->setType('step', PARAM_ALPHA);

        // Download summary.
        $result = api::getcontent(
            $documentid,
            'summary',
            'summary.json'
        );

        $json = api::readcontent($documentid, 'summary.json');
        if (!$json) {
            redirect(
                new moodle_url('/local/nolej/manage.php'),
                get_string('genericerror', 'local_nolej', ['error' => var_export($result, true)]),
                null,
                notification::NOTIFY_ERROR
            );
        }

        $summary = json_decode($json);

        if (!isset($summary->summary) || !is_array($summary->summary)) {
            return;
        }

        // Summary.
        $mform->addElement('header', 'summaryheader', get_string('summary', 'local_nolej'));

        $summarycount = count($summary->summary);
        $mform->addElement('hidden', 'summarycount')->setValue($summarycount);
        $mform->setType('summarycount', PARAM_INT);

        for ($i = 0; $i < $summarycount; $i++) {
            $titleid = 'summary_' . $i . '_title';
            $mform->addElement('text', $titleid, '', 'style="width:100%;"');
            $mform->setType($titleid, PARAM_TEXT);
            $mform->setDefault($titleid, $summary->summary[$i]->title);
            $mform->addRule($titleid, get_string('required'), 'required', null, 'server', false, false);

            $textid = 'summary_' . $i . '_text';
            $mform->addElement('textarea', $textid, '', 'wrap="virtual" rows="6"');
            $mform->setType($textid, PARAM_TEXT);
            $mform->setDefault($textid, $summary->summary[$i]->text);
            $mform->addRule($textid, get_string('required'), 'required', null, 'server', false, false);
        }

        // Abstract.
        if ($summarycount > 1) {
            $mform->addElement('header', 'abstractheader', get_string('abstract', 'local_nolej'));
            $mform->addElement('textarea', "abstract", '', 'wrap="virtual" rows="15"');
            $mform->setType("abstract", PARAM_TEXT);
            $mform->setDefault('abstract', $summary->abstract);
            $mform->addRule('abstract', get_string('required'), 'required', null, 'server', false, false);
        }

        // Keypoints.
        $mform->addElement('header', 'keypointsheader', get_string('keypoints', 'local_nolej'));

        $keypointscount = count($summary->keypoints);
        $mform->addElement('hidden', 'keypointscount')->setValue($keypointscount);
        $mform->setType('keypointscount', PARAM_INT);

        for ($i = 0; $i < $keypointscount; $i++) {
            $keypointid = 'keypoints_' . $i;
            $mform->addElement('textarea', $keypointid, '', 'wrap="virtual" rows="2"');
            $mform->setType($keypointid, PARAM_TEXT);
            $mform->setDefault($keypointid, $summary->keypoints[$i]);
            $mform->addRule($keypointid, get_string('required'), 'required', null, 'server', false, false);
        }

        $this->add_action_buttons(true, get_string('savesummary', 'local_nolej'));
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array of errors
     */
    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
