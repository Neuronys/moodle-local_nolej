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

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

require_once(__DIR__ . '/../classes/form/element/range_form_element.php');

/**
 * Test script for classes/form/element/range_form_element.php.
 *
 * @package     local_nolej
 * @author      Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category    test
 * @coversDefaultClass \range_form_element
 */
final class range_form_element_test extends \advanced_testcase {

    /**
     * Testing input value.
     * @covers ::exportValue
     */
    final public function test_exportValue(): void {
        $min = 1;
        $max = 99;
        $step = 2;
        $element = new \range_form_element(
            'testel',
            null,
            ['min' => $min, 'max' => $max, 'step' => $step]
        );

        // Normal value.
        $value = ['testel' => 3];
        $this->assertEquals(['testel' => 3], $element->exportValue($value));

        // Over the maximum.
        $value = ['testel' => $max + 1];
        $this->assertEquals(['testel' => $max], $element->exportValue($value));

        // Below the minimum.
        $value = ['testel' => $min - 1];
        $this->assertEquals(['testel' => $min], $element->exportValue($value));

        // Floating point value.
        $value = ['testel' => 3.14];
        $this->assertEquals(['testel' => 3], $element->exportValue($value));

        // String value.
        $value = ['testel' => '3.14'];
        $this->assertEquals(['testel' => 3], $element->exportValue($value));

        // String value below minimum.
        $value = ['testel' => '-3.14'];
        $this->assertEquals(['testel' => $min], $element->exportValue($value));

        // String value with alphanumeric chars.
        $value = ['testel' => '3.14blah'];
        $this->assertEquals(['testel' => 3], $element->exportValue($value));

        // String value with alphanumeric chars.
        $value = ['testel' => 'blah'];
        $this->assertEquals(['testel' => $min], $element->exportValue($value));

        // Correct offset value.
        $value = ['testel' => 5];
        $this->assertEquals(['testel' => 5], $element->exportValue($value));

        // Wrong offset value.
        $value = ['testel' => 6];
        $this->assertEquals(['testel' => false], $element->exportValue($value));
    }
}
