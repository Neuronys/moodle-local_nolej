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
 * Nolej webhook
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.RequireLogin.Missing

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/nolej/classes/api.php');

use local_nolej\api;

$nolej = new api();
$data = $nolej->decodetoken();

// Looking for a file
if (property_exists($data, 'fileid')) {
    $filename = $data->fileid;
    $dir = property_exists($data, 'documentid') ? api::datadir($data->documentid) : api::uploaddir();
    $filepath = $dir . '/' . $filename;

    if (file_exists($filepath) && is_file($filepath)) {
        // Delivering file.
        api::deliverfile($filepath);
    }

    // File not found.
    $nolej->respondwithmessage(400, 'Request not valid.');
    exit;
}

// Parse POST data.
if (property_exists($data, 'url') && property_exists($data, 'time')) {
    // Check module existence.
    $module = $DB->get_record(
        'local_nolej_module',
        [
            'doc_url' => $data->url,
            'tstamp' => $data->time,
        ]
    );

    if ($module == null) {
        // Module not found.
        $nolej->respondwithmessage(400, 'Request not valid.');
        exit;
    }

    $nolej->parse();
}

// Request not valid.
$nolej->respondwithmessage(400, 'Request not valid.');
