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

namespace tool_stackui;

/**
 * Class stackui utility class
 *
 * @package    tool_stackui
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stackui {
    /**
     * Creates a toggle checkbox with associated JavaScript functionality
     *
     * @param string $elementid The ID of the element to attach the checkbox to
     * @param string $checkboxlabel The label text for the checkbox
     * @return string The complete HTML and JavaScript for the toggle checkbox
     */
    public static function toggle_checkbox(string $elementid, string $checkboxlabel): string {
        $showall = optional_param('showall', '', PARAM_TEXT);
        $checkedstatus = '';

        if ($showall === "true") {
            $checkedstatus = "checked=true";
            $checkboxlabel = get_string('showall', 'tool_stackui');
        }

        $html = self::get_checkbox_html($elementid, $checkboxlabel, $checkedstatus);
        $js = self::get_checkbox_javascript($elementid, $showall);

        $content = $html . $js;

        if ($showall === '') {
            $content .= self::hide_elements();
        }

        return $content;
    }
    /**
     * Generates the HTML markup for the checkbox
     *
     * @param string $elementid The ID of the element to attach the checkbox to
     * @param string $checkboxlabel The label text for the checkbox
     * @param string $checkedstatus The checked status of the checkbox
     * @return string The HTML markup for the checkbox
     */
    private static function get_checkbox_html(string $elementid, string $checkboxlabel, string $checkedstatus): string {
        return "
        <div>
            <div id='cbx_{$elementid}' class='custom-control custom-switch'>
                <input type='checkbox' {$checkedstatus} name='xsetmode' class='custom-control-input' data-initial-value='on'>
                <span class='custom-control-label'>{$checkboxlabel}</span>
            </div>";
    }
    /**
     * Generates the JavaScript code for checkbox functionality
     *
     * @param string $elementid The ID of the element to attach the JavaScript to
     * @param string $showall The current show/hide state
     * @return string The JavaScript code as a string
     */
    private static function get_checkbox_javascript(string $elementid, string $showall): string {
        return "
        <script>
            const header = document.getElementById('{$elementid}');
            const cbx = document.getElementById('cbx_{$elementid}');

            cbx.addEventListener('click', function(event) {
                const url = new URL(window.location.href);
                if('{$showall}' === 'true') {
                    url.searchParams.delete('showall');
                } else {
                    url.searchParams.append('showall', 'true');
                }
                window.location.href = url.href;
                event.preventDefault();
            });

            function insertAfter(referenceNode, newNode) {
                referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
            }

            insertAfter(header, cbx);
        </script>";
    }
    /**
     * Hide elements with javascript
     *
     * @return string The CSS and JavaScript to hide elements
     */
    public static function hide_elements(): string {
        global $DB, $OUTPUT;
        $config = get_config('tool_stackui', 'elementstohide');
        $array = explode(',', $config);
        $trimmedarray = array_map('trim', $array);

        $tohide = array_filter($trimmedarray, function($value) {
            return $value !== '';
        });

        $content = '<style>';
        foreach ($tohide as $element) {
            $content .= PHP_EOL;
            $content .= '#'.$element. '{'.PHP_EOL;
            $content .= 'display:none;'.PHP_EOL;
            $content .= '}'.PHP_EOL;
        }
        $content .= '</style>';

        if (in_array('id_fixdollars', $tohide)) {
            $content .= "<script>
            const element = document.getElementById('id_fixdollars');
            const ancestor = element.closest('.mb-3');
            ancestor.style.display = 'none';
            </script>";
        }

        $msg = 'Some elements are hidden for simplification based on you being in cohort '.get_config('tool_stackui', 'uicohort');
        \core\notification::add($msg, \core\notification::WARNING);
        return $content;
    }
    /**
     * Check if the user is in the UI cohort
     *
     * @return array Array of cohort records the user belongs to
     */
    public static function in_uicohort(): array {
        global $DB, $USER;
        $incohort = [];
        $uicohort = get_config('tool_stackui', 'uicohort');
        $cache = \cache::make('tool_stackui', 'stackuicache');

        if (($incohort = $cache->get('incohort')) === false) {
            $sql = "SELECT * FROM {cohort} co
                    JOIN {cohort_members} cm
                    ON co.id = cm.cohortid
                    WHERE cm.userid = :userid
                    AND co.name = :uicohort";
            $incohort = $DB->get_records_sql($sql, ['userid' => $USER->id, 'uicohort' => $uicohort]);
            $cache->set('incohort', $incohort);
        }

        return $incohort;
    }

    /**
     * Sets the height of the question variables textarea field
     * Gets the configured height from settings and applies it via JavaScript
     *
     * @return string JavaScript content to set the height or empty string if no height configured
     */
    public static function set_qvar_height(): string {
        $content = "";
        $qvarheight = get_config('tool_stackui', 'qvarheight');
        if ($qvarheight !== '') {
            $qvarheight = "$qvarheight.em";
            $content = "
                <script>
                var varea = document.getElementById('id_questionvariables');
                varea.setAttribute('style', 'height: $qvarheight');
                </script>
            ";
        }
        return $content;
    }


}
