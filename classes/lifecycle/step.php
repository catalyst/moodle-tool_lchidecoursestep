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

namespace tool_lchidecoursestep\lifecycle;

global $CFG;
require_once($CFG->dirroot . '/admin/tool/lifecycle/step/lib.php');

use tool_lifecycle\local\response\step_response;
use tool_lifecycle\step\libbase;

defined('MOODLE_INTERNAL') || die();

class step extends libbase {
    public function get_subpluginname()
    {
        return 'tool_lchidecoursestep';
    }

    public function get_plugin_description() {
        return "Hide course";
    }

    public function process_course($processid, $instanceid, $course)
    {
        course_change_visibility($course->id, false);
        return step_response::proceed();
    }

}
