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

require_once($CFG->dirroot . '/mod/sebtsprintroster/locallib.php');

function xmldb_sebtsprintroster_install() {
    global $DB;

    /// Initialize the lookup table with the pre-configured print layout options
    $record = new stdClass;

    $record->name = '5 per page/single column';
    $record->description = 'Print roster with larger-sized picture of participants in a single column, listing 5 participants on a page.';
    $record->cols = 1;
    $record->rows = 5;
    $record->printdirection = PRINT_DIRECTION_ACROSS_THEN_DOWN;
    $record->imageheight = 150;
    $record->imagewidth = 150;
    $record->fontsize = 1.5;
    $DB->insert_record('sebtsprintroster_layouts', $record);

    $record->name = '8 per page/single column';
    $record->description = 'Print roster with medium-sized picture of participants in a single column, listing 8 participants on a page.';
    $record->cols = 1;
    $record->rows = 8;
    $record->printdirection = PRINT_DIRECTION_ACROSS_THEN_DOWN;
    $record->imageheight = 85;
    $record->imagewidth = 85;
    $record->fontsize = 1.0;
    $DB->insert_record('sebtsprintroster_layouts', $record);

    $record->name = '14 per page/two columns/print down then across';
    $record->description = 'Print roster with medium-sized picture of participants in two columns, listing 14 participants on a page.'
                         . '<br />' . get_string('print_direction_downthenacross','sebtsprintroster');
    $record->cols = 2;
    $record->rows = 7;
    $record->printdirection = PRINT_DIRECTION_DOWN_THEN_ACROSS;
    $record->imageheight = 95;
    $record->imagewidth = 95;
    $record->fontsize = 1.0;
    $DB->insert_record('sebtsprintroster_layouts', $record);

    $record->name = '14 per page/two columns/print across then down';
    $record->description = 'Print roster with medium-sized picture of participants in two columns, listing 14 participants on a page.'
                         . '<br />' . get_string('print_direction_acrossthendown','sebtsprintroster');
    $record->cols = 2;
    $record->rows = 7;
    $record->printdirection = PRINT_DIRECTION_ACROSS_THEN_DOWN;
    $record->imageheight = 95;
    $record->imagewidth = 95;
    $record->fontsize = 1.0;
    $DB->insert_record('sebtsprintroster_layouts', $record);


    $record->name = '30 per page/three columns/print down then across';
    $record->description = 'Print roster with small-sized picture of participants in three columns, listing 30 participants on a page.'
                         . '<br />' . get_string('print_direction_downthenacross','sebtsprintroster');
    $record->cols = 3;
    $record->rows = 10;
    $record->printdirection = PRINT_DIRECTION_DOWN_THEN_ACROSS;
    $record->imageheight = 65;
    $record->imagewidth = 65;
    $record->fontsize = .85;
    $DB->insert_record('sebtsprintroster_layouts', $record);

    $record->name = '30 per page/three columns/print across then down';
    $record->description = 'Print roster with small-sized picture of participants in three columns, listing 30 participants on a page.'
                         . '<br />' . get_string('print_direction_acrossthendown','sebtsprintroster');
    $record->cols = 3;
    $record->rows = 10;
    $record->printdirection = PRINT_DIRECTION_ACROSS_THEN_DOWN;
    $record->imageheight = 65;
    $record->imagewidth = 65;
    $record->fontsize = .85;
    $DB->insert_record('sebtsprintroster_layouts', $record);
    /// Disable this module so that it's not available to be added as a resource on a course
    $DB->set_field('modules', 'visible', 0, array('name'=>'sebtsprintroster'));
}
