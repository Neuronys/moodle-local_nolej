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
 * Nolej API
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej;

defined('MOODLE_INTERNAL') || die();

use curl;
use moodle_url;
use context_coursecat;
use core_course_category;
use core_user;
use Exception;
use core\message\message;
use core\output\notification;
use core_h5p\factory;
use core_h5p\file_storage;
use contenttype_h5p\contenttype;
use local_nolej\event\webhook_called;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use mod_lti\local\ltiopenid\jwks_helper;

global $CFG;
require_once($CFG->dirroot . '/local/nolej/classes/event/webhook_called.php');
require_once($CFG->dirroot . '/local/nolej/classes/module.php');

/**
 * Nolej API class
 */
class api {

    /** @var string Nolej API endpoint */
    const ENDPOINT = 'https://api-live.nolej.io';

    /** @var string[] Allowed audio formats */
    const TYPE_AUDIO = ['mp3', 'wav', 'opus', 'ogg', 'oga', 'm4a'];

    /** @var string[] Allowed video formats */
    const TYPE_VIDEO = ['m4v', 'mp4', 'ogv', 'avi', 'webm'];

    /** @var string[] Allowed document formats */
    const TYPE_DOC = ['pdf', 'doc', 'docx', 'odt'];

    /** @var string[] Allowed text file formats */
    const TYPE_TEXT = ['txt', 'htm', 'html'];

    /** @var int Max bytes for uploaded files (1 GB) */
    const MAX_SIZE = 1073741824;

    /** @var array */
    protected $data;

    /** @var bool */
    public $shouldexit = true;

    /**
     * Check that the API key has been set
     * @return bool
     */
    public static function haskey() {
        return !empty(get_config('local_nolej', 'api_key'));
    }

    /**
     * Send a GET request to Nolej API
     * @param string $path
     * @param array $data
     * @param bool $encodeinput
     * @param bool $decodeoutput
     *
     * @return object|string return the result given by Nolej. If
     * $decodeoutput is true, treat the result as json object and decode it.
     */
    public static function get(
        $path,
        $data = [],
        $encodeinput = false,
        $decodeoutput = true
    ) {
        $apikey = get_config('local_nolej', 'api_key');
        if (!$apikey) {
            return null;
        }

        $url = self::ENDPOINT . $path;
        $encodeddata = empty($data) ? null : ($encodeinput ? json_encode($data) : $data);

        $options = [
            'CURLOPT_CUSTOMREQUEST' => 'GET', // Need a GET request with POST data.
            'RETURNTRANSFER' => 1,
            'HEADER' => 0,
            'FAILONERROR' => 0,
        ];

        $header = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: X-API-KEY ' . $apikey,
            'User-Agent: Moodle Plugin',
        ];

        $curl = new curl();
        $curl->setHeader($header);
        $response = $curl->post($url, $encodeddata, $options);

        if (!$decodeoutput) {
            return $response;
        }

