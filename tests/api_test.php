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

require_once(__DIR__ . '/../classes/api.php');

/**
 * Test script for classes/api.php.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category    test
 * @coversDefaultClass \local_nolej\api
 */
class api_test extends \advanced_testcase {

    /**
     * Testing key.
     *
     * @covers ::haskey
     */
    final public function test_key(): void {
        $this->resetAfterTest(true);

        unset_config('api_key', 'local_nolej');
        $this->assertFalse(api::haskey());

        $key = random_string(15);
        set_config('api_key', $key, 'local_nolej');
        $this->assertTrue(api::haskey());
    }

    /**
     * Testing formatfromextension().
     *
     * @covers ::formatfromextension
     */
    final public function test_formatfromextension(): void {
        $this->assertEquals('audio', api::formatfromextension('mp3'));
        $this->assertEquals('video', api::formatfromextension('mp4'));
        $this->assertEquals('document', api::formatfromextension('pdf'));
        $this->assertEquals('freetext', api::formatfromextension('txt'));
        $this->assertNull(api::formatfromextension('unknown'));
    }

    /**
     * Testing data directories.
     *
     * @covers ::datadir
     * @covers ::uploaddir
     * @covers ::h5pdir
     */
    final public function test_directories(): void {
        global $CFG;

        $this->resetAfterTest(true);

        $plugindatadir = $CFG->dataroot . '/local_nolej';

        // Test data dir.
        $datadir = api::datadir();
        $this->assertEquals($plugindatadir, $datadir);
        $this->assertDirectoryIsWritable($datadir);

        // Test upload dir.
        $uploaddir = api::uploaddir();
        $this->assertStringStartsWith($plugindatadir, $uploaddir);
        $this->assertDirectoryIsWritable($uploaddir);

        // Example document ID.
        $documentid = '00000000-abcd-1234-5678-000000000000';

        // Test data dir with document ID.
        $datadir = api::datadir($documentid);
        $this->assertStringStartsWith($plugindatadir, $datadir);
        $this->assertDirectoryIsWritable($datadir);

        // Test h5p dir.
        $h5pdir = api::h5pdir($documentid);
        $this->assertStringStartsWith($datadir, $h5pdir);
        $this->assertDirectoryIsWritable($h5pdir);

        // Clean up.
        rmdir($h5pdir);
        rmdir($datadir);
    }

    /**
     * Testing documents lookup.
     *
     * @covers ::lookupdocumentstatus
     * @covers ::lookupdocumentwithstatus
     */
    final public function test_lookupdocument(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Example document ID.
        $documentid = '00000000-abcd-1234-5678-000000000000';

        $exampledocument = (object) [
            'document_id' => $documentid,
            'user_id' => 1,
            'tstamp' => time(),
            'status' => module::STATUS_ANALYSIS,
            'title' => 'Example module',
            'consumed_credit' => 0,
            'doc_url' => 'http://example.com',
            'media_type' => 'web',
            'automatic_mode' => false,
            'language' => 'en',
        ];

        $DB->insert_record(
            'local_nolej_module',
            $exampledocument,
            false
        );

        $exampledocument = $DB->get_record('local_nolej_module', ['document_id' => $documentid]);

        // Test with no user.
        $documentstatus = api::lookupdocumentstatus($documentid);
        $this->assertEquals(module::STATUS_ANALYSIS, $documentstatus);

        // Test with correct user.
        $documentstatus = api::lookupdocumentstatus($documentid, 1);
        $this->assertEquals(module::STATUS_ANALYSIS, $documentstatus);

        // Test with wrong user.
        $documentstatus = api::lookupdocumentstatus($documentid, 2);
        $this->assertEquals(-1, $documentstatus);

        $api = new api();

        // Test with wrong status.
        $document = $api->lookupdocumentwithstatus($documentid, module::STATUS_CREATION);
        $this->assertFalse($document);

        // Test with correct status.
        $document = $api->lookupdocumentwithstatus($documentid, module::STATUS_ANALYSIS);
        $this->assertEquals($exampledocument, $document);
    }

    /**
     * Testing sanitizefilename().
     *
     * @covers ::sanitizefilename
     */
    final public function test_sanitizefilename(): void {
        $filename = 'file$0/\\123.(..[[=,<>.abc';
        $this->assertEquals('file0123.(.abc', api::sanitizefilename($filename));
    }

    /**
     * Testing token generation.
     *
     * @covers ::generatetoken
     * @covers ::webhookurl
     * @covers ::decodetoken
     */
    final public function test_tokens(): void {
        $nolej = new api();

        $data = ['fileid' => 'examplefile'];

        // Test token as a string.
        $token = api::generatetoken($data, true);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);

        // Test token as an array.
        $token = api::generatetoken($data);
        $this->assertIsArray($token);
        $this->assertArrayHasKey('token', $token);
        $this->assertIsString($token['token']);
        $this->assertNotEmpty($token['token']);

        // Test decode token.
        $decodeddata = $nolej->decodetoken($token['token']);
        $this->assertNotNull($decodeddata);
        $this->assertIsObject($decodeddata);
        $this->assertEquals((object) $data, $decodeddata);

        // Test webhook token.
        $context = \context_system::instance();
        $moduleid = 1;
        $userid = 2;
        $url = api::webhookurl($moduleid, $userid, $context->id);
        $this->assertIsString($url);

        $parts = parse_url($url);
        $this->assertIsArray($parts);
        $this->assertArrayHasKey('query', $parts);

        $query = $parts['query'];
        $this->assertIsString($query);
        $this->assertStringStartsWith('token=', $query);

        $token = substr($query, strlen('token='));
        $this->assertNotEmpty($token);

        // Test decode webhook token.
        $decodeddata = $nolej->decodetoken($token);
        $this->assertNotNull($decodeddata);
        $this->assertIsObject($decodeddata);
        $this->assertEquals((object) ['moduleid' => $moduleid, 'userid' => $userid, 'contextid' => $context->id], $decodeddata);
    }
}
