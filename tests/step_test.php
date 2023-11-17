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
 * Trigger test for end date delay trigger.
 *
 * @package    tool_lchidecoursestep
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lchidecoursestep\tests;

use tool_lifecycle\action;
use tool_lifecycle\local\entity\trigger_subplugin;
use tool_lifecycle\local\manager\process_manager;
use tool_lifecycle\local\manager\trigger_manager;
use tool_lifecycle\local\manager\workflow_manager;
use tool_lifecycle\processor;

defined('MOODLE_INTERNAL') || die();

/**
 * Trigger test for start date delay trigger.
 *
 * @package    tool_lchidecoursestep
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class step_test extends \advanced_testcase {
    /** Icon of the manual trigger. */
    const MANUAL_TRIGGER1_ICON = 't/up';

    /** Display name of the manual trigger. */
    const MANUAL_TRIGGER1_DISPLAYNAME = 'Up';

    /** Capability of the manual trigger. */
    const MANUAL_TRIGGER1_CAPABILITY = 'moodle/course:manageactivities';

    /** @var trigger_subplugin $trigger Instances of the triggers under test. */
    private $trigger;

    /** @var array $course Instance of the course under test. */
    private $course;

    public function setUp() : void {
        global $USER;

        // We do not need a sesskey check in these tests.
        $USER->ignoresesskey = true;

        // Create manual workflow.
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_lifecycle');
        $triggersettings = new \stdClass();
        $triggersettings->icon = self::MANUAL_TRIGGER1_ICON;
        $triggersettings->displayname = self::MANUAL_TRIGGER1_DISPLAYNAME;
        $triggersettings->capability = self::MANUAL_TRIGGER1_CAPABILITY;
        $manualworkflow = $generator->create_manual_workflow($triggersettings);

        // Trigger.
        $this->trigger = trigger_manager::get_triggers_for_workflow($manualworkflow->id)[0];

        // Step.
        $generator->create_step("instance1", "tool_lchidecoursestep", $manualworkflow->id);

        // Course.
        $this->course = $this->getDataGenerator()->create_course();

        // Activate the workflow.
        workflow_manager::handle_action(action::WORKFLOW_ACTIVATE, $manualworkflow->id);
    }

    /**
     * Test course is hidden.
     */
    public function test_hide_course() {
        $this->resetAfterTest(true);

        // Course is visible.
        $course = get_course($this->course->id);
        $this->assertEquals(1, $course->visible);

        // Hide course.
        process_manager::manually_trigger_process($this->course->id, $this->trigger->id);
        $processor = new processor();
        $processor->process_courses();

        // Course is hidden.
        $course = get_course($this->course->id);
        $this->assertEquals(0, $course->visible);
    }

}
