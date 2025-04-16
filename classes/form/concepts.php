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
 * Copcepts edit form
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
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
 * Concepts edit form
 */
class concepts extends \moodleform {

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
        $mform->addElement('hidden', 'step')->setValue('concepts');
        $mform->setType('step', PARAM_ALPHA);

        // Download concepts.
        $result = api::getcontent(
            $documentid,
            'concepts',
            'concepts.json'
        );

        $json = api::readcontent($documentid, 'concepts.json');
        if (!$json) {
            redirect(
                new moodle_url('/local/nolej/library.php', ['contextid' => $contextid]),
                get_string('genericerror', 'local_nolej', ['error' => var_export($result, true)]),
                null,
                notification::NOTIFY_ERROR
            );
        }

        $concepts = json_decode($json);
        if (!isset($concepts->concepts)) {
            return;
        }

        $concepts = $concepts->concepts;
        if (!is_array($concepts)) {
            return;
        }

        // Sort concepts alphabetically.
        usort($concepts, function ($a, $b) {
            return strcmp(
                $a->concept->label,
                $b->concept->label
            );
        });

        for ($i = 0, $conceptscount = count($concepts); $i < $conceptscount; $i++) {
            $mform->addElement(
                'header',
                'concept_' . $concepts[$i]->id . '_header',
                $concepts[$i]->concept->label
            );

            // Concept label.
            $labelid = 'concept_' . $concepts[$i]->id . '_label';
            $mform->addElement(
                'text',
                $labelid,
                get_string('conceptlabel', 'local_nolej'),
                'style="width:100%;"'
            )->setValue($concepts[$i]->concept->label);
            $mform->setType($labelid, PARAM_TEXT);
            $mform->addRule($labelid, get_string('required'), 'required', null, 'server', false, false);

            // Concept enable.
            $enableid = 'concept_' . $concepts[$i]->id . '_enable';
            $mform->addElement(
                'selectyesno',
                $enableid,
                get_string('conceptenable', 'local_nolej')
            )->setValue($concepts[$i]->enable);

            // Concept definition.
            $definitionid = 'concept_' . $concepts[$i]->id . '_definition';
            $mform->addElement(
                'textarea',
                $definitionid,
                get_string('conceptdefinition', 'local_nolej'),
                'wrap="virtual" rows="3"'
            )->setValue($concepts[$i]->concept->definition);
            $mform->setType($definitionid, PARAM_TEXT);
            $mform->addRule($definitionid, get_string('required'), 'required', null, 'server', false, false);

            // Concept related games.
            $availablegames = $concepts[$i]->concept->available_games;
            if ($availablegames != null && is_array($availablegames) && count($availablegames) > 0) {
                $mform->addElement(
                    'selectyesno',
                    'concept_' . $concepts[$i]->id . '_use_for_gaming',
                    get_string('conceptuseforgaming', 'local_nolej')
                )->setValue($concepts[$i]->use_for_gaming);

                $games = [];

                // Crossword game.
                if (in_array('cw', $availablegames)) {
                    $games[] = &$mform->createElement(
                        'advcheckbox',
                        'use_for_cw',
                        get_string('conceptuseforcw', 'local_nolej')
                    );
                    $mform->setType('concept_' . $concepts[$i]->id . '_games[use_for_cw]', PARAM_BOOL);
                    $mform->setDefault('concept_' . $concepts[$i]->id . '_games[use_for_cw]', (bool) $concepts[$i]->use_for_cw);
                }

                // Drag the word game.
                if (in_array('dtw', $availablegames)) {
                    $games[] = &$mform->createElement(
                        'advcheckbox',
                        'use_for_dtw',
                        get_string('conceptusefordtw', 'local_nolej')
                    );
                    $mform->setType('concept_' . $concepts[$i]->id . '_games[use_for_dtw]', PARAM_BOOL);
                    $mform->setDefault('concept_' . $concepts[$i]->id . '_games[use_for_dtw]', (bool) $concepts[$i]->use_for_dtw);
                }

                // Find the word game.
                if (in_array('ftw', $availablegames)) {
                    $games[] = &$mform->createElement(
                        'advcheckbox',
                        'use_for_ftw',
                        get_string('conceptuseforftw', 'local_nolej')
                    );
                    $mform->setType('concept_' . $concepts[$i]->id . '_games[use_for_ftw]', PARAM_BOOL);
                    $mform->setDefault('concept_' . $concepts[$i]->id . '_games[use_for_ftw]', (bool) $concepts[$i]->use_for_ftw);
                }

                $mform->addGroup(
                    $games,
                    'concept_' . $concepts[$i]->id . '_games',
                    get_string('conceptuseingames', 'local_nolej'),
                    [' '],
                    true
                );

                $mform->hideIf(
                    'concept_' . $concepts[$i]->id . '_games',
                    'concept_' . $concepts[$i]->id . '_use_for_gaming',
                    'neq',
                    '1'
                );
            }

            // Use the concept for practice activity.
            $mform->addElement(
                'selectyesno',
                'concept_' . $concepts[$i]->id . '_use_for_practice',
                get_string('conceptuseforpractice', 'local_nolej')
            )->setValue($concepts[$i]->use_for_practice);
        }

        $this->add_action_buttons(true, get_string('save'));
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
