SEBTS Print Roster Module
===============
This module creates a course's participants listing in a printable format.
The listing only includes participants who are in the auditor or student roles.


Requirements
------------
Moodle 2.7 or later

Installation
------------
Simply drop the sebtsprintroster folder into the Moodle's {root}/mod/ folder. Then run the admin plugin update check.

This module's visibility is turned off in the {modules} table so that it cannot be added as an activity or resource on the course page.

In order to provide access to this module, a hack must be made in a Moodle core page, {root}\user\index.php.
The following lines of code appears immediately above the section of code which prints the alphabetical first/last initials filters.
The lines between the /* SEBTS ..... */ is the hack that needs to be inserted.
	
	:
	:
	:
	if ($bulkoperations) {
		echo '<form action="action_redir.php" method="post" id="participantsform">';
		echo '<div>';
		echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
		echo '<input type="hidden" name="returnto" value="'.s($PAGE->url->out(false)).'" />';
	}

	/* SEBTS Printable Class List: START */
	if (has_capability('mod/sebtsprintroster:view', $context)) {
		$classlist = new moodle_url('/mod/sebtsprintroster/index.php?id='.$course->id);
		echo '<p><strong>'.$OUTPUT->action_link($classlist, 'Printable Class List');
	}
	/* SEBTS end Printable Class List: END */

	if ($mode === MODE_USERDETAILS) {    // Print simple listing.
		if ($totalcount < 1) {
			echo $OUTPUT->heading(get_string('nothingtodisplay'));
		} else {
			if ($totalcount > $perpage) {

				$firstinitial = $table->get_initial_first();
				$lastinitial  = $table->get_initial_last();
				$strall = get_string('all');
				$alpha  = explode(',', get_string('alphabet', 'langconfig'));

				// Bar of first initials.
	:
	:
	:
