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
 * Module creation form
 *
 * @package     local_nolej
 * @author      2024 Vincenzo Padula <vincenzo@oc-group.eu>
 * @copyright   2024 OC Open Consulting SB Srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nolej\form;

defined('MOODLE_INTERNAL') || die();

require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->dirroot . '/local/nolej/classes/api.php');

class creation extends \moodleform
{

    public function definition()
    {
        global $CFG, $OUTPUT;

        $mform = $this->_form;

        // Document title
        $mform->addElement('text', 'title', get_string('title', 'local_nolej'), 'style="width:100%;"');
        $mform->setType('title', PARAM_NOTAGS);
        $mform->addElement('static', 'titledesc', '', get_string('titledesc', 'local_nolej'));

        // Select source
        $mform->addGroup(
            [
                $mform->createElement('radio', 'sourcetype', '', get_string('sourcetypefile', 'local_nolej'), 'file', ''),
                $mform->createElement('radio', 'sourcetype', '', get_string('sourcetypeweb', 'local_nolej'), 'web', ''),
                $mform->createElement('radio', 'sourcetype', '', get_string('sourcetypetext', 'local_nolej'), 'text', ''),
            ],
            'sourcetypegroup',
            get_string('sourcetype', 'local_nolej'),
            [' '],
            false
        );
        $mform->setDefault('sourcetype', 'file');

        $mform->addElement(
            'static',
            'sourcelimits',
            get_string('limitcontent', 'local_nolej'),
            $OUTPUT->render_from_template(
                'local_nolej/contentlimits',
                (object) [
                    'audioformats' => join(', ', \local_nolej\api\api::TYPE_AUDIO),
                    'videoformats' => join(', ', \local_nolej\api\api::TYPE_VIDEO),
                    'docformats' => join(', ', \local_nolej\api\api::TYPE_DOC),
                ]
            )
        );

        // Source: file
        $mform->addElement(
            'filepicker',
            'sourcefile',
            get_string('sourcefile', 'local_nolej'),
            null,
            [
                'maxbytes' => 500000,
                'accepted_types' => join(',', \local_nolej\api\api::allowedtypes()),
            ]
        );
        $mform->hideIf('sourcefile', 'sourcetype', 'neq', 'file');

        // Source: web
        $mform->addElement('text', 'sourceurl', get_string('sourceurl', 'local_nolej'), 'style="width:100%;"');
        $mform->setType('sourceurl', PARAM_URL);
        $mform->addElement('static', 'sourceurldesc', '', get_string('sourceurldesc', 'local_nolej'));

        $mform->addElement(
            'select',
            'sourceurltype',
            get_string('sourceurltype', 'local_nolej'),
            [
                'audio' => get_string('sourceaudio', 'local_nolej'),
                'video' => get_string('sourcevideo', 'local_nolej'),
                'web' => get_string('sourceweb', 'local_nolej'),
            ]
        );

        $mform->hideIf('sourceurl', 'sourcetype', 'neq', 'web');
        $mform->hideIf('sourceurltype', 'sourcetype', 'neq', 'web');

        // Source: text
        $mform->addElement(
            'editor',
            'sourcetext',
            get_string('sourcefreetext', 'local_nolej'),
            null,
            [
                'maxfiles' => 0,
                'maxbytes' => 0,
                'trusttext' => false,
                'context' => null,
                'collapsed' => true,
                'canUseHtmlEditor' => false,
            ]
        );
        $mform->setType('sourcetext', PARAM_CLEANHTML);
        $mform->hideIf('sourcetext', 'sourcetype', 'neq', 'text');

        // Language
        $languages = $this->getlanguages();
        $mform->addElement(
            'select',
            'language',
            get_string('language', 'local_nolej'),
            [
                'en' => $languages['en'],
                'fr' => $languages['fr'],
                'it' => $languages['it'],
                'de' => $languages['de'],
                'pt' => $languages['pt'],
                'es' => $languages['es'],
                'nl' => $languages['nl'],
            ]
        );
        $mform->addElement('static', 'languagedesc', '', get_string('languagedesc', 'local_nolej'));

        $this->add_action_buttons(true, get_string('create', 'local_nolej'));
    }

    public function validation($data, $files)
    {
        return [];
    }

    /**
     * Get all available languages as an associative array
     * @return array
     */
    protected function getlanguages()
    {
        $controller = new \tool_langimport\controller();
        $availablelangs = $controller->availablelangs;
        $languages = [];
        foreach ($availablelangs as $alang) {
            $languages[$alang[0]] = $alang[2];
        }
        return $languages;
    }
}
