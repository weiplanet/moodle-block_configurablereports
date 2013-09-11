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
 * This file keeps track of upgrades to the ltiprovider plugin
 *
 * @package    local
 * @subpackage ltiprovider
 * @copyright  2011 Juan Leyva <juanleyvadelgado@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_configurable_reports_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011040103) {

        $table = new xmldb_table('block_configurable_reports_report');
        $dbman->rename_table($table, 'block_configurable_reports');
        upgrade_plugin_savepoint(true, 2011040103, 'block', 'configurable_reports');
    }

    // Migrate depricated MOODLE_22 branch and all its tables
    // Into new 2.5 single table architecture
    if ($oldversion < 2013091101) {
        $table = new xmldb_table('block_cr_component');
        if ($dbman->table_exists($table)) {
            $status = $dbman->drop_table($table, true, false);
        }

        $table = new xmldb_table('block_cr_plugin');
        if ($dbman->table_exists($table)) {
            $status = $dbman->drop_table($table, true, false);
        }

        $table = new xmldb_table('block_cr_report');
        if ($dbman->table_exists($table)) {
            $status = $dbman->drop_table($table, true, false);
        }

        $table = new xmldb_table('block_configurable_reports');
        if (!$dbman->table_exists($table)) {
            $dbman->install_from_xmldb_file($CFG->dirroot . '/blocks/configurable_reports/db/install.xml');
        }
        upgrade_block_savepoint(true, 2013091101, 'configurable_reports');
    }

//    if ($oldversion < 2013090706) {
//
//        $table = new xmldb_table('block_configurable_reports');
//        $field = new xmldb_field('lastexecutiontime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', null);
//
//        if (!$dbman->field_exists($table, $field)) {
//            $dbman->add_field($table, $field);
//        }
//
//        upgrade_plugin_savepoint(true, 2013090706, 'block', 'configurable_reports');
//    }
    return true;
}