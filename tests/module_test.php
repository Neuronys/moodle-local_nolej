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

namespace local_nolej;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../classes/module.php');

/**
 * Test script for classes/module.php.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category    test
 * @coversDefaultClass \local_nolej\module
 */
final class module_test extends \advanced_testcase {

    /**
     * Testing deletion.
     *
     * @covers ::delete
     */
    final public function test_delete(): void {
        global $DB;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        $document1 = '00000000-abcd-1234-5678-000000000000';
        $document2 = '00000000-abcd-1234-5678-111111111111';

        $module1 = $DB->insert_record(
            'local_nolej_module',
            (object) [
                'document_id' => $document1,
                'user_id' => $user->id,
                'tstamp' => time(),
                'status' => module::STATUS_ANALYSIS,
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
                'document_id' => $document1,
                'user_id' => $user->id,
                'action' => 'transcription',
                'tstamp' => time(),
                'status' => 'ok',
                'code' => 200,
                'error_message' => '',
                'consumed_credit' => 0,
                'notified' => false,
            ]
        );

        $module2 = $DB->insert_record(
            'local_nolej_module',
            (object) [
                'document_id' => $document2,
                'user_id' => $user->id,
                'tstamp' => time(),
                'status' => module::STATUS_ANALYSIS,
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
                'document_id' => $document2,
                'user_id' => $user->id,
                'action' => 'transcription',
                'tstamp' => time(),
                'status' => 'ok',
                'code' => 200,
                'error_message' => '',
                'consumed_credit' => 0,
                'notified' => false,
            ]
        );

        $DB->insert_record(
            'local_nolej_activity',
            (object) [
                'document_id' => $document2,
                'user_id' => $user->id,
                'action' => 'analysis',
                'tstamp' => time(),
                'status' => 'ok',
                'code' => 200,
                'error_message' => '',
                'consumed_credit' => 0,
                'notified' => false,
            ]
        );

        $this->assertCount(2, $DB->get_records('local_nolej_module', ['user_id' => $user->id]));
        $this->assertCount(3, $DB->get_records('local_nolej_activity', ['user_id' => $user->id]));

        // Test deletion for wrong user.
        $success = module::delete($module1, $otheruser->id);
        $this->assertFalse($success);

        // Test deletion.
        $success = module::delete($module1, $user->id);
        $this->assertTrue($success);
        $this->assertCount(1, $DB->get_records('local_nolej_module', ['user_id' => $user->id]));
        $this->assertCount(2, $DB->get_records('local_nolej_activity', ['user_id' => $user->id]));

        // Test module already deleted.
        $success = module::delete($module1, $user->id);
        $this->assertFalse($success);

        // Test deletion.
        $success = module::delete($module2, $user->id);
        $this->assertTrue($success);
        $this->assertEmpty($DB->get_records('local_nolej_module', ['user_id' => $user->id]));
        $this->assertEmpty($DB->get_records('local_nolej_activity', ['user_id' => $user->id]));
    }
}
