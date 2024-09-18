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
 * Activities generation form
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
 * Activities generation form
 */
class activities extends \moodleform {

    /**
     * Form definition
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        \MoodleQuickForm::registerElementType(
            'range',
            $CFG->dirroot . '/local/nolej/classes/form/element/range_form_element.php',
            'range_form_element'
        );

        // Context ID.
        $contextid = $this->_customdata['contextid'];
        $mform->addElement('hidden', 'contextid')->setValue($contextid);
        $mform->setType('contextid', PARAM_INT);

        // Document ID.
        $documentid = $this->_customdata['documentid'];
        $mform->addElement('hidden', 'documentid')->setValue($documentid);
        $mform->setType('documentid', PARAM_ALPHANUMEXT);

        // Step.
        $mform->addElement('hidden', 'step')->setValue('activities');
        $mform->setType('step', PARAM_ALPHA);

        // Download activities settings.
        $result = api::getcontent(
            $documentid,
            'settings',
            'settings.json',
            true
        );

        $json = api::readcontent($documentid, 'settings.json');
        if (!$json) {
            redirect(
                new moodle_url('/local/nolej/manage.php', ['contextid' => $contextid]),
                get_string('genericerror', 'local_nolej', ['error' => var_export($result, true)]),
                null,
                notification::NOTIFY_ERROR
            );
        }

        $settings = json_decode($json);
        $availableactivities = $settings->avaible_packages ?? [];

        if (!is_object($settings->settings)) {
            return;
        }

        $settings = $settings->settings;

        // Sort activities alphabetically.
        usort($availableactivities, function ($a, $b) {
            return strcmp(
                get_string('activities' . $a, 'local_nolej'),
                get_string('activities' . $b, 'local_nolej')
            );
        });

        for ($i = 0, $len = count($availableactivities); $i < $len; $i++) {
            $mform->addElement(
                'header',
                'activityheader_' . $availableactivities[$i],
                get_string('activities' . $availableactivities[$i], 'local_nolej')
            );

            $mform->addElement(
                'selectyesno',
                'activity_' . $availableactivities[$i],
                get_string('activitiesenable', 'local_nolej', get_string('activities' . $availableactivities[$i], 'local_nolej'))
            );
            $mform->setType('activity_' . $availableactivities[$i], PARAM_BOOL);
            $mform->setDefault('activity_' . $availableactivities[$i], true);

            switch ($availableactivities[$i]) {
                case 'ibook':
                    // Nothing to add.
                    break;

                case 'glossary':
                    $mform->addElement(
                        'selectyesno',
                        'Glossary_include_IB',
                        get_string('activitiesuseinibook', 'local_nolej')
                    );
                    $mform->setDefault('Glossary_include_IB', $settings->Glossary_include_IB);
                    $mform->hideIf('Glossary_include_IB', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "summary":
                    $mform->addElement(
                        'selectyesno',
                        'Summary_include_IB',
                        get_string('activitiesuseinibook', 'local_nolej')
                    );
                    $mform->setDefault('Summary_include_IB', $settings->Summary_include_IB);
                    $mform->hideIf('Summary_include_IB', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "findtheword":
                    $mform->addElement(
                        'range',
                        'FTW_number_word_current',
                        get_string('activitiesftwwords', 'local_nolej'),
                        ['min' => 3, 'max' => $settings->FTW_number_word_max]
                    );
                    $mform->setType('FTW_number_word_current', PARAM_INT);
                    $mform->setDefault('FTW_number_word_current', $settings->FTW_number_word_current);
                    $mform->hideIf('FTW_number_word_current', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "dragtheword":
                    $mform->addElement(
                        'selectyesno',
                        'DTW_include_IB',
                        get_string('activitiesuseinibook', 'local_nolej')
                    );
                    $mform->setDefault('DTW_include_IB', $settings->DTW_include_IB);
                    $mform->hideIf('DTW_include_IB', 'activity_' . $availableactivities[$i], 'neq', '1');

                    $mform->addElement(
                        'range',
                        'DTW_number_word_current',
                        get_string('activitiesdtwwords', 'local_nolej'),
                        ['min' => 3, 'max' => $settings->DTW_number_word_max]
                    );
                    $mform->setType('DTW_number_word_current', PARAM_INT);
                    $mform->setDefault('DTW_number_word_current', $settings->DTW_number_word_current);
                    $mform->hideIf('DTW_number_word_current', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "crossword":
                    $mform->addElement(
                        'range',
                        'CW_number_word_current',
                        get_string('activitiescwwords', 'local_nolej'),
                        ['min' => 3, 'max' => $settings->CW_number_word_max]
                    );
                    $mform->setType('CW_number_word_current', PARAM_INT);
                    $mform->setDefault('CW_number_word_current', $settings->CW_number_word_current);
                    $mform->hideIf('CW_number_word_current', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "practice":
                    $mform->addElement(
                        'selectyesno',
                        'Practice_include_IB',
                        get_string('activitiesuseinibook', 'local_nolej')
                    );
                    $mform->setDefault('Practice_include_IB', $settings->Practice_include_IB);
                    $mform->hideIf('Practice_include_IB', 'activity_' . $availableactivities[$i], 'neq', '1');

                    $mform->addElement(
                        'range',
                        'Practice_number_flashcard_current',
                        get_string('activitiespracticeflashcards', 'local_nolej'),
                        ['min' => 0, 'max' => $settings->Practice_number_flashcard_max]
                    );
                    $mform->setType('Practice_number_flashcard_current', PARAM_INT);
                    $mform->setDefault('Practice_number_flashcard_current', $settings->Practice_number_flashcard_current);
                    $mform->hideIf('Practice_number_flashcard_current', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "practiceq":
                    $mform->addElement(
                        'selectyesno',
                        'PracticeQ_include_IB',
                        get_string('activitiesuseinibook', 'local_nolej')
                    );
                    $mform->setDefault('PracticeQ_include_IB', $settings->PracticeQ_include_IB);
                    $mform->hideIf('PracticeQ_include_IB', 'activity_' . $availableactivities[$i], 'neq', '1');

                    $mform->addElement(
                        'range',
                        'PracticeQ_number_flashcard_current',
                        get_string('activitiespracticeqflashcards', 'local_nolej'),
                        ['min' => 0, 'max' => $settings->PracticeQ_number_flashcard_max]
                    );
                    $mform->setType('PracticeQ_number_flashcard_current', PARAM_INT);
                    $mform->setDefault('PracticeQ_number_flashcard_current', $settings->PracticeQ_number_flashcard_current);
                    $mform->hideIf('PracticeQ_number_flashcard_current', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "grade":
                    $mform->addElement(
                        'selectyesno',
                        'Grade_include_IB',
                        get_string('activitiesuseinibook', 'local_nolej')
                    );
                    $mform->setDefault('Grade_include_IB', $settings->Grade_include_IB);
                    $mform->hideIf('Grade_include_IB', 'activity_' . $availableactivities[$i], 'neq', '1');

                    $mform->addElement(
                        'range',
                        'Grade_number_question_current',
                        get_string('activitiesgradequestions', 'local_nolej'),
                        ['min' => 0, 'max' => $settings->Grade_number_question_max]
                    );
                    $mform->setType('Grade_number_question_current', PARAM_INT);
                    $mform->setDefault('Grade_number_question_current', $settings->Grade_number_question_current);
                    $mform->hideIf('Grade_number_question_current', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "gradeq":
                    $mform->addElement(
                        'selectyesno',
                        'GradeQ_include_IB',
                        get_string('activitiesuseinibook', 'local_nolej')
                    );
                    $mform->setDefault('GradeQ_include_IB', $settings->GradeQ_include_IB);
                    $mform->hideIf('GradeQ_include_IB', 'activity_' . $availableactivities[$i], 'neq', '1');

                    $mform->addElement(
                        'range',
                        'GradeQ_number_question_current',
                        get_string('activitiesgradeqquestions', 'local_nolej'),
                        ['min' => 0, 'max' => $settings->GradeQ_number_question_max]
                    );
                    $mform->setType('GradeQ_number_question_current', PARAM_INT);
                    $mform->setDefault('GradeQ_number_question_current', $settings->GradeQ_number_question_current);
                    $mform->hideIf('GradeQ_number_question_current', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "flashcards":
                    $mform->addElement(
                        'range',
                        'Flashcards_number_flashcard_current',
                        get_string('activitiesflashcardsflashcards', 'local_nolej'),
                        ['min' => 0, 'max' => $settings->Flashcards_number_flashcard_max]
                    );
                    $mform->setType('Flashcards_number_flashcard_current', PARAM_INT);
                    $mform->setDefault('Flashcards_number_flashcard_current', $settings->Flashcards_number_flashcard_current);
                    $mform->hideIf('Flashcards_number_flashcard_current', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;

                case "ivideo":
                    $mform->addElement(
                        'range',
                        'IV_number_question_perset_current',
                        get_string('activitiesivideoquestions', 'local_nolej'),
                        ['min' => 0, 'max' => $settings->IV_number_question_perset_max]
                    );
                    $mform->setType('IV_number_question_perset_current', PARAM_INT);
                    $mform->setDefault('IV_number_question_perset_current', $settings->IV_number_question_perset_current);
                    $mform->hideIf('IV_number_question_perset_current', 'activity_' . $availableactivities[$i], 'neq', '1');
                    break;
            }
        }

        $this->add_action_buttons(true, get_string('generate', 'local_nolej'));
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
        return $errors;
    }
}
