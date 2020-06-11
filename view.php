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
 * Prints an instance of mod_notetaker.
 *
 * @package     mod_notetaker
 * @copyright   2020 Jo Beaver <myemail@example.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

$cmid = required_param('id', PARAM_INT); // Course_module id.
$modid  = optional_param('id', 0, PARAM_INT); // Module instance id.

if ($cmid) {
    $cm             = get_coursemodule_from_id('notetaker', $cmid, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $notetaker      = $DB->get_record('notetaker', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($modid) {
    $notetaker      = $DB->get_record('notetaker', array('id' => $modid), '*', MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $notetaker->course), '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('notetaker', $notetaker->id, $notetaker->course, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mod_notetaker'));
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/notetaker:view', $context);

// Completion and trigger events.
notetaker_view($notetaker, $course, $cm, $context);

$url = new moodle_url('/mod/notetaker/view.php', array('id' => $cm->id));

$PAGE->set_url($url);
$PAGE->set_title(format_string($notetaker->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

echo $OUTPUT->heading(format_string($notetaker->name));

$data = [
    'id' => $cmid
]; // TODO get the notes already created. $data = array_values(xxxx_lib::get_notes());

echo $OUTPUT->render_from_template('mod_notetaker/view', $data);

echo $OUTPUT->footer();

