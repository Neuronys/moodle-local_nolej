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

namespace local_nolej\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use local_nolej\module;
use local_nolej\api;

class provider_test extends provider_testcase
{

    /**
     * PHPUnit test setup: reset after every test.
     */
    protected function setUp(): void
    {
        $this->resetAfterTest();
    }

    /**
     * Test for provider::get_metadata().
     *
     * @covers ::get_metadata
     */
    public function test_get_metadata()
    {
        $collection = new collection('local_nolej');
        $items = provider::get_metadata($collection)->get_collection();
        $this->assertCount(4, $items);
    }

    /**
     * Test for provider::get_contexts_for_userid().
     *
     * @covers ::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid()
    {
        global $DB;

        $userid = 1;

        // Example document ID.
        $documentid = '00000000-abcd-1234-5678-000000000000';

        $DB->insert_record(
            'nolej_module',
            (object) [
                'document_id' => $documentid,
                'user_id' => $userid,
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
            'nolej_activity',
            (object) [
                'document_id' => $documentid,
                'user_id' => $userid,
                'action' => 'transcription',
                'tstamp' => time(),
                'status' => 'ok',
                'code' => 200,
                'error_message' => '',
                'consumed_credit' => 0,
                'notified' => false,
            ]
        );

        $contextlist = provider::get_contexts_for_userid($userid);
        $contextids = $contextlist->get_contextids();
        $this->assertCount(1, $contextids);
        foreach ($contextlist as $context) {
            $this->assertEquals(CONTEXT_SYSTEM, $context->contextlevel);
        }
    }

    /**
     * Test for provider::export_user_data().
     *
     * @covers ::export_user_data
     */
    public function test_export_user_data()
    {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'local_nolej', $contextlist->get_contextids());

        // Example document ID.
        $documentid = '00000000-abcd-1234-5678-000000000000';

        $DB->insert_record(
            'nolej_module',
            (object) [
                'document_id' => $documentid,
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
            'nolej_activity',
            (object) [
                'document_id' => $documentid,
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

        provider::export_user_data($approvedcontextlist);

        foreach ($contextlist as $context) {
            $writer = writer::with_context($context);
            $this->assertTrue($writer->has_any_data());

            $data = $writer->get_data(['privacy:metadata:nolej_module']);
            $this->assertNotEmpty($data);

            $data = $writer->get_data(['privacy:metadata:nolej_activity']);
            $this->assertNotEmpty($data);
        }
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     *
     * @covers ::delete_data_for_all_users_in_context
     */
    public function test_delete_data_for_all_users_in_context()
    {
        global $DB;

        $context = \context_system::instance();
        $userid = 1;

        // Example document ID.
        $documentid = '00000000-abcd-1234-5678-000000000000';

        $DB->insert_record(
            'nolej_module',
            (object) [
                'document_id' => $documentid,
                'user_id' => $userid,
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
            'nolej_activity',
            (object) [
                'document_id' => $documentid,
                'user_id' => $userid,
                'action' => 'transcription',
                'tstamp' => time(),
                'status' => 'ok',
                'code' => 200,
                'error_message' => '',
                'consumed_credit' => 0,
                'notified' => false,
            ]
        );

        provider::delete_data_for_all_users_in_context($context);

        $this->assertEmpty($DB->get_records('nolej_module'));
        $this->assertEmpty($DB->get_records('nolej_activity'));
    }

    /**
     * Test for provider::delete_data_for_user().
     *
     * @covers ::delete_data_for_user
     */
    public function test_delete_data_for_user()
    {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $context = \context_system::instance();
        $contextlist = new approved_contextlist($user, 'local_nolej', [$context->id]);

        // Example document ID.
        $documentid = '00000000-abcd-1234-5678-000000000000';

        $DB->insert_record(
            'nolej_module',
            (object) [
                'document_id' => $documentid,
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
            'nolej_activity',
            (object) [
                'document_id' => $documentid,
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

        provider::delete_data_for_user($contextlist);

        $this->assertEmpty($DB->get_records('nolej_module', ['user_id' => $user->id]));
        $this->assertEmpty($DB->get_records('nolej_activity', ['user_id' => $user->id]));
    }

    /**
     * Test for provider::get_users_in_context().
     *
     * @covers ::get_users_in_context
     */
    public function test_get_users_in_context()
    {
        global $DB;

        $context = \context_system::instance();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $DB->insert_record(
            'nolej_module',
            (object) [
                'document_id' => '00000000-abcd-1234-5678-000000000000',
                'user_id' => $user1->id,
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
            'nolej_module',
            (object) [
                'document_id' => '00000000-abcd-1234-5678-111111111111',
                'user_id' => $user2->id,
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

        $userlist = new userlist($context, 'local_nolej');
        provider::get_users_in_context($userlist);

        $this->assertCount(2, $userlist->get_userids());
    }

    /**
     * Test for provider::delete_data_for_users().
     *
     * @covers ::delete_data_for_users
     */
    public function test_delete_data_for_users()
    {
        global $DB;

        $context = \context_system::instance();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $DB->insert_record(
            'nolej_module',
            (object) [
                'document_id' => '00000000-abcd-1234-5678-000000000000',
                'user_id' => $user1->id,
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
            'nolej_module',
            (object) [
                'document_id' => '00000000-abcd-1234-5678-111111111111',
                'user_id' => $user2->id,
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
            'nolej_activity',
            (object) [
                'document_id' => '00000000-abcd-1234-5678-111111111111',
                'user_id' => $user2->id,
                'action' => 'transcription',
                'tstamp' => time(),
                'status' => 'ok',
                'code' => 200,
                'error_message' => '',
                'consumed_credit' => 0,
                'notified' => false,
            ]
        );

        $userlist = new approved_userlist($context, 'local_nolej', [$user1->id, $user2->id]);
        provider::delete_data_for_users($userlist);

        $this->assertEmpty($DB->get_records('nolej_module', ['user_id' => $user1->id]));
        $this->assertEmpty($DB->get_records('nolej_activity', ['user_id' => $user2->id]));
    }
}
