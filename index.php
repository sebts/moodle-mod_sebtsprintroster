<?php
require_once("../../config.php");
require_once('lib.php');

$id = required_param('id', PARAM_INT);                 // course id
$layoutid = optional_param('layoutid', 0, PARAM_INT);  // print layout id
$mode = RENDER_MODE_PREVIEW;

if (!empty($id)) {
    $course = get_course($id);
} else {
    print_error('missingparameter');
}

/// Require users to be logged in to use this feature but don't auto login as guest.
require_login($course, false); 

/// Get the course context and ensure that the logged in user has the required permissions
$context = context_course::instance($course->id);
require_capability('mod/sebtsprintroster:view', $context);


/// Print the header
$PAGE->set_url('/mod/sebtsprintroster/index.php', array('id'=>$id,'mode'=>$mode));
$PAGE->set_title('Course: ' . $course->fullname);
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(false);
$PAGE->set_pagelayout('incourse');


if (empty($layoutid) || $layoutid <= 0 ) {
    $layoutid = get_default_layout($USER->id,$course->id);
}
$roster = create_roster($course->id,$mode,$layoutid);
$reposturl = new moodle_url("$CFG->wwwroot/mod/sebtsprintroster/index.php?id=$id");
$printurl = new moodle_url("$CFG->wwwroot/mod/sebtsprintroster/print.php?id=$id&layoutid=$layoutid");
$layoutoptions = get_layout_options();

echo $OUTPUT->header();

echo $OUTPUT->box(get_string('previewinstructions', 'sebtsprintroster'));

echo "<br />";
echo '<div class="previewrosterform">';
echo '<label for="previewrosterform_jump">Print Layout:&nbsp;</label>';
echo $OUTPUT->single_select($reposturl, 'layoutid', $layoutoptions, $layoutid, null, 'previewrosterform');
echo "&nbsp;<a href=$printurl target='_blank' class='btn'>Send to printer</a>";
echo '</div>';


echo "<hr style='border-width: 8px;' />" . get_string('sampleoutputheading', 'sebtsprintroster');
echo $roster;

echo $OUTPUT->footer();
