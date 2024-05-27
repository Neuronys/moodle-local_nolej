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
 * Nolej module class
 *
 * @package     local_nolej
 * @author      2024 Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej;

defined('MOODLE_INTERNAL') || die();

/**
 * Nolej module class
 */
class module
{
    /** @var int */
    const STATUS_CREATION = 0;

    /** @var int */
    const STATUS_CREATION_PENDING = 1;

    /** @var int */
    const STATUS_ANALYSIS = 2;

    /** @var int */
    const STATUS_ANALYSIS_PENDING = 3;

    /** @var int */
    const STATUS_REVISION = 4;

    /** @var int */
    const STATUS_REVISION_PENDING = 5;

    /** @var int */
    const STATUS_ACTIVITIES = 6;

    /** @var int */
    const STATUS_ACTIVITIES_PENDING = 7;

    /** @var int */
    const STATUS_COMPLETED = 8;

    /** @var int */
    const STATUS_FAILED = 9;

    /** @var string */
    const STEP_CREATION = 'creation';

    /** @var string */
    const STEP_ANALYSIS = 'analysis';

    /** @var string */
    const STEP_CONCEPTS = 'concepts';

    /** @var string */
    const STEP_QUESTIONS = 'questions';

    /** @var string */
    const STEP_SUMMARY = 'summary';

    /** @var string */
    const STEP_ACTIVITIES = 'activities';

    /** @var ?object */
    protected ?object $document;

    /** @var ?string */
    protected ?string $documentid;

    /** @var string */
    protected string $step;

    /**
     * Constructor
     *
     * @param object|null $document default null
     * @param string $step default ''
     */
    public function __construct(?object $document = null, string $step = '')
    {
        $this->document = $document;
        $this->documentid = $document != null ? $document->document_id : null;
        $this->step = $step;
    }

    /**
     * Get module status name
     * @param int $status
     * @return string
     */
    public static function getstatusname(int $status)
    {
        switch ($status) {
            case self::STATUS_CREATION:
                return get_string('statuscreation', 'local_nolej');
            case self::STATUS_CREATION_PENDING:
                return get_string('statuscreationpending', 'local_nolej');
            case self::STATUS_ANALYSIS:
                return get_string('statusanalysis', 'local_nolej');
            case self::STATUS_ANALYSIS_PENDING:
                return get_string('statusanalysispending', 'local_nolej');
            case self::STATUS_REVISION:
                return get_string('statusrevision', 'local_nolej');
            case self::STATUS_REVISION_PENDING:
                return get_string('statusrevisionpending', 'local_nolej');
            case self::STATUS_ACTIVITIES:
                return get_string('statusactivities', 'local_nolej');
            case self::STATUS_ACTIVITIES_PENDING:
                return get_string('statusactivitiespending', 'local_nolej');
            case self::STATUS_COMPLETED:
                return get_string('statuscompleted', 'local_nolej');
            case self::STATUS_FAILED:
            default:
                return get_string('statusfailed', 'local_nolej');
        }
    }

    /**
     * Set module step
     * @param string $step
     */
    public function setstep(string $step)
    {
        $this->step = $step;
    }

