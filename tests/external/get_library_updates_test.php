<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_nolej\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use local_nolej\module;

/**
 * Test script for classes/external/get_library_updates.php.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category    test
 * @coversDefaultClass \local_nolej\external\get_library_updates
 */
class get_library_updates_test extends \externallib_advanced_testcase {

    /**
     * Test the execute function when capabilities are present.
     * @covers ::execute
     */
    public function test_execute(): void {
        global $DB;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Set the required capabilities by the external function.
        $context = \context_system::instance();
        $roleid = $this->assignUserCapability('local/nolej:usenolej', $context->id);

        // Example document ID.
        $documentid = '00000000-abcd-1234-5678-000000000000';

        $DB->insert_record(
            'local_nolej_module',
            (object) [
                'document_id' => $documentid,
                'user_id' => $user->id,
                'tstamp' => time(),
                'status' => module::STATUS_ANALYSIS_PENDING,
                'title' => 'Example module',
                'consumed_credit' => 0,
                'doc_url' => 'http://example.com',
                'media_type' => 'web',
                'automatic_mode' => false,
                'language' => 'en',
            ]
        );
        $DB->insert_record(
            'local_nolej_activity',
            (object) [
                'document_id' => $documentid,
                'user_id' => $user->id,
                'action' => 'transcription',
                'tstamp' => time(),
                'status' => 'ok',
                'code' => 200,
                'error_message' => '',
                'consumed_credit' => 0,
                'notified' => true,
            ]
        );
        $DB->insert_record(
            'local_nolej_activity',
            (object) [
                'document_id' => $documentid,
                'user_id' => $user->id,
                'action' => 'transcription_ok',
                'tstamp' => time(),
                'status' => 'ok',
                'code' => 200,
                'error_message' => '',
                'consumed_credit' => 0,
                'notified' => false,
            ]
        );

        // Call the external service function.
        $returnvalue = get_library_updates::execute([]);

        // Clean return values to simulate the web service server.
        $returnvalue = \external_api::clean_returnvalue(
            get_library_updates::execute_returns(),
            $returnvalue
        );

        // Assert that there was a response.
        $this->assertIsArray($returnvalue);
        $this->assertArrayHasKey('updates', $returnvalue);
        $this->assertIsArray($returnvalue['updates']);
        $this->assertCount(1, $returnvalue['updates']);
        $this->assertEquals($documentid, $returnvalue['updates'][0]['documentid']);

        // Call again the external service function.
        $returnvalue = get_library_updates::execute([]);

        // Clean return values to simulate the web service server.
        $returnvalue = \external_api::clean_returnvalue(
            get_library_updates::execute_returns(),
            $returnvalue
        );

        $this->assertIsArray($returnvalue);
        $this->assertArrayHasKey('updates', $returnvalue);
        $this->assertIsArray($returnvalue['updates']);
        $this->assertEmpty($returnvalue['updates']);
    }

    /**
     * Test the execute function when capabilities are missing.
     * @covers ::execute
     */
    public function test_capabilities_missing(): void {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Call without required capability.
        $this->expectException(\required_capability_exception::class);
        get_library_updates::execute([]);
    }
}
