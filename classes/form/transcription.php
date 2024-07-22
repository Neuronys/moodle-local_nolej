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
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/local/nolej/classes/api.php');

/**
 * Transcription edit form
 */
class transcription extends \moodleform {

    /**
     * Form definition
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Context ID.
        $contextid = $this->_customdata['contextid'];
        $mform->addElement('hidden', 'contextid')->setValue($contextid);
        $mform->setType('contextid', PARAM_INT);

        // Document ID.
        $documentid = $this->_customdata['documentid'];
        $mform->addElement('hidden', 'documentid')->setValue($documentid);
        $mform->setType('documentid', PARAM_ALPHANUMEXT);

        // Step.
        $mform->addElement('hidden', 'step')->setValue('analysis');
        $mform->setType('step', PARAM_ALPHA);

        // Download transcription.
        $result = api::get(
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
                new moodle_url('/local/nolej/manage.php', [ 'contextid' => $contextid ]),
                get_string('genericerror', 'local_nolej', ['error' => var_export($result, true)]),
                null,
                notification::NOTIFY_ERROR
            );
        }

        // Document title.
        $mform->addElement('text', 'title', get_string('title', 'local_nolej'), 'style="width:100%;"');
        $mform->setType('title', PARAM_NOTAGS);
        $mform->setDefault('title', $result->title);

        // Download transcription.
        $transcription = file_get_contents($result->result);
        $success = api::writecontent(
            $documentid,
            'transcription.htm',
            $transcription
        );

        if (!$success) {
            redirect(
                new moodle_url('/local/nolej/manage.php', [ 'contextid' => $contextid ]),
                get_string('cannotwritetranscription', 'local_nolej'),
                null,
                notification::NOTIFY_ERROR
            );
        }

        // Transcription.
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

        $mform->addRule('title', get_string('required'), 'required', null, 'server', false, false);
        $mform->addRule('transcription', get_string('required'), 'required', null, 'server', false, false);

        // Use custom submit buttons.
        $buttonarray = [];
        $buttonarray[] = &$mform->createElement('submit', 'confirmanalysis', get_string('analyze', 'local_nolej'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array of errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check transcription limits.
        if (!isset($data['transcription']['text'])) {
            $errors['transcription'] = get_string('required');
        } else if (strlen($data['transcription']['text']) < 500) {
            $errors['transcription'] = get_string('limitmincharacters', 'local_nolej', 500);
        } else if (strlen($data['transcription']['text']) > 50000) {
            $errors['transcription'] = get_string('limitmaxcharacters', 'local_nolej', 50000);
        }

        return $errors;
    }
}
