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
 * @package    mod
 * @subpackage sebtsprintroster
 * @copyright  2016 SEBTS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configtext('sebtsprintroster/pictureurl', 'Picture URL', 'Enter the full URL to location of student picture files', ''));
$settings->add(new admin_setting_configtext('sebtsprintroster/picturefiletype', 'Picture Filetype', 'Enter the file extension of students\' picture files.', '.jpg'));
$settings->add(new admin_setting_configtext('sebtsprintroster/missingimgurl', 'Missing Image URL', 'Enter the full URL of the image to be displayed in place of a missing student\'s picture.', 'http://{moodlerootURLgoeshere}/mod/sebtsprintroster/pix/anonymous.svg'));
$settings->add(new admin_setting_configtext('sebtsprintroster/defaultlayoutid', 'Site Default Layout ID', 'Enter the ID of a layout configuration to be used if no layout preferences is found for a user.', '1'));
