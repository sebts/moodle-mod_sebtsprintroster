<?PHP

/**
 * Version information for mod/sebtsprintroster
 *
 * @package    mod
 * @subpackage sebtsprintroster
 * @copyright  2016 SEBTS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This module was first released in Moodle 2.7.
 * It would probably work in earlier version, but why have an earlier Moodle.
 */

$plugin->component = 'mod_sebtsprintroster'; // Full name of the plugin (used for diagnostics)
$plugin->version   = 2016010101;      // The current module version (Date: YYYYMMDDXX)
$plugin->release   = '2.7.0+';         // Human-friendly version name
$plugin->requires  = 2014050600;      // Requires Moodle 2.7
$plugin->maturity  = MATURITY_STABLE; // Alpha development code - not for production sites