        $object = json_decode($response);
        return $object !== null ? $object : $response;
    }

    /**
     * Send a POST request to Nolej API
     * @param string $path
     * @param array $data
     * @param bool $decode
     *
     * @return object|string return the result given by Nolej. If
     * $decodeOutput is true, treat the result as json object and decode it.
     */
    public static function post(
        $path,
        $data = [],
        $decode = true
    ) {
        $apikey = get_config('local_nolej', 'api_key');
        if (!$apikey) {
            return null;
        }

        $jsondata = json_encode($data);
        $url = self::ENDPOINT . $path;

        $options = [
            'RETURNTRANSFER' => 1,
            'HEADER' => 0,
            'FAILONERROR' => 0,
        ];

        $header = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: X-API-KEY ' . $apikey,
            'User-Agent: Moodle Plugin',
        ];

        $curl = new curl();
        $curl->setHeader($header);
        $jsonresult = $curl->post($url, $jsondata, $options);

        if (!$decode) {
            return $jsonresult;
        }

        $object = json_decode($jsonresult);
        return $object !== null ? $object : $jsonresult;
    }

    /**
     * Send a PUT request to Nolej API
     * @param string $path
     * @param mixed $data
     * @param bool $encode input's data
     * @param bool $decode output
     */
    public static function put(
        $path,
        $data = [],
        $encode = false,
        $decode = true
    ) {
        $apikey = get_config('local_nolej', 'api_key');
        if (!$apikey) {
            return null;
        }

        $jsondata = $encode ? json_encode($data) : $data;
        $url = self::ENDPOINT . $path;

        $options = [
            'RETURNTRANSFER' => 1,
            'HEADER' => 0,
            'FAILONERROR' => 0,
        ];

        $header = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: X-API-KEY ' . $apikey,
            'User-Agent: Moodle Plugin',
        ];

        $curl = new curl();
        $curl->setHeader($header);
        $jsonresult = $curl->put($url, $jsondata, $options);

        if (!$decode) {
            return $jsonresult;
        }

        $object = json_decode($jsonresult);
        return $object !== null ? $object : $jsonresult;
    }

    /**
     * Return all the allowed formats
     * @return array
     */
    public static function allowedtypes() {
        return array_merge(
            self::TYPE_AUDIO,
            self::TYPE_VIDEO,
            self::TYPE_DOC,
            self::TYPE_TEXT
        );
    }

    /**
     * Return the format given the extension, or null if not valid.
     * @param string $extension
     * @return ?string
     */
    public static function formatfromextension($extension) {
        if (in_array($extension, self::TYPE_AUDIO)) {
            return 'audio';
        }
        if (in_array($extension, self::TYPE_VIDEO)) {
            return 'video';
        }
        if (in_array($extension, self::TYPE_DOC)) {
            return 'document';
        }
        if (in_array($extension, self::TYPE_TEXT)) {
            return 'freetext';
        }
        return null;
    }

    /**
     * Return the Nolej data directory
     * @param ?string $documentid (optional)
     * @return string
     */
    public static function datadir($documentid = null) {
        global $CFG;
        $datadir = $CFG->dataroot . '/local_nolej';
        if ($documentid != null) {
            $datadir .= '/' . $documentid;
        }
        if (!file_exists($datadir)) {
            mkdir($datadir, 0744, true);
        }
        return $datadir;
    }

    /**
     * Return the Nolej upload directory
     * @return string
     */
    public static function uploaddir() {
        $datadir = self::datadir();
        $uploaddir = $datadir . '/uploads';
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir, 0744, true);
        }
        return $uploaddir;
    }

    /**
     * Return the Nolej h5p directory for a document
     * @param string $documentid
     * @return string
     */
    public static function h5pdir($documentid) {
        $datadir = self::datadir($documentid);
        $uploaddir = $datadir . '/h5p';
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir, 0744, true);
        }
        return $uploaddir;
    }

    /**
     * Fetch and save the content of a Nolej file
     *
     * @param string $documentid
     * @param string $pathname the 'id' of Nolej file
     * @param string|null $saveas the name of the file to be saved as
     * @param bool $forcedownload if false check if the file already exists
     * @param array $withdata
     * @param bool $encodeinput iff true encode input's data
     *
     * @return bool|string return true on success, false on failure. If $saveAs
     * is null, then the content is returned as string.
     */
    public static function getcontent(
        $documentid,
        $pathname,
        $saveas = null,
        $forcedownload = false,
        $withdata = [],
        $encodeinput = false
    ) {
        $filepath = self::datadir($documentid) . '/' . $saveas;

        if (
            $saveas != null &&
            !$forcedownload &&
            is_file($filepath)
        ) {
            return true;
        }

        $result = self::get(
            sprintf('/documents/%s/%s', $documentid, $pathname),
            $withdata,
            $encodeinput,
            false
        );

        return $saveas == null
            ? $result
            : self::writecontent($documentid, $saveas, $result);
    }

    /**
     * Read a file of the requested document, if exists.
     *
     * @param string $documentid
     * @param string $filename the name of the file to read
     * @return string|false return the content if the file exists,
     * false otherwise.
     */
    public static function readcontent($documentid, $filename) {
        $path = self::datadir($documentid) . '/' . $filename;
        if (!file_exists($path)) {
            return false;
        }
        return file_get_contents($path);
    }

    /**
     * Put the content of a file to Nolej
     *
     * @param string $documentid
     * @param string $pathname the 'id' of Nolej file
     * @param string $filename the name of the file on disk
     *
     * @return bool true on success, false on failure
     */
    public static function putcontent($documentid, $pathname, $filename) {
        $content = self::readcontent($documentid, $filename);
        if (!$content) {
            return false;
        }

        return self::put(
            sprintf('/documents/%s/%s', $documentid, $pathname),
            $content
        );
    }

    /**
     * Write a file of the document.
     *
     * @param string $documentid
     * @param string $filename the name of the file.
     * @param string $content the content of the file.
     *
     * @return bool returns true on success, false on failure.
     */
    public static function writecontent($documentid, $filename, $content) {
        return file_put_contents(
            self::datadir($documentid) . '/' . $filename,
            $content
        ) !== false;
    }

    /**
     * Lookup for a document status
     * @param string $documentid
     * @param int $userid (optional)
     * @return int status
     */
    public static function lookupdocumentstatus($documentid, $userid = null) {
        global $DB;

        if ($userid != null) {
            $document = $DB->get_record(
                'local_nolej_module',
                [
                    'document_id' => $documentid,
                    'user_id' => $userid,
                ]
            );
        } else {
            $document = $DB->get_record(
                'local_nolej_module',
                ['document_id' => $documentid]
            );
        }

        if (!$document) {
            return -1;
        }

        return $document->status;
    }

    /**
     * Log the event
     * @param string $message
     * @param ?string $documentid
     */
    public function log($message, $documentid = null) {
        $event = webhook_called::create(
            [
                'other' => [
                    'documentid' => $documentid,
                    'message' => $message,
                ],
            ]
        );
        $event->trigger();
    }

    /**
     * Parse the request.
     * @param mixed $data if null parse POST data.
     */
    public function parse($data = null) {
        if ($data == null) {
            header('Content-type: application/json; charset=UTF-8');
            try {
                $data = json_decode(file_get_contents('php://input'), true);
            } catch (Exception $e) {
                $this->respondwithmessage(400, 'Request not valid.');
                return;
            }
        }

        if (
            !is_array($data) ||
            !isset($data['action']) ||
            !is_string($data['action'])
        ) {
            $this->log('Received invalid request: ' . var_export($data, true));
            $this->respondwithmessage(400, 'Request not valid.');
            return;
        }

        $this->data = $data;
        switch ($data['action']) {
            case 'transcription':
                $this->log('Received transcription request: ' . var_export($data, true));
                $this->checktranscription();
                break;

            case 'analysis':
                $this->log('Received analysis request: ' . var_export($data, true));
                $this->checkanalysis();
                break;

            case 'activities':
                $this->log('Received activities request: ' . var_export($data, true));
                $this->checkactivities();
                break;

            case 'work in progress':
                $this->log('Received work in progress.');
                if (isloggedin() && !isguestuser()) {
                    \core\notification::add(
                        get_string('work_in_progress', 'local_nolej'),
                        notification::NOTIFY_INFO
                    );
                    return;
                }
                break;

            default:
                $this->log('Received invalid action: ' . var_export($data, true));
        }
    }

    /**
     * Die with status code and a message
     * @param int $code
     * @param string $message
     * @return void
     */
    public function respondwithmessage(
        $code = 400,
        $message = ''
    ) {
        if (!empty($message)) {
            $this->log('Replied to Nolej with message: ' . $message);
            if ($this->shouldexit) {
                echo json_encode(['message' => $message]);
            }
        }

        if (!$this->shouldexit) {
            return;
        }

        http_response_code($code);
        exit;
    }

    /**
     * Look for a document with a specific status
     * @param string $documentid
     * @param int $status
     * @return object|false
     */
    public function lookupdocumentwithstatus($documentid, $status) {
        global $DB;

        return $DB->get_record(
            'local_nolej_module',
            [
                'document_id' => $documentid,
                'status' => $status,
            ]
        );
    }

    /**
     * Check the transcription result
     *
     * @return void
     */
    public function checktranscription() {
        global $DB;

        if ($this->data['consumedCredit'] == null) {
            $this->data['consumedCredit'] = 0;
        }

        if (
            !isset(
            $this->data['documentID'],
            $this->data['status'],
            $this->data['code'],
            $this->data['error_message'],
            $this->data['consumedCredit']
        ) ||
            !is_string($this->data['documentID']) ||
            !is_string($this->data['status']) ||
            !is_string($this->data['error_message']) ||
            !is_integer($this->data['code']) ||
            !is_integer($this->data['consumedCredit'])
        ) {
            $this->respondwithmessage(400, 'Request not valid.');
            return;
        }

        $documentid = $this->data['documentID'];

        $document = $this->lookupdocumentwithstatus($documentid, module::STATUS_CREATION_PENDING);
        if (!$document) {
            $this->respondwithmessage(404, 'Document ID not found.');
            return;
        }

        $this->setlanguageofuser((int) $document->user_id);

        $now = time();

        if (
            $this->data['status'] != '\'ok\'' &&
            $this->data['status'] != 'ok'
        ) {
            $this->log('Result: ko');

            $success = $DB->update_record(
                'local_nolej_module',
                (object) [
                    'id' => $document->id,
                    'document_id' => $documentid,
                    'status' => module::STATUS_FAILED,
                    'consumed_credit' => $this->data['consumedCredit'],
                ]
            );
            if (!$success) {
                $this->respondwithmessage(404, 'Document not found.');
                return;
            }

            $this->sendnotification(
                $documentid,
                (int) $document->user_id,
                'transcription_ko',
                $this->data['status'],
                $this->data['code'],
                $this->data['error_message'],
                $this->data['consumedCredit'],
                'action_transcription_ko_body',
                (object) [
                    'title' => $document->title,
                    'tstamp' => userdate($now, get_string('strftimedatetimeshortaccurate', 'core_langconfig')),
                    'errormessage' => $this->data['error_message'],
                ]
            );
            return;
        }

        $success = $DB->update_record(
            'local_nolej_module',
            (object) [
                'id' => $document->id,
                'document_id' => $documentid,
                'status' => module::STATUS_ANALYSIS,
                'consumed_credit' => $this->data['consumedCredit'],
            ]
        );
        if (!$success) {
            $this->respondwithmessage(404, 'Document not found.');
            return;
        }

        $this->sendnotification(
            $documentid,
            (int) $document->user_id,
            'transcription_ok',
            $this->data['status'],
            $this->data['code'],
            $this->data['error_message'],
            $this->data['consumedCredit'],
            'action_transcription_ok_body',
            (object) [
                'title' => $document->title,
                'tstamp' => userdate($now, get_string('strftimedatetimeshortaccurate', 'core_langconfig')),
            ]
        );

        $this->respondwithmessage(200, 'Transcription received!');
    }

    /**
     * Check the analysis result
     *
     * @return void
     */
    public function checkanalysis() {
        global $DB;

        if ($this->data['consumedCredit'] == null) {
            $this->data['consumedCredit'] = 0;
        }

        if (
            !isset(
            $this->data['documentID'],
            $this->data['status'],
            $this->data['code'],
            $this->data['error_message'],
            $this->data['consumedCredit']
        ) ||
            !is_string($this->data['documentID']) ||
            !is_string($this->data['status']) ||
            !is_string($this->data['error_message']) ||
            !is_integer($this->data['code']) ||
            !is_integer($this->data['consumedCredit'])
        ) {
            $this->respondwithmessage(400, 'Request not valid.');
            return;
        }

        $documentid = $this->data['documentID'];

        $document = $this->lookupdocumentwithstatus($documentid, module::STATUS_ANALYSIS_PENDING);
        if (!$document) {
            $this->respondwithmessage(404, 'Document ID not found.');
            return;
        }

        $this->setlanguageofuser((int) $document->user_id);

        $now = time();

        if (
            $this->data['status'] != '\'ok\'' &&
            $this->data['status'] != 'ok'
        ) {
            $this->log('Result: ko');

            $success = $DB->update_record(
                'local_nolej_module',
                (object) [
                    'id' => $document->id,
                    'document_id' => $documentid,
                    'status' => module::STATUS_FAILED,
                    'consumed_credit' => $this->data['consumedCredit'],
                ]
            );
            if (!$success) {
                $this->respondwithmessage(404, 'Document not found.');
                return;
            }

            $this->sendnotification(
                $documentid,
                (int) $document->user_id,
                'analysis_ko',
                $this->data['status'],
                $this->data['code'],
                $this->data['error_message'],
                $this->data['consumedCredit'],
                'action_analysis_ko_body',
                (object) [
                    'title' => $document->title,
                    'tstamp' => userdate($now, get_string('strftimedatetimeshortaccurate', 'core_langconfig')),
                    'errormessage' => $this->data['error_message'],
                ]
            );
            return;
        }

        $success = $DB->update_record(
            'local_nolej_module',
            (object) [
                'id' => $document->id,
                'document_id' => $documentid,
                'status' => module::STATUS_REVISION,
                'consumed_credit' => $this->data['consumedCredit'],
            ]
        );
        if (!$success) {
            $this->respondwithmessage(404, 'Document not found.');
            return;
        }

        $this->sendnotification(
            $documentid,
            (int) $document->user_id,
            'analysis_ok',
            $this->data['status'],
            $this->data['code'],
            $this->data['error_message'],
            $this->data['consumedCredit'],
            'action_analysis_ok_body',
            (object) [
                'title' => $document->title,
                'tstamp' => userdate($now, get_string('strftimedatetimeshortaccurate', 'core_langconfig')),
            ]
        );

        $this->respondwithmessage(200, 'Analysis received!');
    }

    /**
     * Check the activities result
     *
     * @return void
     */
    protected function checkactivities() {
        global $DB;

        if ($this->data['consumedCredit'] == null) {
            $this->data['consumedCredit'] = 0;
        }

        if (
            !isset(
            $this->data['documentID'],
            $this->data['status'],
            $this->data['code'],
            $this->data['error_message'],
            $this->data['consumedCredit']
        ) ||
            !is_string($this->data['documentID']) ||
            !is_string($this->data['status']) ||
            !is_string($this->data['error_message']) ||
            !is_integer($this->data['code']) ||
            !is_integer($this->data['consumedCredit'])
        ) {
            $this->respondwithmessage(400, 'Request not valid.');
            return;
        }

        $documentid = $this->data['documentID'];

        $document = $this->lookupdocumentwithstatus($documentid, module::STATUS_ACTIVITIES_PENDING);
        if (!$document) {
            $this->respondwithmessage(404, 'Document ID not found.');
            return;
        }

        $this->setlanguageofuser((int) $document->user_id);

        $now = time();

        if (
            $this->data['status'] != '\'ok\'' &&
            $this->data['status'] != 'ok'
        ) {
            $this->log('Result: ko');

            $success = $DB->update_record(
                'local_nolej_module',
                (object) [
                    'id' => $document->id,
                    'document_id' => $documentid,
                    'status' => module::STATUS_ACTIVITIES,
                    'consumed_credit' => $this->data['consumedCredit'],
                ]
            );
            if (!$success) {
                $this->respondwithmessage(404, 'Document not found.');
                return;
            }

            $this->sendnotification(
                $documentid,
                (int) $document->user_id,
                'activities_ko',
                $this->data['status'],
                $this->data['code'],
                $this->data['error_message'],
                $this->data['consumedCredit'],
                'action_activities_ko_body',
                (object) [
                    'title' => $document->title,
                    'tstamp' => userdate($now, get_string('strftimedatetimeshortaccurate', 'core_langconfig')),
                    'errormessage' => $this->data['error_message'],
                ]
            );
            return;
        }

        $success = $DB->update_record(
            'local_nolej_module',
            (object) [
                'id' => $document->id,
                'document_id' => $documentid,
                'status' => module::STATUS_COMPLETED,
                'consumed_credit' => $this->data['consumedCredit'],
            ]
        );
        if (!$success) {
            $this->respondwithmessage(404, 'Document not found.');
            return;
        }

        $errors = $this->downloadactivities($document);
        if (!empty($errors)) {
            $this->log('Failed to download some activities: ' . $errors . '.');
            $this->sendnotification(
                $documentid,
                (int) $document->user_id,
                'activities_ko',
                $this->data['status'],
                $this->data['code'],
                $this->data['error_message'],
                $this->data['consumedCredit'],
                'err_activities_get',
                (object) [
                    'errors' => '<ul><li>' . join('</li><li>', $errors) . '</li></ul>',
                ]
            );

            $this->respondwithmessage(200, 'Activities received, but something went wrong while retrieving them.');
            return;
        }

        $this->sendnotification(
            $documentid,
            (int) $document->user_id,
            'activities_ok',
            $this->data['status'],
            $this->data['code'],
            $this->data['error_message'],
            $this->data['consumedCredit'],
            'action_activities_ok_body',
            (object) [
                'title' => $document->title,
                'tstamp' => userdate($now, get_string('strftimedatetimeshortaccurate', 'core_langconfig')),
            ]
        );

        $this->respondwithmessage(200, 'Activities received!');
    }

    /**
     * Get the Nolej category id,
     * create it if not exists.
     * @return int
     */
    protected static function getnolejcategoryid() {
        $categoryid = get_config('local_nolej', 'categoryid');

        if (!empty($categoryid) && core_course_category::get($categoryid, IGNORE_MISSING, true) != null) {
            return (int) $categoryid;
        }

        // Create Nolej context if not exists.
        $nolejcategory = core_course_category::create((object) [
            'name' => 'Nolej',
            'description' => 'This category contains Nolej h5p contents',
            'parent' => 0,
            'visible' => 0,
        ]);
        $categoryid = $nolejcategory->id;
        set_config('categoryid', $categoryid, 'local_nolej');
        return (int) $categoryid;
    }

    /**
     * Download activities and save them in the Content Box
     *
     * @param object $document
     * @return array of errors
     */
    public function downloadactivities($document) {
        global $CFG, $DB;

        $errors = [];

        $nolejcategoryid = self::getnolejcategoryid();
        $h5pdir = self::h5pdir($document->document_id);
        $now = time();

        $fs = get_file_storage();
        $h5pfactory = new factory();

        $json = self::getcontent(
            $document->document_id,
            'activities',
            null,
            true,
            ['format' => 'h5p'],
            true
        );
        if (!$json) {
            return get_string('erractivitiesdecode', 'local_nolej');
        }
        $activities = json_decode($json);
        $activities = $activities->activities;

        // Create category.
        $modulecategory = core_course_category::create((object) [
            'name' => sprintf(
                '%s (%s)',
                $document->title,
                userdate($now, get_string('strftimedatetimeshortaccurate', 'core_langconfig'))
            ),
            'description' => userdate($now, get_string('strftimedatetimeshortaccurate', 'core_langconfig')),
            'parent' => $nolejcategoryid,
        ]);
        $modulecontext = context_coursecat::instance($modulecategory->id);

        foreach ($activities as $activity) {
            $filepath = sprintf('%s/%s.h5p', $h5pdir, $activity->activity_name);

            // Download the h5p activity.
            $success = file_put_contents(
                $filepath,
                file_get_contents($activity->url)
            );

            if (!$success) {
                $errors[] = sprintf('%s (%s)', $activity->activity_name, get_string('erractivitydownload', 'local_nolej'));
                continue;
            }

            try {

                $record = (object) [
                    'name' => get_string('activities' . $activity->activity_name, 'local_nolej') . '.h5p',
                    'configdata' => '',
                    'contenttype' => 'contenttype_h5p',
                    'title' => get_string('activities' . $activity->activity_name, 'local_nolej') . '.h5p',
                    'author' => $document->user_id,
                    'type' => $activity->activity_name,
                    'contextid' => $modulecontext->id,
                    'filepath' => '/',
                ];
                $contenttype = new contenttype($modulecontext);
                $h5pcontent = $contenttype->create_content($record);

                $filerecord = (object) [
                    'contextid' => $modulecontext->id,
                    'component' => file_storage::COMPONENT,
                    'filearea' => file_storage::CONTENT_FILEAREA,
                    'itemid' => $h5pcontent->get_id(),
                    'filepath' => '/',
                    'filename' => get_string('activities' . $activity->activity_name, 'local_nolej') . '.h5p',
                ];

                $file = $fs->create_file_from_pathname($filerecord, $filepath);

                $h5pfactory->get_framework()->set_file($file);
                $h5pcontent->import_file($file);

                $DB->insert_record(
                    'local_nolej_h5p',
                    (object) [
                        'document_id' => $document->document_id,
                        'tstamp' => $now,
                        'type' => $activity->activity_name,
                        'content_id' => $h5pcontent->get_id(),
                    ],
                    false
                );

            } catch (Exception $e) {
                $errors[] = sprintf('%s (%s)', $activity->activity_name, 'Exception: ' . var_export($e, true));
            }
        }

        return $errors;
    }

    /**
     * Send notification to user
     *
     * @param string $documentid
     * @param int $userid
     * @param string $action used as language key for title
     * @param string $status
     * @param int $code
     * @param string $errormessage
     * @param int $credits
     * @param string $body language variable to use in mail body
     * @param ?object $vars parameters to use in $bodyVar's sprintf
     */
    public function sendnotification(
        $documentid,
        $userid,
        $action,
        $status,
        $code,
        $errormessage,
        $credits,
        $body,
        $vars = null
    ) {
        global $DB;

        $DB->insert_record(
            'local_nolej_activity',
            (object) [
                'document_id' => $documentid,
                'user_id' => $userid,
                'action' => $action,
                'tstamp' => time(),
                'status' => $status,
                'code' => $code,
                'error_message' => $errormessage,
                'consumed_credit' => $credits,
                'notified' => false,
            ],
            false
        );

        $message = new message();
        $message->component = 'local_nolej';
        $message->name = $action;
        $message->userfrom = core_user::get_noreply_user();
        $message->userto = $userid;
        $message->subject = get_string('action_' . $action, 'local_nolej');
        $message->fullmessage = get_string('action_' . $action . '_body', 'local_nolej', $vars);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = get_string($body, 'local_nolej', $vars);
        $message->smallmessage = get_string('action_' . $action, 'local_nolej');
        $message->notification = 1; // Notification generated from Moodle, not a user-to-user message.
        $message->contexturl = substr($action, -2) == 'ok'
            ? (new moodle_url('/local/nolej/edit.php', ['documentid' => $documentid]))->out(false)
            : (new moodle_url('/local/nolej/manage.php'))->out(false);
        $message->contexturlname = get_string('moduleview', 'local_nolej');
        $messageid = message_send($message);

        $this->log('Message sent with ID: ' . $messageid . ' to user ' . $userid);
    }

    /**
     * Set the language of the user
     * @param int $userid
     * @return void
     */
    protected function setlanguageofuser(int $userid) {
        global $DB, $CFG, $USER;
        $user = $DB->get_record(
            'user',
            ['id' => $userid]
        );
        $preferredlanguage = $user->lang;

        if (isloggedin()) {
            $USER->lang = $preferredlanguage;
        } else {
            $CFG->lang = $preferredlanguage;
        }
    }

    /**
     * Remove anything which isn't a word, number
     * or any of the following caracters -_().
     * Remove any runs of periods.
     * @see https://stackoverflow.com/a/2021729
     *
     * @param string $filename
     * @return string
     */
    public static function sanitizefilename($filename) {
        $filename = mb_ereg_replace("([^\w\d\-_\(\).])", '', $filename);
        $filename = mb_ereg_replace("([\.]{2,})", '.', $filename);
        return $filename;
    }

    /**
     * Download the file
     * @see https://stackoverflow.com/a/2882523
     *
     * @param string $filepath
     */
    public static function deliverfile(string $filepath) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        ob_clean();
        flush();
        readfile($filepath);
        exit;
    }

    /**
     * Generate a JWT token with the given content.
     * @param array $content
     * @param bool $raw token string or array for moodle url
     * @param bool $expiration iff true, set the token expiration
     * @return array|string string if $raw is set, array otherwise
     */
    public static function generatetoken(
        array $content = [],
        bool $raw = false,
        bool $expiration = true
    ) {
        $privatekey = jwks_helper::get_private_key();

        $data = [
            'sub' => $content,
            'scope' => 'local_nolej',
        ];

        if ($expiration) {
            $now = time();
            $data['iat'] = $now;
            $data['exp'] = $now + HOURSECS;
        }

        $token = JWT::encode(
            $data,
            $privatekey['key'],
            'RS256',
            $privatekey['kid'],
        );

        return $raw ? $token : ['token' => $token];
    }

    /**
     * Generate a webhook url with token, given the url and timestamp of the request.
     * @param string $shorturl
     * @param int $time
     * @return string
     */
    public static function webhookurl($shorturl, $time) {
        $url = new \moodle_url(
            '/local/nolej/webhook.php',
            self::generatetoken(
                ['url' => $shorturl, 'time' => $time],
                false,
                false
            )
        );
        return $url->out(false);
    }

    /**
     * Decode JWT token and return the data or exit with failure.
     * @return object data
     */
    public function decodetoken() {
        try {

            $token = required_param('token', PARAM_NOTAGS);
            $keys = JWK::parseKeySet(jwks_helper::get_jwks());
            $data = JWT::decode($token, $keys);

            if (!property_exists($data, 'sub')) {
                $this->respondwithmessage(400, 'Request not valid.');
                return (object) [];
            }

            return $data->sub;

        } catch (Exception $e) {

            // Token not valid; exit.
            $this->respondwithmessage(400, 'Request not valid.');
        }

        return (object) [];
    }
}
