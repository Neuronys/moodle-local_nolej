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
        global $USER;

        $this->resetAfterTest(true);

        // Set the required capabilities by the external function.
        $context = \context_system::instance();
        $roleid = $this->assignUserCapability('local/nolej:usenolej', $context->id);

        // Call the external service function.
        $returnvalue = get_library_updates::execute([]);

        // We need to execute the return values cleaning process to simulate
        // the web service server.
        $returnvalue = \external_api::clean_returnvalue(
            get_library_updates::execute_returns(),
            $returnvalue
        );

        // Assert that there was a response.
        $this->assertNotNull($returnvalue);
    }

    /**
     * Test the execute function when capabilities are missing.
     * @covers ::execute
     */
    public function test_capabilities_missing(): void {
        // Call without required capability.
        $this->expectException(\required_capability_exception::class);
        get_library_updates::execute([]);
    }
}
