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
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/nolej/classes/api.php');

use moodle_url;
use core\output\notification;

require_login();
$context = context_system::instance();
require_capability('local/nolej:usenolej', $context);

$moduleid = optional_param('moduleid', null, PARAM_ALPHANUMEXT);

if ($moduleid == null) {
    // Document not set. Go to library.
    redirect(new moodle_url('/local/nolej/manage.php'));
}

$document = $DB->get_record(
    'local_nolej_module',
    [
        'id' => $moduleid,
        'user_id' => $USER->id,
    ]
);

if (!$document) {
    // Document does not exist.
    redirect(
        new moodle_url('/local/nolej/manage.php'),
        get_string('modulenotfound', 'local_nolej'),
        null,
        notification::NOTIFY_ERROR
    );
}

$DB->delete_records(
    'local_nolej_module',
    ['id' => $moduleid]
);

if (!empty($document->document_id)) {
    $DB->delete_records(
        'local_nolej_activity',
        ['document_id' => $document->document_id]
    );
}

redirect(
    new moodle_url('/local/nolej/manage.php'),
    get_string('moduledeleted', 'local_nolej'),
    null,
    notification::NOTIFY_SUCCESS
);
