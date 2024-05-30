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
 * Range input
 *
 * @package     local_nolej
 * @author      2024 Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->libdir . '/form/text.php');

/**
 * Range input custom form element
 */
class range_form_element extends HTML_QuickForm_text
{

    /**
     * @var array Range input options
     */
    protected $_options = [
        'min' => 0,
        'max' => 100,
        'step' => 1,
    ];

    /**
     * @var string Element label
     */
    protected $elementlabel = '';

    /**
     * Class constructor
     *
     * @param string $elementname (optional) Input field name attribute
     * @param string $elementlabel (optional) Input field label
     * @param mixed $attributes (optional) Either a typical HTML attribute string or an associative array
     * @param mixed $options (optional) Range input options
     *
     * @return void
     */
    public function __construct($elementname = null, $elementlabel = null, $attributes = null, $options = null)
    {
        parent::__construct($elementname, $elementlabel, $attributes);

        // Hide default label.
        $this->elementlabel = $elementlabel;
        $this->_label = '';

        if ($options != null && is_array($options)) {
            $this->_options = array_merge($this->_options, $options);
        }

        $this->_type = 'range';
    }

    /**
     * Returns the element value.
     * @param array $submitValues
     * @param bool $assoc
     * @param int $nesting
     *
     * @return mixed
     */
    public function exportValue(&$submitvalues, $assoc = false, $nesting = 0)
    {
        $value = parent::exportValue($submitvalues, $assoc, $nesting);
        return $value;
    }

    /**
     * Returns the HTML for the element.
     *
     * @return string
     */
    public function toHtml()
    {
        global $OUTPUT;

        $this->_generateId();

        return $OUTPUT->render_from_template(
            'local_nolej/range_form_element',
            (object) [
                'elementname' => $this->getName(),
                'label' => $this->elementlabel,
                'min' => $this->_options['min'],
                'max' => $this->_options['max'],
                'step' => $this->_options['step'],
                'value' => $this->getValue(),
                'attributes' => $this->_getAttrString($this->_attributes),
                'errormessage' => '',
            ]
        );
    }
}
