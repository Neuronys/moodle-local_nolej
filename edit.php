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


if (
    empty($documentid) ||
    \local_nolej\api\api::lookupdocumentstatus($documentid, $USER->id) <= \local_nolej\api\api::STATUS_CREATION
) {
    $PAGE->set_heading(get_string('status_0', 'local_nolej'));
    $PAGE->set_title(get_string('status_0', 'local_nolej'));
    handlecreation();
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

    $PAGE->set_heading(get_string('status_' . $document->status, 'local_nolej'));
    $PAGE->set_title(empty($document->title) ? get_string('status_0', 'local_nolej') : $document->title);

    switch ($document->status) {
        case \local_nolej\api\api::STATUS_CREATION_PENDING:
            \core\notification::add(get_string('status_' . $document->status, 'local_nolej'), \core\output\notification::NOTIFY_INFO);
            // TODO: display document data
            echo $OUTPUT->header();
            printdocumentinfo();
            echo $OUTPUT->footer();
            return;

        case \local_nolej\api\api::STATUS_ANALYSIS_PENDING:
            \core\notification::add(get_string('status_' . $document->status, 'local_nolej'), \core\output\notification::NOTIFY_INFO);
            // TODO: display document data and transcription
            echo $OUTPUT->header();
            printdocumentinfo();
            echo $OUTPUT->footer();
            return;

        case \local_nolej\api\api::STATUS_CREATION_PENDING:
            \core\notification::add(get_string('status_' . $document->status, 'local_nolej'), \core\output\notification::NOTIFY_INFO);
            // TODO: display document data, transcription and review/settings
            echo $OUTPUT->header();
            printdocumentinfo();
            echo $OUTPUT->footer();
            return;
    }

    switch ($step) {
        case 'analysis':
            handleanalysis();
            break;

        case 'summary':
            handlesummary();
            break;

        case 'questions':
            handlequestions();
            break;

        case 'concepts':
            handleconcepts();
            break;

        case 'activities':
            handleactivities();
            break;

        default:
            if ($document->status < \local_nolej\api\api::STATUS_REVISION) {
                $step = 'analysis';
                handleanalysis();
            } else {
                $step = 'concepts';
                handleconcepts();
            }
    }
}

/**
 * Forms
 */

/**
 * Handle creation form
 */
