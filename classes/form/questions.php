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
 * Questions edit form
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

class questions extends \moodleform
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
        $mform->addElement('hidden', 'step')->setValue('questions');
        $mform->setType('step', PARAM_ALPHA);

        // Download questions
        $result = \local_nolej\api\api::getcontent(
            $documentid,
            'questions',
            'questions.json'
        );

        $json = \local_nolej\api\api::readcontent($documentid, 'questions.json');
        if (!$json) {
            redirect(
                new \moodle_url('/local/nolej/manage.php'),
                get_string('genericerror', 'local_nolej', ['error' => print_r($result, true)]),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }

        $questions = json_decode($json);
        if (!isset($questions->questions)) {
            return;
        }

        $questions = $questions->questions;
        if (!is_array($questions)) {
            return;
        }

        // Sort questions by type
        $formquestions = [];
        for ($i = 0, $questionscount = count($questions); $i < $questionscount; $i++) {
            $questiontype = $questions[$i]->question_type;

            if (!isset($formquestions[$questiontype])) {
                $formquestions[$questiontype] = [];
            }

            // $questions[$i]->local_id = $i;
            $formquestions[$questiontype][] = $questions[$i];
        }

        ksort($formquestions);

        foreach ($formquestions as $questiontype => $questions) {

            $mform->addElement(
                'header',
                'questiontypeheader' . $questiontype,
                get_string('questiontype' . $questiontype, 'local_nolej') . ' (' . count($questions) . ')'
            );

            for ($i = 0, $questionscount = count($questions); $i < $questionscount; $i++) {

                // Open question card
                $mform->addElement(
                    'html',
                    sprintf(
                        '<div class="local_nolej_question"><div class="badge %s">%s</div>',
                        $questiontype,
                        get_string('questiontype' . $questiontype, 'local_nolej')
                    )
                );

                // $mform->addElement('hidden', 'question_' . $questions[$i]->id . '_id')->setValue($questions[$i]->id);
                // $mform->setType('question_' . $questions[$i]->id . '_id', PARAM_INT);

                if ($questiontype != 'tf') {
                    $mform->addElement(
                        'textarea',
                        'question_' . $questions[$i]->id . '_question',
                        get_string('question', 'local_nolej'),
                        'wrap="virtual" rows="2"'
                    )->setValue($questions[$i]->question);
                }

                $mform->addElement('hidden', 'question_' . $questions[$i]->id . '_type')->setValue($questions[$i]->question_type);
                $mform->setType('question_' . $questions[$i]->id . '_type', PARAM_ALPHA);

                $mform->addElement(
                    'selectyesno',
                    'question_' . $questions[$i]->id . '_enable',
                    get_string(
                        $questiontype == 'open' ? 'questionenable' : 'questionuseforgrading',
                        'local_nolej'
                    )
                )->setValue(
                        $questiontype == 'open'
                        ? $questions[$i]->enable
                        : $questions[$i]->use_for_grading
                    );

                if ($questiontype != 'hoq') {
                    $mform->addElement(
                        $questiontype == 'ftb' ? 'text' : 'textarea',
                        'question_' . $questions[$i]->id . '_answer',
                        get_string($questiontype == 'tf' ? 'questionanswertrue' : 'questionanswer', 'local_nolej'),
                        $questiontype == 'ftb' ? '' : 'wrap="virtual" rows="3"'
                    )->setValue($questions[$i]->answer);
                    $mform->setType('question_' . $questions[$i]->id . '_answer', PARAM_TEXT);
                }

                $distractorscount = count($questions[$i]->distractors);
                $mform->addElement('hidden', 'question_' . $questions[$i]->id . '_distractors')->setValue($distractorscount);
                $mform->setType('question_' . $questions[$i]->id . '_distractors', PARAM_INT);

                for ($j = 0; $j < $distractorscount; $j++) {
                    $mform->addElement(
                        'textarea',
                        'question_' . $questions[$i]->id . '_distractor_' . $j,
                        get_string($questiontype == 'tf' ? 'questionanswerfalse' : 'questiondistractor', 'local_nolej'),
                        'wrap="virtual" rows="3"'
                    )->setValue($questions[$i]->distractors[$j]);
                }

                if ($questiontype == 'tf') {
                    $mform->addElement(
                        'select',
                        'question_' . $questions[$i]->id . '_selected_distractor',
                        get_string('questionusedistractor', 'local_nolej'),
                        [
                            '' => get_string('questionanswertrue', 'local_nolej'),
                            'usefalse' => get_string('questionanswerfalse', 'local_nolej'),
                        ]
                    )->setValue(empty($questions[$i]->selected_distractor) ? '' : 'usefalse');
                }

                // Close question card
                $mform->addElement('html', '</div>');
            }
        }

        $this->add_action_buttons(true, get_string('savequestions', 'local_nolej'));
    }

    function validation($data, $files)
    {
        return [];
    }
}
