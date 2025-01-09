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

/**
 * Hook callbacks for tool_stackui
 *
 * @package    tool_stackui
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_standard_head_html_generation {

    /**
     * Allows plugins to add any elements to the page &lt;head&gt; html tag.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function callback(\core\hook\output\before_standard_head_html_generation $hook): void {
        global $DB, $OUTPUT, $PAGE;
        $pagetype = $PAGE->pagetype;

        if ($pagetype == "question-type-stack") {
           $PAGE->requires->css('/admin/tool/stackui/amd/src/codemirror/lib/codemirror.css');
        }
        $pagetype = $PAGE->pagetype;
        $content = '';
        if ($pagetype == "question-bank-previewquestion-preview") {
         //   $content = self::set_preview_language();
        }

        $hook->add_html($content);
    }
       /**
     * Generates the JavaScript code for checkbox functionality
     *
     * @param string $elementid The ID of the element to attach the JavaScript to
     * @param string $showall The current show/hide state
     * @return string The JavaScript code as a string
     */
    private static function set_preview_language(): string {
        return "";
        return "
        <script>
        debugger;
                const url = new URL(window.location.href);
                const lang = url.searchParams.get('lang');
                alert(lang);
                if(lang !== 'de') {
                    url.searchParams.append('lang', 'de');
                    window.location.href = url.href;
                }

        </script>";
    }
}