    /**
     * Handle creation form
     */
    public function creation()
    {
        global $OUTPUT, $PAGE, $DB, $USER, $SITE, $context;

        // Display and handle creation form.
        $mform = new \local_nolej\form\creation();

        if ($mform->is_cancelled()) {
            // Cancelled.
            redirect(
                new \moodle_url('/local/nolej/manage.php'),
                get_string('modulenotcreated', 'local_nolej'),
                null,
                \core\output\notification::NOTIFY_INFO
            );
        } else if ($fromform = $mform->get_data()) {
            // Submitted and validated.
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
                    $uploaddir = api::uploaddir();

                    $filename = $mform->get_new_filename('sourcefile');
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $format = api::formatfromextension($extension);
                    if (empty($format)) {
                        break;
                    }
                    $filename = $USER->id . '.' . random_string(10) . '.' . $extension;
                    $filename = $this->uniquefilename($filename, $uploaddir . '/');
                    $dest = $uploaddir . '/' . $filename;
                    $success = $mform->save_file('sourcefile', $dest);
                    if ($success) {
                        chmod($dest, 0775);
                        $webhook = new \moodle_url(
                            '/local/nolej/webhook.php',
                            ['fileid' => $filename]
                        );
                        $url = $webhook->out(false);
                    }
                    break;

                case 'text':
                    $uploaddir = api::uploaddir();

                    $filename = $USER->id . '.' . random_string(10) . '.htm';
                    $filename = $this->uniquefilename($filename, $uploaddir . '/');
                    $dest = $uploaddir . '/' . $filename;
                    $success = file_put_contents($dest, $fromform->sourcetext);
                    if ($success) {
                        chmod($dest, 0775);
                        $webhook = new \moodle_url(
                            '/local/nolej/webhook.php',
                            ['fileid' => $filename]
                        );
                        $url = $webhook->out(false);
                        $format = 'freetext';
                    }
                    break;
            }

            if (!empty($url) && !empty($format)) {
                // Call Nolej creation API.
                $webhook = new \moodle_url('/local/nolej/webhook.php');

                $result = api::post(
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
                        'language' => $language,
                    ],
                    true
                );

                if (!is_object($result) || !property_exists($result, 'id') || !is_string($result->id)) {

                    // An error occurred.
                    \core\notification::add(
                        get_string('errdocument', 'local_nolej', var_export($result, true)),
                        core\output\notification::NOTIFY_ERROR
                    );

                    if (
                        ($sourcetype == 'file' || $sourcetype == 'text') &&
                        isset($dest) &&
                        !empty($dest) &&
                        is_file($dest)
                    ) {
                        // Remove the uploaded file.
                        unlink($dest);
                    }
                } else {
                    $DB->insert_record(
                        'nolej_module',
                        (object) [
                            'document_id' => $result->id,
                            'user_id' => $USER->id,
                            'tstamp' => time(),
                            'status' => self::STATUS_CREATION_PENDING,
                            'title' => $title,
                            'consumed_credit' => $consumedcredit,
                            'doc_url' => $url,
                            'media_type' => $format,
                            'automatic_mode' => $automaticmode,
                            'language' => $language,
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
                            'notified' => false,
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
                \core\notification::add(
                    get_string('errdatamissing', 'local_nolej'),
                    \core\output\notification::NOTIFY_ERROR
                );
            }
        }

        $PAGE->requires->js_call_amd('local_nolej/creation');

        echo $OUTPUT->header();
        $mform->display();
        echo $OUTPUT->footer();
    }

    /**
     * Handle analysis form
     */
    public function analysis()
    {
        global $OUTPUT, $DB, $USER, $PAGE;

        // Display and handle analysis form.
        $mform = new \local_nolej\form\transcription(
            (
                new \moodle_url(
                    '/local/nolej/edit.php',
                    [
                        'documentid' => $this->documentid,
                        'step' => 'analysis',
                    ]
                )
            )->out(false),
            ['documentid' => $this->documentid]
        );

        if ($mform->is_cancelled()) {
            // Cancelled.
            redirect(new \moodle_url('/local/nolej/manage.php'));
        } else if ($fromform = $mform->get_data()) {
            // Submitted and validated.

            $title = $fromform->title;
            $transcription = $fromform->transcription;

            if (!empty($transcription)) {
                api::writecontent(
                    $this->documentid,
                    'transcription.htm',
                    $transcription
                );

                // Call Nolej analysis API.
                $webhook = (
                    new \moodle_url(
                        '/local/nolej/webhook.php',
                        [
                            'documentid' => $this->documentid,
                            'fileid' => 'transcription.htm',
                        ]
                    )
                )->out(false);

                $result = api::put(
                    "/documents/{$this->documentid}/transcription",
                    [
                        's3URL' => $webhook,
                        'automaticMode' => false,
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
                        get_string('genericerror', 'local_nolej', (object) ['error' => var_export($result, true)]),
                        null,
                        \core\output\notification::NOTIFY_ERROR
                    );
                }

                $DB->update_record(
                    'nolej_module',
                    (object) [
                        'id' => $this->document->id,
                        'document_id' => $this->documentid,
                        'status' => self::STATUS_ANALYSIS_PENDING,
                        'title' => $title,
                    ]
                );

                $DB->insert_record(
                    'nolej_activity',
                    (object) [
                        'document_id' => $this->documentid,
                        'user_id' => $USER->id,
                        'action' => 'transcription',
                        'tstamp' => time(),
                        'status' => 'ok',
                        'code' => 200,
                        'error_message' => '',
                        'consumed_credit' => 0,
                        'notified' => true,
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
                                'documentid' => $this->documentid,
                                'step' => 'analysis',
                            ]
                        )
                    )->out(false),
                    get_string('missingtranscription', 'local_nolej'),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
            }
        } else {
            // Data not valid.
            $PAGE->requires->js_call_amd('local_nolej/confirmanalysis');
        }

        echo $OUTPUT->header();
        $this->printinfo();
        $mform->display();
        echo $OUTPUT->footer();
    }

