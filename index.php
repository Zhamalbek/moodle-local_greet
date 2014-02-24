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
 * An example of a stand-alone Moodle script.
 *
 * Says Hello, {username}, or Hello {name} if the name is given in the URL.
 *
 * @package   local_greet
 * @copyright 2014 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

require_login();
$context = context_system::instance();
require_capability('local/greet:begreeted', $context);

$name = optional_param('name', '', PARAM_TEXT);
if (!$name) {
    $name = fullname($USER);
}

$standardgreetings = $DB->get_records('local_greet_greetings', array(), 'name');

add_to_log(SITEID, 'local_greet', 'begreeted',
        'local/greet/index.php?name=' . urlencode($name));

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/greet/index.php'),
        array('name' => $name));
$PAGE->set_title(get_string('welcome', 'local_greet'));

echo $OUTPUT->header();
echo $OUTPUT->box(get_string('greet', 'local_greet',
        format_string($name)));

echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('standardgreetings', 'local_greet'));
if ($standardgreetings) {
    echo html_writer::start_tag('ul');
    foreach ($standardgreetings as $data) {
        $link = html_writer::link(
                new moodle_url('/local/greet/index.php', array('name' => $data->name)),
                format_string($data->name));
        $edit = $OUTPUT->action_icon(
                new moodle_url('/local/greet/edit.php', array('id' => $data->id)),
                new pix_icon('t/edit', get_string('edit')));
        $delete = $OUTPUT->action_icon(
                new moodle_url('/local/greet/delete.php', array('id' => $data->id)),
                new pix_icon('t/delete', get_string('delete')));
        echo html_writer::tag('li', "{$link} {$edit} {$delete}");
    }
    echo html_writer::end_tag('ul');
}
echo html_writer::link(new moodle_url('/local/greet/edit.php'),
        get_string('addstandardgreeting', 'local_greet'));
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
