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

namespace local_nolej\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use local_nolej\module;

require_once("$CFG->libdir/externallib.php"); // Required for Moodle 4.1.

/**
 * Get library updates service.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2025 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preview extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        // No parameters needed.
        return new external_function_parameters([
            'documentid' => new external_value(PARAM_ALPHANUMEXT, 'Nolej document ID'),
        ]);
    }

    /**
     * Fetch and returns updates regarding the Nolej modules.
     * @param string $documentid The Nolej document ID.
     * @return array of link and error message
     */
    public static function execute(string $documentid): array {
        global $DB, $USER;

        // Perform security checks.
        $params = self::validate_parameters(self::execute_parameters(), ['documentid' => $documentid]);

        // Check content bank url.
        $contentbankurl = module::getcontentbankurl($documentid);
        if ($contentbankurl) {
            // Return the module preview link.
            return ['link' => (string) $contentbankurl, 'message' => ''];
        }

        // Return the error message.
        return ['link' => '', 'message' => get_string('statuspreviewnotavailable', 'local_nolej'),];
    }

    /**
     * Return description of method result value
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'link' => new external_value(PARAM_URL, 'Link to the preview page of activities'),
            'message' => new external_value(PARAM_TEXT, 'Error message'),
        ]);
    }
}
