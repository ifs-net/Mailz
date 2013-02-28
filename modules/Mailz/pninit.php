<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

 /**
  * initialise the module
  *
  */
function mailz_init() {
    $dom = ZLanguage::getModuleDomain('mailz');

  	// Create tables
    $tables = array (
  		'mailz_groups',
  		'mailz_group_relations',
  		'mailz_newsletters',
  		'mailz_archive',
  		'mailz_subscriptions',
  		'mailz_plugins',
  		'mailz_queue'
		  );
	foreach ($tables as $table) {
		if (!DBUtil::createTable($table)) {
		  	return false;
		}
	}

	// install system init hook
    if (!pnModRegisterHook('zikula', 'systeminit', 'API', 'mailz', 'common', 'systeminit')) {
        LogUtil::registerError(__('Error creating hook', $dom));
        return false;
    }
    pnModAPIFunc('Modules', 'admin', 'enablehooks', array('callermodname' => 'zikula', 'hookmodname' => 'mailz'));

	// Return success
	return true;
}

/**
 * upgrade the module
 *
 * @return       bool       true on success, false otherwise
 */
function mailz_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0':
        default:
    }   
    return true;
}

/**
 * delete the module
 *
 */
function mailz_delete() {
    $dom = ZLanguage::getModuleDomain('mailz');

  	// Drop tables
  	$tables = array (
  		'mailz_group_relations',
  		'mailz_newsletters',
  		'mailz_archive',
  		'mailz_subscriptions',
  		'mailz_plugins',
  		'mailz_groups',
  		'mailz_queue'
		  );
	foreach ($tables as $table) {
		if (!DBUtil::dropTable($table)) {
		  	return false;
		}
	}
	// Delete module variables if there are any
	pnModDelVar('mailz');

    // Disable hook
    pnModAPIFunc('Modules', 'admin', 'disablehooks', array('callermodname' => 'zikula', 'hookmodname' => 'mailz'));

    // Unregister system init hook
    if (!pnModUnregisterHook('zikula', 'systeminit', 'API', 'mailz', 'common', 'systeminit')) {
        LogUtil::registerError(__('Error removing hook', $dom));
        return false;
    }

	// Return success
	return true;
}