function handlecreation()
{
    global $OUTPUT, $DB, $USER, $SITE, $context;

    // Display and handle creation form
    $mform = new \local_nolej\form\creation();

    if ($mform->is_cancelled()) {
        // Cancelled
        redirect(
            new \moodle_url('/local/nolej/manage.php'),
            get_string('modulenotcreated', 'local_nolej'),
            null,
            \core\output\notification::NOTIFY_INFO
        );
    } else if ($fromform = $mform->get_data()) {
        // Submitted and validated
        $success = true;

        $title = $fromform->title;
        $sourcetype = $fromform->sourcetype;
        $language = $fromform->language;
        $url = '';
        $format = '';
        $consumedcredit = 1;
        $automaticmode = false;

        switch ($sourcetype) {
            case 'web':
                $url = $fromform->sourceurl;
                $format = $fromform->sourceurltype;
                break;

            case 'file':
                $uploaddir = \local_nolej\api\api::uploaddir();

                $filename = $mform->get_new_filename('sourcefile');
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $format = \local_nolej\api\api::formatfromextension($extension);
                if (empty($format)) {
                    break;
                }
                $filename = $USER->id . '.' . randomstring() . '.' . $extension;
                $filename = uniquefilename($filename, $uploaddir . '/');
                $dest = $uploaddir . '/' . $filename;
                $success = $mform->save_file('sourcefile', $dest);
                if ($success) {
                    chmod($dest, 0775);
                    $webhook = new \moodle_url(
                        '/local/nolej/webhook.php',
                        [
                            'fileid' => $filename
                        ]
                    );
                    $url = $webhook->out(false);
                }
                break;

            case 'text':
                $uploaddir = \local_nolej\api\api::uploaddir();

                $filename = $USER->id . '.' . randomstring() . '.htm';
                $filename = uniquefilename($filename, $uploaddir . '/');
                $dest = $uploaddir . '/' . $filename;
                $success = file_put_contents($dest, $fromform->sourcetext);
                if ($success) {
                    chmod($dest, 0775);
                    $webhook = new \moodle_url(
                        '/local/nolej/webhook.php',
                        [
                            'fileid' => $filename
                        ]
                    );
                    $url = $webhook->out(false);
                    $format = 'freetext';
                }
                break;
        }

        if (!empty($url) && !empty($format)) {
            // Call Nolej creation API
            $webhook = new \moodle_url('/local/nolej/webhook.php');

            $result = \local_nolej\api\api::post(
                '/documents',
                [
                    'userID' => (int) $USER->id,
                    'organisationID' => format_string($SITE->fullname, true, ['context' => $context]),
                    'title' => $title,
                    'decrementedCredit' => $consumedcredit,
                    'docURL' => $url,
                    'webhookURL' => $webhook->out(false),
                    'mediaType' => $format,
                    'automaticMode' => $automaticmode,
                    'language' => $language
                ],
                true
            );

            if (!is_object($result) || !property_exists($result, 'id') || !is_string($result->id)) {

                // An error occurred
                \core\notification::add(
                    get_string('errdocument', 'local_nolej', print_r($result, true)),
                    core\output\notification::NOTIFY_ERROR
                );

                if (
                    ($sourcetype == 'file' || $sourcetype == 'text') &&
                    isset($dest) &&
                    !empty($dest) &&
                    is_file($dest)
                ) {
                    // Remove the uploaded file
                    unlink($dest);
                }
            } else {
                $DB->insert_record(
                    'nolej_module',
                    (object) [
                        'document_id' => $result->id,
                        'user_id' => $USER->id,
                        'tstamp' => time(),
                        'status' => \local_nolej\api\api::STATUS_CREATION_PENDING,
                        'title' => $title,
                        'consumed_credit' => $consumedcredit,
                        'doc_url' => $url,
                        'media_type' => $format,
                        'automatic_mode' => $automaticmode,
                        'language' => $language
                    ],
                    false
                );

                $DB->insert_record(
                    'nolej_activity',
                    (object) [
                        'document_id' => $result->id,
                        'user_id' => $USER->id,
                        'action' => 'transcription',
                        'tstamp' => time(),
                        'status' => 'ok',
                        'code' => 200,
                        'error_message' => '',
                        'consumed_credit' => $consumedcredit,
                        'notified' => false
                    ],
                    false
                );

                redirect(
                    new \moodle_url('/local/nolej/manage.php'),
                    get_string('modulecreated', 'local_nolej'),
                    null,
                    \core\output\notification::NOTIFY_SUCCESS
                );
            }
        } else {
            \core\notification::add('Some data missing', \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        // Display form
    }

    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->render_from_template('local_nolej/creation', null);
    echo $OUTPUT->footer();
}

/**
 * Handle analysis form
 */
function handleanalysis()
{
    global $OUTPUT, $DB, $USER, $PAGE, $document, $documentid;

    // Display and handle creation form
    $mform = new \local_nolej\form\transcription(
        (
            new \moodle_url(
                '/local/nolej/edit.php',
                [
                    'documentid' => $documentid,
                    'step' => 'analysis'
                ]
            )
        )->out(false),
        ['documentid' => $documentid]
    );

    if ($mform->is_cancelled()) {
        // Cancelled
        redirect(new \moodle_url('/local/nolej/manage.php'));
    } else if ($fromform = $mform->get_data()) {
        // Submitted and validated

        $title = $fromform->title;
        $transcription = $fromform->transcription;

        if (!empty($transcription)) {
            \local_nolej\api\api::writecontent($documentid, 'transcription.htm', $transcription);

            // Call Nolej analysis API
            $webhook = (
                new \moodle_url(
                    '/local/nolej/webhook.php',
                    [
                        'documentid' => $documentid,
                        'fileid' => 'transcription.htm'
                    ]
                )
            )->out(false);

            $result = \local_nolej\api\api::put(
                "/documents/$documentid/transcription",
                [
                    's3URL' => $webhook,
                    'automaticMode' => false
                ],
                true,
                true
            );

            if (
                !is_object($result) ||
                !property_exists($result, 'result') ||
                !is_string($result->result) ||
                !(
                    $result->result == 'ok' ||
                    $result->result == '"ok"'
                )
            ) {
                redirect(
                    new \moodle_url('/local/nolej/manage.php'),
                    get_string('genericerror', 'local_nolej', (object) ['error' => print_r($result, true)]),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
            }

            $DB->update_record(
                'nolej_module',
                (object) [
                    'id' => $document->id,
                    'document_id' => $documentid,
                    'status' => \local_nolej\api\api::STATUS_ANALYSIS_PENDING,
                    'title' => $title
                ]
            );

            $DB->insert_record(
                'nolej_activity',
                (object) [
                    'document_id' => $documentid,
                    'user_id' => $USER->id,
                    'action' => 'transcription',
                    'tstamp' => time(),
                    'status' => 'ok',
                    'code' => 200,
                    'error_message' => '',
                    'consumed_credit' => 0,
                    'notified' => true
                ],
                false
            );

            redirect(
                new \moodle_url('/local/nolej/manage.php'),
                get_string('analysisstart', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        } else {
            redirect(
                (
                    new \moodle_url(
                        '/local/nolej/edit.php',
                        [
                            'documentid' => $documentid,
                            'step' => 'analysis'
                        ]
                    )
                )->out(false),
                get_string('missingtranscription', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    } else {
        $PAGE->requires->js_call_amd('local_nolej/confirmanalysis');
        // Data not valid, display the form.
    }

    echo $OUTPUT->header();
    printdocumentinfo();
    $mform->display();
    echo $OUTPUT->footer();
}

/**
 * Handle review summary form
 */
function handlesummary()
{
    global $OUTPUT, $DB, $USER, $document, $documentid;

    // Display and handle creation form
    $mform = new \local_nolej\form\summary(
        (
            new \moodle_url(
                '/local/nolej/edit.php',
                [
                    'documentid' => $documentid,
                    'step' => 'summary'
                ]
            )
        )->out(false),
        ['documentid' => $documentid]
    );

    if ($mform->is_cancelled()) {
        // Cancelled
        redirect(new \moodle_url('/local/nolej/manage.php'));
    } else if ($fromform = $mform->get_data()) {
        // Submitted and validated

        $summary = [
            'summary' => [],
            'abstract' => '',
            'keypoints' => []
        ];

        $summarycount = $fromform->summarycount;
        for ($i = 0; $i < $summarycount; $i++) {
            $title = $fromform->{'summary_' . $i . '_title'};
            $txt = $fromform->{'summary_' . $i . '_text'};
            if (!empty($title) && !empty($txt)) {
                $summary['summary'][] = [
                    'title' => $title,
                    'text' => $txt
                ];
            }
        }

        $summary['abstract'] = $summarycount > 1
            ? $fromform->abstract
            : '';

        $keypointscount = $fromform->keypointscount;
        for ($i = 0; $i < $keypointscount; $i++) {
            $txt = $fromform->{'keypoints_' . $i};
            if (!empty($txt)) {
                $summary['keypoints'][] = $txt;
            }
        }

        $success = \local_nolej\api\api::writecontent($documentid, 'summary.json', json_encode($summary));
        if (!$success) {
            redirect(
                (
                    new \moodle_url(
                        '/local/nolej/edit.php',
                        [
                            'documentid' => $documentid,
                            'step' => 'summary'
                        ]
                    )
                )->out(false),
                get_string('cannotwritesummary', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
            return;
        }

        $success = \local_nolej\api\api::putcontent($documentid, 'summary', 'summary.json');
        redirect(
            (
                new \moodle_url(
                    '/local/nolej/edit.php',
                    [
                        'documentid' => $documentid,
                        'step' => 'summary'
                    ]
                )
            )->out(false),
            get_string($success ? 'summarysaved' : 'summarynotsaved', 'local_nolej'),
            null,
            $success ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR
        );
    } else {
        // Data not valid, display the form.
    }

    echo $OUTPUT->header();
    printdocumentinfo();
    $mform->display();
    echo $OUTPUT->footer();
}

/**
 * Handle review concepts form
 */
function handleconcepts()
{
    global $OUTPUT, $DB, $USER, $document, $documentid;

    // Display and handle creation form
    $mform = new \local_nolej\form\concepts(
        (
            new \moodle_url(
                '/local/nolej/edit.php',
                [
                    'documentid' => $documentid,
                    'step' => 'concepts'
                ]
            )
        )->out(false),
        ['documentid' => $documentid]
    );

    if ($mform->is_cancelled()) {
        // Cancelled
        redirect(new \moodle_url('/local/nolej/manage.php'));
    } else if ($fromform = $mform->get_data()) {
        // Submitted and validated

        // Download concepts
        $result = \local_nolej\api\api::getcontent(
            $documentid,
            'concepts',
            'concepts.json'
        );

        $json = \local_nolej\api\api::readcontent($documentid, 'concepts.json');
        if (!$json) {
            redirect(
                new \moodle_url('/local/nolej/manage.php'),
                get_string('genericerror', 'local_nolej', ['error' => print_r($result, true)]),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }

        $concepts = json_decode($json);
        $concepts = $concepts->concepts;

        for ($i = 0, $conceptscount = count($concepts); $i < $conceptscount; $i++) {
            $id = $concepts[$i]->id;

            $concepts[$i]->concept->label = $fromform->{'concept_' . $id . '_label'};
            $concepts[$i]->enable = (bool) $fromform->{'concept_' . $id . '_enable'};
            $concepts[$i]->concept->definition = $fromform->{'concept_' . $id . '_definition'};

            $availablegames = $concepts[$i]->concept->available_games;
            if ($availablegames != null && is_array($availablegames) && count($availablegames) > 0) {
                $concepts[$i]->use_for_gaming = (bool) $fromform->{'concept_' . $id . '_use_for_gaming'};

                $games = $fromform->{'concept_' . $id . '_games'};

                if (in_array('cw', $availablegames)) {
                    $concepts[$i]->use_for_cw = isset($games['use_for_cw']) && $games['use_for_cw'];
                }

                if (in_array('dtw', $availablegames)) {
                    $concepts[$i]->use_for_dtw = isset($games['use_for_dtw']) && $games['use_for_dtw'];
                }

                if (in_array('ftw', $availablegames)) {
                    $concepts[$i]->use_for_ftw = isset($games['use_for_ftw']) && $games['use_for_ftw'];
                }
            }

            $concepts[$i]->use_for_practice = (bool) $fromform->{'concept_' . $id . '_use_for_practice'};
        }

        $success = \local_nolej\api\api::writecontent($documentid, 'concepts.json', json_encode(['concepts' => $concepts]));
        if (!$success) {
            redirect(
                (
                    new \moodle_url(
                        '/local/nolej/edit.php',
                        [
                            'documentid' => $documentid,
                            'step' => 'concepts'
                        ]
                    )
                )->out(false),
                get_string('cannotwriteconcepts', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
            return;
        }

        $success = \local_nolej\api\api::putcontent($documentid, 'concepts', 'concepts.json');
        redirect(
            (
                new \moodle_url(
                    '/local/nolej/edit.php',
                    [
                        'documentid' => $documentid,
                        'step' => 'concepts'
                    ]
                )
            )->out(false),
            get_string($success ? 'conceptssaved' : 'conceptsnotsaved', 'local_nolej'),
            null,
            $success ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR
        );
    } else {
        // Data not valid, display the form.
    }

    echo $OUTPUT->header();
    printdocumentinfo();
    $mform->display();
    echo $OUTPUT->footer();
}

/**
 * Handle review questions form
 */
function handlequestions()
{
    global $OUTPUT, $DB, $USER, $document, $documentid;

    // Display and handle creation form
    $mform = new \local_nolej\form\questions(
        (
            new \moodle_url(
                '/local/nolej/edit.php',
                [
                    'documentid' => $documentid,
                    'step' => 'questions'
                ]
            )
        )->out(false),
        ['documentid' => $documentid]
    );

    if ($mform->is_cancelled()) {
        // Cancelled
        redirect(new \moodle_url('/local/nolej/manage.php'));
    } else if ($fromform = $mform->get_data()) {
        // Submitted and validated

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
        $questions = $questions->questions;

        for ($i = 0, $questionscount = count($questions); $i < $questionscount; $i++) {
            $id = $questions[$i]->id;
            $questiontype = $questions[$i]->question_type;

            if ($questiontype == 'open') {
                $questions[$i]->enable = (bool) $fromform->{'question_' . $id . '_enable'};
            } else {
                $questions[$i]->use_for_grading = (bool) $fromform->{'question_' . $id . '_enable'};
            }

            if ($questiontype != 'hoq') {
                $questions[$i]->answer = $fromform->{'question_' . $id . '_answer'};
            }

            if ($questiontype == 'tf') {
                $questions[$i]->selected_distractor = $fromform->{'question_' . $id . '_selected_distractor'};
            } else {
                $questions[$i]->question = $fromform->{'question_' . $id . '_question'};
            }

            $distractors = [];
            for ($j = 0, $distractorscount = $fromform->{'question_' . $id . '_distractors'}; $j < $distractorscount; $j++) {
                $distractor = $fromform->{'question_' . $id . '_distractor_' . $j};
                if (!empty($distractor)) {
                    $distractors[] = $distractor;
                }
            }
            $questions[$i]->distractors = $distractors;
        }

        $success = \local_nolej\api\api::writecontent($documentid, 'questions.json', json_encode(['questions' => $questions]));
        if (!$success) {
            redirect(
                (
                    new \moodle_url(
                        '/local/nolej/edit.php',
                        [
                            'documentid' => $documentid,
                            'step' => 'questions'
                        ]
                    )
                )->out(false),
                get_string('cannotwritequestions', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
            return;
        }

        $success = \local_nolej\api\api::putcontent($documentid, 'questions', 'questions.json');
        redirect(
            (
                new \moodle_url(
                    '/local/nolej/edit.php',
                    [
                        'documentid' => $documentid,
                        'step' => 'questions'
                    ]
                )
            )->out(false),
            get_string($success ? 'questionssaved' : 'questionsnotsaved', 'local_nolej'),
            null,
            $success ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR
        );
    } else {
        // Data not valid, display the form.
    }

    echo $OUTPUT->header();
    printdocumentinfo();
    $mform->display();
    echo $OUTPUT->footer();
}

/**
 * Handle activities form
 */
function handleactivities()
{
    global $OUTPUT, $DB, $USER, $document, $documentid;

    // Display and handle creation form
    $mform = new \local_nolej\form\activities(
        (
            new \moodle_url(
                '/local/nolej/edit.php',
                [
                    'documentid' => $documentid,
                    'step' => 'activities'
                ]
            )
        )->out(false),
        ['documentid' => $documentid]
    );

    if ($mform->is_cancelled()) {
        // Cancelled
        redirect(new \moodle_url('/local/nolej/manage.php'));
    } else if ($fromform = $mform->get_data()) {
        // Submitted and validated

        // Download settings
        $result = \local_nolej\api\api::getcontent(
            $documentid,
            'settings',
            'settings.json'
        );

        $json = \local_nolej\api\api::readcontent($documentid, 'settings.json');
        if (!$json) {
            redirect(
                new \moodle_url('/local/nolej/manage.php'),
                get_string('genericerror', 'local_nolej', ['error' => print_r($result, true)]),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }

        $settings = json_decode($json, true);
        $availableactivities = $settings['avaible_packages'] ?? [];

        $settingstosave = [
            'settings' => $settings['settings'],
            'avaible_packages' => $availableactivities,
            'desired_packages' => []
        ];

        for ($i = 0, $len = count($availableactivities); $i < $len; $i++) {
            $useactivity = (bool) $fromform->{'activity_' . $availableactivities[$i]};
            if (!$useactivity) {
                // Activity not to be generated, skip related settings
                continue;
            }

            $settingstosave['desired_packages'][] = $availableactivities[$i];

            switch ($availableactivities[$i]) {
                case 'glossary':
                    $ibook = (bool) $fromform->Glossary_include_IB;
                    $settingstosave['settings']['Glossary_include_IB'] = $ibook;
                    break;

                case 'summary':
                    $ibook = (bool) $fromform->Summary_include_IB;
                    $settingstosave['settings']['Summary_include_IB'] = $ibook;
                    break;

                case 'findtheword':
                    $number = (int) $fromform->FTW_number_word_current;
                    $settingstosave['settings']['FTW_number_word_current'] = $number;
                    break;

                case 'dragtheword':
                    $ibook = (bool) $fromform->DTW_include_IB;
                    $settingstosave['settings']['DTW_include_IB'] = $ibook;
                    $number = (int) $fromform->DTW_number_word_current;
                    $settingstosave['settings']['DTW_number_word_current'] = $number;
                    break;

                case 'crossword':
                    $number = (int) $fromform->CW_number_word_current;
                    $settingstosave['settings']['CW_number_word_current'] = $number;
                    break;

                case 'practice':
                    $ibook = (bool) $fromform->Practice_include_IB;
                    $settingstosave['settings']['Practice_include_IB'] = $ibook;
                    $number = (int) $fromform->Practice_number_flashcard_current;
                    $settingstosave['settings']['Practice_number_flashcard_current'] = $number;
                    break;

                case 'practiceq':
                    $ibook = (bool) $fromform->PracticeQ_include_IB;
                    $settingstosave['settings']['PracticeQ_include_IB'] = $ibook;
                    $number = (int) $fromform->PracticeQ_number_flashcard_current;
                    $settingstosave['settings']['PracticeQ_number_flashcard_current'] = $number;
                    break;

                case 'grade':
                    $ibook = (bool) $fromform->Grade_include_IB;
                    $settingstosave['settings']['Grade_include_IB'] = $ibook;
                    $number = (int) $fromform->Grade_number_question_current;
                    $settingstosave['settings']['Grade_number_question_current'] = $number;
                    break;

                case 'gradeq':
                    $ibook = (bool) $fromform->GradeQ_include_IB;
                    $settingstosave['settings']['GradeQ_include_IB'] = $ibook;
                    $number = (int) $fromform->GradeQ_number_question_current;
                    $settingstosave['settings']['GradeQ_number_question_current'] = $number;
                    break;

                case 'flashcards':
                    $number = (int) $fromform->Flashcards_number_flashcard_current;
                    $settingstosave['settings']['Flashcards_number_flashcard_current'] = $number;
                    break;

                case 'ivideo':
                    $number = (int) $fromform->IV_number_question_perset_current;
                    $settingstosave['settings']['IV_number_question_perset_current'] = $number;
                    break;
            }
        }

        $success = \local_nolej\api\api::writecontent($documentid, 'settings.json', json_encode($settingstosave));
        if (!$success) {
            redirect(
                (
                    new \moodle_url(
                        '/local/nolej/edit.php',
                        [
                            'documentid' => $documentid,
                            'step' => 'settings'
                        ]
                    )
                )->out(false),
                get_string('cannotwritesettings', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
            return;
        }

        $success = \local_nolej\api\api::putcontent($documentid, 'settings', 'settings.json');
        if ($success) {
            $DB->update_record(
                'nolej_module',
                (object) [
                    'id' => $document->id,
                    'document_id' => $documentid,
                    'status' => \local_nolej\api\api::STATUS_ACTIVITIES_PENDING
                ]
            );

            $DB->insert_record(
                'nolej_activity',
                (object) [
                    'document_id' => $documentid,
                    'user_id' => $USER->id,
                    'action' => 'activities',
                    'tstamp' => time(),
                    'status' => 'ok',
                    'code' => 200,
                    'error_message' => '',
                    'consumed_credit' => 0,
                    'notified' => true
                ],
                false
            );

            redirect(
                (new \moodle_url('/local/nolej/manage.php'))->out(false),
                get_string('generationstarted', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        } else {
            redirect(
                (
                    new \moodle_url(
                        '/local/nolej/edit.php',
                        [
                            'documentid' => $documentid,
                            'step' => 'settings'
                        ]
                    )
                )->out(false),
                get_string('settingsnotsaved', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    } else {
        // Data not valid, display the form.
    }

    echo $OUTPUT->header();
    printdocumentinfo();
    $mform->display();
    echo $OUTPUT->footer();
}

/**
 * Internal functions
 */

/**
 * Remove anything which isn't a word, number
 * or any of the following caracters -_().
 * Remove any runs of periods.
 * @see https://stackoverflow.com/a/2021729
 * 
 * @param string $filename
 * @return string
 */
function sanitizefilename($filename)
{
    $filename = mb_ereg_replace("([^\w\d\-_\(\).])", '', $filename);
    $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
    return $filename;
}

/**
 * If file already exists add an incremental suffix
 * 
 * @param string $filename
 * @param string $directory
 * 
 * @return string unique filename
 * 
 * @see https://forums.phpfreaks.com/topic/103941-solved-auto-increment-if-file-exists/
 */
function uniquefilename($filename, $directory)
{
    $basename = pathinfo($filename, PATHINFO_FILENAME);
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    if (file_exists($directory . $filename) && !is_dir($directory . $filename)) {
        if (preg_match('/(^.*?)+(?:___(\d+))?(\.(?:\w){0,3}$)/si', $filename, $options)) {
            $offset = (int) $options[2];
            while (file_exists($directory . $filename) && !is_dir($directory . $filename)) {
                $offset = $offset + 1;
                $filename = $basename . '___' . $offset . '.' . $extension;
            }
        }
    }
    return $filename;
}

/**
 * Generate a random string
 * @param int $length
 * @return string
 * 
 * @see https://stackoverflow.com/a/4356295
 */
function randomstring($length = 10)
{
    $characters = '0123456789bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function printdocumentinfo()
{
    global $OUTPUT, $document, $step;

    $reviewavailable = $document->status >= \local_nolej\api\api::STATUS_REVISION;

    echo $OUTPUT->render_from_template(
        'local_nolej/documentinfo',
        (object) [
            'lang' => [
                'documentinfo' => get_string('documentinfo', 'local_nolej'),
                'title' => get_string('title', 'local_nolej'),
                'source' => get_string('source', 'local_nolej'),
                'sourcetype' => get_string('sourcetype', 'local_nolej'),
                'transcription' => get_string('transcription', 'local_nolej'),
                'summary' => get_string('summary', 'local_nolej'),
                'questions' => get_string('questions', 'local_nolej'),
                'concepts' => get_string('concepts', 'local_nolej'),
                'settings' => get_string('settings', 'local_nolej')
            ],
            'title' => $document->title,
            'source' => $document->doc_url,
            'sourcetype' => get_string('source' . $document->media_type, 'local_nolej'),
            'transcription' => $reviewavailable ? \local_nolej\api\api::readcontent($document->document_id, 'transcription.htm') : null,
            'review' => $reviewavailable,
            'summary' => $step == 'summary',
            'questions' => $step == 'questions',
            'concepts' => $step == 'concepts',
            'settings' => $step == 'activities',
            'editurl' => (new \moodle_url('/local/nolej/edit.php', ['documentid' => $document->document_id]))->out(false)
        ]
    );
}