    /**
     * Handle review concepts form
     */
    public function concepts()
    {
        global $OUTPUT, $DB, $USER;

        // Display and handle concepts form.
        $mform = new \local_nolej\form\concepts(
            (
                new \moodle_url(
                    '/local/nolej/edit.php',
                    [
                        'documentid' => $this->documentid,
                        'step' => 'concepts',
                    ]
                )
            )->out(false),
            ['documentid' => $this->documentid]
        );

        if ($mform->is_cancelled()) {

            // Cancelled.
            redirect(new \moodle_url('/local/nolej/manage.php'));

        } else if ($fromform = $mform->get_data()) {
            // Submitted and validated.

            // Download concepts.
            $result = api::getcontent(
                $this->documentid,
                'concepts',
                'concepts.json'
            );

            $json = api::readcontent($this->documentid, 'concepts.json');
            if (!$json) {
                redirect(
                    new \moodle_url('/local/nolej/manage.php'),
                    get_string('genericerror', 'local_nolej', ['error' => var_export($result, true)]),
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

            $success = api::writecontent(
                $this->documentid,
                'concepts.json',
                json_encode(['concepts' => $concepts])
            );
            if (!$success) {
                redirect(
                    (
                        new \moodle_url(
                            '/local/nolej/edit.php',
                            [
                                'documentid' => $this->documentid,
                                'step' => 'concepts',
                            ]
                        )
                    )->out(false),
                    get_string('cannotwriteconcepts', 'local_nolej'),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
                return;
            }

            $success = api::putcontent($this->documentid, 'concepts', 'concepts.json');
            redirect(
                (
                    new \moodle_url(
                        '/local/nolej/edit.php',
                        [
                            'documentid' => $this->documentid,
                            'step' => 'concepts',
                        ]
                    )
                )->out(false),
                get_string($success ? 'conceptssaved' : 'conceptsnotsaved', 'local_nolej'),
                null,
                $success ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR
            );
        }

        echo $OUTPUT->header();
        $this->printinfo();
        $mform->display();
        echo $OUTPUT->footer();
    }

    /**
     * Handle review questions form
     */
    public function questions()
    {
        global $OUTPUT, $DB, $USER;

        // Display and handle questions form.
        $mform = new \local_nolej\form\questions(
            (
                new \moodle_url(
                    '/local/nolej/edit.php',
                    [
                        'documentid' => $this->documentid,
                        'step' => 'questions',
                    ]
                )
            )->out(false),
            ['documentid' => $this->documentid]
        );

        if ($mform->is_cancelled()) {

            // Cancelled.
            redirect(new \moodle_url('/local/nolej/manage.php'));

        } else if ($fromform = $mform->get_data()) {
            // Submitted and validated.

            // Download questions.
            $result = api::getcontent(
                $this->documentid,
                'questions',
                'questions.json'
            );

            $json = api::readcontent($this->documentid, 'questions.json');
            if (!$json) {
                redirect(
                    new \moodle_url('/local/nolej/manage.php'),
                    get_string('genericerror', 'local_nolej', ['error' => var_export($result, true)]),
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

            $success = api::writecontent(
                $this->documentid,
                'questions.json',
                json_encode(['questions' => $questions])
            );
            if (!$success) {
                redirect(
                    (
                        new \moodle_url(
                            '/local/nolej/edit.php',
                            [
                                'documentid' => $this->documentid,
                                'step' => 'questions',
                            ]
                        )
                    )->out(false),
                    get_string('cannotwritequestions', 'local_nolej'),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
                return;
            }

            $success = api::putcontent($this->documentid, 'questions', 'questions.json');
            redirect(
                (
                    new \moodle_url(
                        '/local/nolej/edit.php',
                        [
                            'documentid' => $this->documentid,
                            'step' => 'questions',
                        ]
                    )
                )->out(false),
                get_string($success ? 'questionssaved' : 'questionsnotsaved', 'local_nolej'),
                null,
                $success ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR
            );
        }

        echo $OUTPUT->header();
        $this->printinfo();
        $mform->display();
        echo $OUTPUT->footer();
    }

    /**
     * Handle review summary form
     */
    public function summary()
    {
        global $OUTPUT, $DB, $USER;

        // Display and handle summary form.
        $mform = new \local_nolej\form\summary(
            (
                new \moodle_url(
                    '/local/nolej/edit.php',
                    [
                        'documentid' => $this->documentid,
                        'step' => 'summary',
                    ]
                )
            )->out(false),
            ['documentid' => $this->documentid]
        );

        if ($mform->is_cancelled()) {

            // Cancelled.
            redirect(new \moodle_url('/local/nolej/manage.php'));

        } else if ($fromform = $mform->get_data()) {
            // Submitted and validated.

            $summary = [
                'summary' => [],
                'abstract' => '',
                'keypoints' => [],
            ];

            $summarycount = $fromform->summarycount;
            for ($i = 0; $i < $summarycount; $i++) {
                $title = $fromform->{'summary_' . $i . '_title'};
                $txt = $fromform->{'summary_' . $i . '_text'};
                if (!empty($title) && !empty($txt)) {
                    $summary['summary'][] = [
                        'title' => $title,
                        'text' => $txt,
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

            $success = api::writecontent(
                $this->documentid,
                'summary.json',
                json_encode($summary)
            );
            if (!$success) {
                redirect(
                    (
                        new \moodle_url(
                            '/local/nolej/edit.php',
                            [
                                'documentid' => $this->documentid,
                                'step' => 'summary',
                            ]
                        )
                    )->out(false),
                    get_string('cannotwritesummary', 'local_nolej'),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
                return;
            }

            $success = api::putcontent($this->documentid, 'summary', 'summary.json');
            redirect(
                (
                    new \moodle_url(
                        '/local/nolej/edit.php',
                        [
                            'documentid' => $this->documentid,
                            'step' => 'summary',
                        ]
                    )
                )->out(false),
                get_string($success ? 'summarysaved' : 'summarynotsaved', 'local_nolej'),
                null,
                $success ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR
            );
        }

        echo $OUTPUT->header();
        $this->printinfo();
        $mform->display();
        echo $OUTPUT->footer();
    }

    /**
     * Handle activities form
     */
    public function activities()
    {
        global $OUTPUT, $DB, $USER;

        // Display and handle activities form.
        $mform = new \local_nolej\form\activities(
            (
                new \moodle_url(
                    '/local/nolej/edit.php',
                    [
                        'documentid' => $this->documentid,
                        'step' => 'activities',
                    ]
                )
            )->out(false),
            ['documentid' => $this->documentid]
        );

        if ($mform->is_cancelled()) {

            // Cancelled.
            redirect(new \moodle_url('/local/nolej/manage.php'));

        } else if ($fromform = $mform->get_data()) {
            // Submitted and validated.

            // Download settings.
            $result = api::getcontent(
                $this->documentid,
                'settings',
                'settings.json'
            );

            $json = api::readcontent($this->documentid, 'settings.json');
            if (!$json) {
                redirect(
                    new \moodle_url('/local/nolej/manage.php'),
                    get_string('genericerror', 'local_nolej', ['error' => var_export($result, true)]),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
            }

            $settings = json_decode($json, true);
            $availableactivities = $settings['avaible_packages'] ?? [];

            $settingstosave = [
                'settings' => $settings['settings'],
                'avaible_packages' => $availableactivities,
                'desired_packages' => [],
            ];

            for ($i = 0, $len = count($availableactivities); $i < $len; $i++) {
                $useactivity = (bool) $fromform->{'activity_' . $availableactivities[$i]};
                if (!$useactivity) {
                    // Activity not to be generated, skip related settings.
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

            $success = api::writecontent(
                $this->documentid,
                'settings.json',
                json_encode($settingstosave)
            );
            if (!$success) {
                redirect(
                    (
                        new \moodle_url(
                            '/local/nolej/edit.php',
                            [
                                'documentid' => $this->documentid,
                                'step' => 'settings',
                            ]
                        )
                    )->out(false),
                    get_string('cannotwritesettings', 'local_nolej'),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
                return;
            }

            $success = api::putcontent($this->documentid, 'settings', 'settings.json');
            if ($success) {
                $DB->update_record(
                    'nolej_module',
                    (object) [
                        'id' => $this->document->id,
                        'document_id' => $this->documentid,
                        'status' => self::STATUS_ACTIVITIES_PENDING,
                    ]
                );

                $DB->insert_record(
                    'nolej_activity',
                    (object) [
                        'document_id' => $this->documentid,
                        'user_id' => $USER->id,
                        'action' => 'activities',
                        'tstamp' => time(),
                        'status' => 'ok',
                        'code' => 200,
                        'error_message' => '',
                        'consumed_credit' => 0,
                        'notified' => true,
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
                                'documentid' => $this->documentid,
                                'step' => 'settings',
                            ]
                        )
                    )->out(false),
                    get_string('settingsnotsaved', 'local_nolej'),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
            }
        }

        echo $OUTPUT->header();
        $this->printinfo();
        $mform->display();
        echo $OUTPUT->footer();
    }

    /**
     * Print module info
     */
    public function printinfo()
    {
        global $OUTPUT, $PAGE;

        $PAGE->requires->js_call_amd('local_nolej/toggleinfo');
        $reviewavailable = $this->document->status >= self::STATUS_REVISION;

        echo $OUTPUT->render_from_template(
            'local_nolej/documentinfo',
            (object) [
                'title' => $this->document->title,
                'source' => $this->document->doc_url,
                'sourcetype' => get_string('source' . $this->document->media_type, 'local_nolej'),
                'transcription' => $reviewavailable ? api::readcontent($this->document->document_id, 'transcription.htm') : null,
                'review' => $reviewavailable,
                'concepts' => $this->step == 'concepts',
                'questions' => $this->step == 'questions',
                'summary' => $this->step == 'summary',
                'settings' => $this->step == 'activities',
                'editurl' => (new \moodle_url('/local/nolej/edit.php', ['documentid' => $this->document->document_id]))->out(false),
                'manageurl' => (new \moodle_url('/local/nolej/manage.php'))->out(false),
            ]
        );
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
    protected function uniquefilename($filename, $directory)
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
}
