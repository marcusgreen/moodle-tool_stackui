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

namespace tool_stackui\local\hooks\output;
use tool_stackui\stackui;
/**
 * Hook callbacks for tool_stackui
 *
 * @package    tool_stackui
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_standard_footer_html_generation {

    /**
     * Output items at the end of pages
     * The Javascript might be migrated to amd at some point
     *
     * @param \core\hook\output\before_standard_footer_html_generation $hook The hook instance
     * @return void
     */
    public static function callback(\core\hook\output\before_standard_footer_html_generation $hook): void {
        global $DB, $OUTPUT;

        if (!get_config('tool_stackui', 'enabled')) {
            return;
        }

        if (!stackui::in_uicohort()) {
            return;
        }

        global $PAGE;
        $pagetype = $PAGE->pagetype;
        if ($pagetype !== "question-type-stack") {
            return;
        }
        $content = stackui::set_qvar_height();
        $content .= stackui::toggle_checkbox('fitem_id_name', 'Show All');
        $hook->add_html($content);
    }

    /**
     * Creates a replace button with click functionality
     *
     * @return string The HTML and JavaScript for the replace button
     */
    public static function add_find_replace(): string {
        xdebug_break();
        $html = "
            <div class='mt-2'>
                <button type='button' id='replace-btn' class='btn btn-secondary'>
                    Replace
                </button>
            </div>";

        $js = "
            <script>
                const replaceBtn = document.getElementById('replace-btn');
                replaceBtn.addEventListener('click', function() {
                    alert('Hello!');
                });

            function insertAfter(referenceNode, newNode) {
                referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
            }
                debugger;
            const anchor = document.getElementById('id_questiontext');
            insertAfter(anchor, replaceBtn);
            </script>";

        return $html . $js;
    }

}
