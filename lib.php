<?php
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/sebtsprintroster/locallib.php');

function create_roster($courseid,$rendermode,$layoutid) {
    global $DB, $USER;

    define("PIX_PATH", get_config('sebtsprintroster', 'pictureurl'));
    define("NO_PIX_FOUND",get_config('sebtsprintroster', 'missingimgurl'));
    define("PIX_FILETYPE", get_config('sebtsprintroster', 'picturefiletype'));

    /// Default to something sensible so that we don't error if something
    /// goes wrong while trying to get the layout configuration
    $colsperpage = 1;
    $rowsperpage = 5;
    $height = '150px';
    $width = '150px';
    $fontsize = '1.5em';
    $printdirection = PRINT_DIRECTION_ACROSS_THEN_DOWN;
    $layoutdescription = "";
    $layoutrec = $DB->get_record('sebtsprintroster_layouts', array('id'=>$layoutid), '*', MUST_EXIST);
    if ($layoutrec) {
        $colsperpage = $layoutrec->cols;
        $rowsperpage = $layoutrec->rows;
        $height = $layoutrec->imageheight . 'px';
        $width = $layoutrec->imagewidth . 'px';
        $fontsize = $layoutrec->fontsize . 'em';
        $printdirection = $layoutrec->printdirection;
        $layoutdescription = $layoutrec->description;
    }


    $currRow = 0;                       //Track the location of the next table cell to be drawn
    $currCol = 0;                       //Track the location of the next table cell to be drawn
    $openTableTag = false;              //Indicates whether the closing </table> tag is needed
    $openTRowTag = false;               //Indicates whether the closing </tr> tag is needed
    $tdwidth = 100 / $colsperpage;      //Divides the table columns into equal widths
    $pagescompleted = 0;                //Count the number of tables drawn
    $content = "";                      //Return variable containing HTML for the student roster listed in a number of tables

    $sql = "SELECT usr.username AS sid"
         . "     , usr.firstname"
         . "     , usr.lastname"
         . "     , rol.name AS participanttype"
         . "     , usr.city"
         . "     , crs.fullname AS coursetitle"
         . "  FROM       {user}             usr"
         . "  INNER JOIN {role_assignments} ras ON usr.id=ras.userid"
         . "  INNER JOIN {role}             rol ON ras.roleid=rol.id AND rol.name IN ('Student','Auditor')"
         . "  INNER JOIN {context}          ctx ON ras.contextid=ctx.id AND ctx.contextlevel=" . CONTEXT_COURSE
         . "  INNER JOIN {course}           crs ON ctx.instanceid=crs.id"
         . "  INNER JOIN {user_enrolments}  enr ON ras.userid=enr.userid AND ras.itemid=enr.enrolid and enr.status=0"		 
         . " WHERE crs.id=$courseid"
         . " ORDER BY rol.name, usr.lastname, usr.firstname;";
    $studentslisting = $DB->get_recordset_sql($sql);

    /// The recordset object only allows forward sequential scrolling through the recordset. This would not work if the roster
    /// is to be listed alphabetically down the page then across to the next table column (PRINT_DIRECTION_DOWN_THEN_ACROSS).
    /// So dump the results into an array so that navigation to specific records can be achieved.
    $result[] = null;
    while ($studentslisting->valid()){
        $result[]=$studentslisting->current();
        $studentslisting->next();
    }
    $studentslisting->close();

    if (count($result) > 1)
    {
        $targetrsid = 0;
        $coursetitle = $result[1]->coursetitle;
        $content =  "";
        if ($rendermode == RENDER_MODE_PRINT) {
            $content = "<html><head><title>$coursetitle</title>"
                     . "<style type='text/css'>"
                     . " table {page-break-after:always; width:100%; font-size:$fontsize; border:2px solid black; border-collapse:collapse;} "
                     . " .pb {page-break-after:always;} "
                     . "</style> "
                     . "</head><body onload='window.print()'>";
        }

        /// The CSS above ensures that each physical page contains only table.
        /// The FOR loop below creates as many tables necessary to contain the number of students in the classs.
        /// The number of columns and the number of rows specified on the layout record dictates how the table is to be drawn.
        /// 
        /// In a multi-column table, without any special handling, the recordset would be read sequentially and the table would
        /// be drawn one row at a time with the cells going across the row. The number of times to iterate throught the FOR loop
        /// would be exactly equal to the number of records to be printed. The student listing would be read alphabetically left
        /// to right, across the first row then down to the next row. That decribes the idea of PRINT_DIRECTION_ACROSS_THEN_DOWN.
        /// PRINT_DIRECTION_ACROSS_THEN_DOWN, 3 x 3 table ex:
        ///     <table>
        ///         <tr><td> A </td><td> B </td><td> C </td></tr>
        ///         <tr><td> D </td><td> E </td><td> &nbsp; </td></tr>
        ///         <tr><td> &nbsp; </td><td> &nbsp; </td><td> &nbsp; </td></tr>
        ///     </table>
        /// 
        /// To accommodate PRINT_DIRECTION_DOWN_THEN_ACROSS, printing the listing with the students listed alphabetically down the
        /// first column of a table then back the top of the table to next column, the FOR loop must be iterated the number of times
        /// necessary to draw the entire table (that is the rowsperpage times the colsperpage as defined by the layout record).
        /// However since the table is still being drawn one row at a time with the cells going across the row, the recordset cannot
        /// be access sequentially. Instead the $targetrsid calculation determines the next record to be printed, stricky on the basis
        /// of the table size and the number of tables (pages) already drawn. Where the value calulated is beyond the number of records
        /// returned from the database, then an empty cell is printed in its place.
        /// PRINT_DIRECTION_DOWN_THEN_ACROSS, 3 x 3 table ex:
        ///     <table>
        ///         <tr><td> A </td><td> D </td><td> &nbsp; </td></tr>
        ///         <tr><td> B </td><td> E </td><td> &nbsp; </td></tr>
        ///         <tr><td> C </td><td> &nbsp; </td><td> &nbsp; </td></tr>
        ///     </table>

        $numofrecsreturned = count($result);
        $looplimit = ($printdirection == PRINT_DIRECTION_ACROSS_THEN_DOWN ? $numofrecsreturned : ceil($numofrecsreturned / ($rowsperpage * $colsperpage)) * $rowsperpage * $colsperpage);

        for ($rsid = 1; $rsid < $looplimit; $rsid++) 
        {
            if ($currCol == 0 && $currRow == 0) {
                if ($rendermode == RENDER_MODE_PRINT) {
                    /// No need for table style here because it will be defined in the CSS loaded into $content above
                    $content .= "<table><tr>";
                } else {
                    /// Provide a description of the layout and simulate on screen as best we can what the output on paper may look like.
                    $content .= "<br />$layoutdescription<br /><br />$coursetitle<br />";
                    $content .= "<table style='width:850px; height:1100px; font-size:$fontsize; border:2px solid black; border-collapse:collapse;'><tr>";
                }
                $openTableTag = true;
                $openTRowTag = true;
                $currCol = 1;
                $currRow = 1;
            }
            
            if ($currCol == 0) {
                $content .= '<tr>';
                $openTRowTag = true;
                $currCol = 1;
                $currRow++;
            }

            if ($printdirection == PRINT_DIRECTION_ACROSS_THEN_DOWN) {
                $targetrsid = $rsid;
            } else {
                $targetrsid = (($currCol - 1) * $rowsperpage) + $currRow + ($pagescompleted * $rowsperpage * $colsperpage);
            }

            $cellData = "&nbsp;";
            if ($targetrsid < $numofrecsreturned)
            {
                $row = $result[$targetrsid];
                $cellData = "<img align='left' height='$height' width='$width' style='margin-right:12px; padding:8px;' alt='$row->sid' src='"
                          . PIX_PATH . $row->sid . PIX_FILETYPE . "' "
                          . "onerror='if (this.src != \"" . NO_PIX_FOUND . "\") this.src = \"" . NO_PIX_FOUND ."\";' />"
                          . "<br /><strong>$row->firstname $row->lastname</strong><br />"
                          . "$row->participanttype<br />$row->city<br />";

            }
            $content .= "<td style='padding: 3px; width: $tdwidth%;'>" . $cellData . "</td>";

            if ($currCol < $colsperpage) {
                $currCol++;
            } else {
                $content .= '</tr>';
                $openTRowTag = false;
                $currCol = 0;
                if ($currRow == $rowsperpage) {
                    $content .= "</table>";
                    $openTableTag = false;
                    if ($rendermode == RENDER_MODE_PRINT) {
                        $currRow = 0;
                        $currCol = 0;
                        $pagescompleted++;
                    } else {
                        /// Break out of the FOR loop early since only the first table is needed
                        /// in preview mode there is no need to process the rest of the list.
                        break;
                    }
                }
            }
        }

        /// Close out any open tags to make sure the HTML is complete.
        if ($openTableTag) {
            if ($openTRowTag){
                while ($currCol < ($colsperpage+1)) {
                    $content .= "<td>&nbsp;</td>";
                    $currCol++;
                }
                $content .= '</tr>';
            }
           $content .= "</table>";
        }

        if ($rendermode == RENDER_MODE_PRINT) {
            $content .= "</body></html>";
        }

        /// Save or update the user preference if the roster is being printed
        if ($rendermode == RENDER_MODE_PRINT) {
            set_layout_preference($USER->id, $courseid, $layoutid);
        }
        
    }

    return $content;
}

