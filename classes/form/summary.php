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
 * @author      2024 Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej\form;

defined('MOODLE_INTERNAL') || die();

require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->dirroot . '/local/nolej/classes/api.php');

/**
 * Summary edit form
 */
class summary extends \moodleform
{

    public function definition()
    {
        global $CFG;

        $mform = $this->_form;

        // Document ID
        $documentid = $this->_customdata['documentid'];
        $mform->addElement('hidden', 'documentid')->setValue($documentid);
        $mform->setType('documentid', PARAM_ALPHANUMEXT);

        // Step
        $mform->addElement('hidden', 'step')->setValue('summary');
        $mform->setType('step', PARAM_ALPHA);

        // Download summary
        $result = \local_nolej\api\api::getcontent(
            $documentid,
            'summary',
            'summary.json'
        );

        $json = \local_nolej\api\api::readcontent($documentid, 'summary.json');
        if (!$json) {
            redirect(
                new \moodle_url('/local/nolej/manage.php'),
                get_string('genericerror', 'local_nolej', ['error' => var_export($result, true)]),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }

        $summary = json_decode($json);

        if (!isset($summary->summary) || !is_array($summary->summary)) {
            return;
        }

        // Summary
        $mform->addElement('header', 'summaryheader', get_string('summary', 'local_nolej'));

        $summarycount = count($summary->summary);
        $mform->addElement('hidden', 'summarycount')->setValue($summarycount);
        $mform->setType('summarycount', PARAM_INT);

        for ($i = 0; $i < $summarycount; $i++) {
            $mform->addElement('text', 'summary_' . $i . '_title', '', 'style="width:100%;"');
            $mform->setType('summary_' . $i . '_title', PARAM_TEXT);
            $mform->setDefault('summary_' . $i . '_title', $summary->summary[$i]->title);

            $mform->addElement('textarea', 'summary_' . $i . '_text', '', 'wrap="virtual" rows="6"');
            $mform->setType('summary_' . $i . '_text', PARAM_TEXT);
            $mform->setDefault('summary_' . $i . '_text', $summary->summary[$i]->text);
        }

        // Abstract
        if ($summarycount > 1) {
            $mform->addElement('header', 'abstractheader', get_string('abstract', 'local_nolej'));
            $mform->addElement('textarea', "abstract", '', 'wrap="virtual" rows="15"');
            $mform->setType("abstract", PARAM_TEXT);
            $mform->setDefault("abstract", $summary->abstract);
        }

        // Keypoints
        $mform->addElement('header', 'keypointsheader', get_string('keypoints', 'local_nolej'));

        $keypointscount = count($summary->keypoints);
        $mform->addElement('hidden', 'keypointscount')->setValue($keypointscount);
        $mform->setType('keypointscount', PARAM_INT);

        for ($i = 0; $i < $keypointscount; $i++) {
            $mform->addElement('textarea', 'keypoints_' . $i, '', 'wrap="virtual" rows="2"');
            $mform->setType('keypoints_' . $i, PARAM_TEXT);
            $mform->setDefault('keypoints_' . $i, $summary->keypoints[$i]);
        }

        $this->add_action_buttons(true, get_string('savesummary', 'local_nolej'));
    }

    public function validation($data, $files)
    {
        return [];
    }
}
