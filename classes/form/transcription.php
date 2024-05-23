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
 * Transcription edit form
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

class transcription extends \moodleform
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
        $mform->addElement('hidden', 'step')->setValue('analysis');
        $mform->setType('step', PARAM_ALPHA);

        // Download transcription
        $result = \local_nolej\api\api::get(
            sprintf('/documents/%s/transcription', $documentid)
        );

        if (
            !is_object($result) ||
            !property_exists($result, 'title') ||
            !is_string($result->title) ||
            !property_exists($result, 'result') ||
            !is_string($result->result)
        ) {
            redirect(
                new \moodle_url('/local/nolej/manage.php'),
                get_string('genericerror', 'local_nolej', ['error' => var_export($result, true)]),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }

        // Document title
        $mform->addElement('text', 'title', get_string('title', 'local_nolej'), 'style="width:100%;"');
        $mform->setType('title', PARAM_NOTAGS);
        $mform->setDefault('title', $result->title);

        // Download transcription
        $transcription = file_get_contents($result->result);
        $success = \local_nolej\api\api::writecontent(
            $documentid,
            'transcription.htm',
            $transcription
        );

        if (!$success) {
            redirect(
                new \moodle_url('/local/nolej/manage.php'),
                get_string('cannotwritetranscription', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }

        // Transcription
        $mform->addElement(
            'editor',
            'transcription',
            get_string('transcription', 'local_nolej'),
            null,
            [
                'subdirs' => 0,
                'maxbytes' => 0,
                'maxfiles' => 0,
                'changeformat' => 0,
                'context' => null,
                'noclean' => 0,
                'trusttext' => 0,
                'enable_filemanagement' => false,
            ]
        )->setValue(['text' => $transcription]);
        $mform->setType('transcription', PARAM_CLEANHTML);

        $mform->addRule('title', get_string('error'), 'required', null, 'server', false, false);
        $mform->addRule('transcription', get_string('error'), 'required', null, 'server', false, false);
        $mform->addRule('transcription', get_string('error'), 'maxlength', 50000, 'server', false, false);
        $mform->addRule('transcription', get_string('error'), 'minlength', 500, 'server', false, false);

        // Add custom submit buttons
        $buttonarray = [];
        $buttonarray[] = &$mform->createElement('submit', 'confirmanalysis', get_string('analyze', 'local_nolej'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }

    function validation($data, $files)
    {
        return [];
    }
}