function get_layout_options() {
    global $DB;

    $table='sebtsprintroster_layouts';
    $fields='id, name';
    $where = null;
    $sort = 'id';
    $options = $DB->get_records_menu($table, $where, $sort, $fields);

    return $options;
}

function get_default_layout($userid, $courseid) {
    global $DB;

    $table='sebtsprintroster_preferences';
    $field='layoutid';
    $where = array('userid'=>$userid, 'courseid'=>$courseid);

    /// Get the user's preferred layout for a particular course if it exists
    $layoutid = $DB->get_field($table, $field, $where);
    if (!$layoutid) {
        /// Set to the site default layout if no user preference found
        $layoutid=get_config('sebtsprintroster', 'defaultlayoutid');
    }
    return $layoutid;
}

function set_layout_preference($userid, $courseid, $layoutid) {
    global $DB;

    /// Check if a preference record exists for the user/course
    $table = 'sebtsprintroster_preferences';
    $where = array('userid'=>$userid,'courseid'=>$courseid);
    $preferencerec = $DB->get_record($table, $where);

    if ($preferencerec) {
        /// Update the existing preference record with the layout used and the current timestamp
        $preferencerec->layoutid = $layoutid;
        $preferencerec->lastused = time();
        $DB->update_record($table, $preferencerec);
    } else {
        $record = new stdClass();
        $record->userid = $userid;
        $record->courseid = $courseid;
        $record->layoutid = $layoutid;
        $record->lastused = time();
        $DB->insert_record($table, $record);
    }

}