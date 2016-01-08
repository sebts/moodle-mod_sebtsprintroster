<?php
require_once("../../config.php");
require_once('lib.php');
global $DB;



$id = required_param('id', PARAM_INT);              // course id
$layoutid = required_param('layoutid', PARAM_INT);  // print layout id

if (empty($id) || empty($layoutid)) {
    print_error('missingparameter');
} else {
    $course = get_course($id);

    $validlayout = $DB->record_exists('sebtsprintroster_layouts', array('id'=>$layoutid));
    if (!$validlayout) {
        print_error('invaliddata');
    }
}


/// Require users to be logged in to use this feature but don't auto login as guest.
require_login($course, false); 

/// Get the course context and ensure that the logged in user has the required permissions
$context = context_course::instance($course->id);
require_capability('mod/sebtsprintroster:view', $context);

$mode = RENDER_MODE_PRINT;
$htmlcontent = create_roster($id, $mode, $layoutid);
echo($htmlcontent);
