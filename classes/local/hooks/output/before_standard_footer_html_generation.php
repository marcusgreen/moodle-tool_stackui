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
use html_writer;
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
        global $DB, $OUTPUT, $PAGE;
        $pagetype = $PAGE->pagetype;
        if ($pagetype !== "question-type-stack") {
            return;
        }

        if (!get_config('tool_stackui', 'enabled')) {
            return;
        }
        $PAGE->requires->js_call_amd('tool_stackui/line_numbers', 'init', ['id_questionvariables']);

        $content = '';
        //$content = self::add_language_list();
        if (stackui::in_uicohort()) {
            $content .= stackui::toggle_checkbox('fitem_id_name', 'Show All');
        }
        $content .= stackui::set_qvar_height();
        $content .= stackui::set_monospace_qtext();
        $hook->add_html($content);

    }
    public static function add_language_list() {
        $langs = get_string_manager()->get_list_of_translations();
        $lang = optional_param('lang', 'en', PARAM_TEXT);
        $html = html_writer::select($langs,'stack_lang_menu',$lang,'Choose language');

        $html .= "
                <div class='col-md-9 d-flex flex-wrap align-items-start felement' data-fieldtype='static'>
                    <div id='id_stack_lang_menu' class='d-flex flex-wrap align-items-center'>
                    $html.
                    </div>
                </div>
                <script>
                function insertAfter(referenceNode, newNode) {
                    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
                }
                const langmenu = document.getElementById('id_stack_lang_menu');
                const anchor = document.getElementById('id_updatebutton');

                insertAfter(anchor, langmenu);
                langmenu.addEventListener('click', function(event) {
                  const language = event.target.value;
                  const url = new URL(window.location.href);
                  url.searchParams.delete('lang');
                  url.searchParams.append('lang', language);
                  window.location.href = url.href;
                  event.preventDefault();

                });
                </script>;
        ";
        return $html;
    }

    /**
     * Creates a replace button with click functionality
     *
     * @return string The HTML and JavaScript for the replace button
     */
    public static function add_find_replace(): string {
        $html = "
            <div class='mt-2'>
                <button type='button' id='

        replace-btn' class='btn btn-secondary'>
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
                referenceNode.parentNode.insertAfter(newNode, referenceNode.nextSibling);
            }
                debugger;
            const anchor = document.getElementById('id_questiontext');
            insertAfter(anchor, replaceBtn);
            </script>";

        return $html . $js;
    }

}
