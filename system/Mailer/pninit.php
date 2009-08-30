<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pninit.php 24342 2008-06-06 12:03:14Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_System_Modules
 * @subpackage Mailer
 */

/**
 * initialise the template module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @author Florian Schießl, Mark West
 * @return bool true if successful, false otherwise
 */
function Mailer_init()
{
	// install system init hook
    if (!pnModRegisterHook('zikula', 'systeminit', 'API', 'Mailer', 'user', 'systeminit')) {
        LogUtil::registerError(_ERRORCREATINGHOOK);
        return false;
    }

    // Create tables
  	$tables = array (
  		'mailer_queue',
  		'mailer_errorlog'
		  );
	foreach ($tables as $table) {
		if (!DBUtil::createTable($table)) {
		  	return false;
		}
	}

	// Create indexes
    if (!DBUtil::createIndex('priorityindex', 'mailer_queue', array('priority'))) {
        LogUtil::registerError(_CREATEINDEXFAILED);
        return false;
    }

    // Set default module variables
    pnModSetVar('Mailer', 'mailertype', 1);
    pnModSetVar('Mailer', 'charset', _CHARSET);
    pnModSetVar('Mailer', 'encoding', '8bit');
    pnModSetVar('Mailer', 'html', false);
    pnModSetVar('Mailer', 'wordwrap', 50);
    pnModSetVar('Mailer', 'msmailheaders', false);
    pnModSetVar('Mailer', 'sendmailpath', '/usr/sbin/sendmail');
    pnModSetVar('Mailer', 'smtpauth', false);
    pnModSetVar('Mailer', 'smtpserver', 'localhost');
    pnModSetVar('Mailer', 'smtpport', 25);
    pnModSetVar('Mailer', 'smtptimeout', 10);
    pnModSetVar('Mailer', 'smtpusername', '');
    pnModSetVar('Mailer', 'smtppassword', '');
    pnModSetVar('Mailer', 'queuetype', 1);
    pnModSetVar('Mailer', 'queuecronpwd', rand(1000000000,9999999999));
    pnModSetVar('Mailer', 'queuefrequency', 20);

    // Initialisation successful
    return true;
}

/**
 * upgrade the template module from an old version
 * This function can be called multiple times
 * @author Florian Schießl, Mark West
 * @param int $oldversion version to upgrade from
 * @return bool true if successful, false otherwise
 */
function Mailer_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        // version 1.0 shipped with PN .76x
        case 1.0:
            $contenttype = pnModGetVar('Mailer', 'contenttype');
            if ($contenttype == 'text/plain') {
                pnModSetVar('Mailer', 'html', false);
            } else {
                pnModSetVar('Mailer', 'html', true);
            }
            pnModDelVar('Mailer', 'contenttype');
        case 1.1:
            // Nothing to do
        case 1.2:
            // Register new module variables for new functions
            pnModSetVar('Mailer', 'queuetype', 1);
            pnModSetVar('Mailer', 'queuecronpwd', rand(1000000000,9999999999));
            pnModSetVar('Mailer', 'queuefrequency', 20);
            // Create tables
          	$tables = array (
          		'mailer_queue',
          		'mailer_errorlog'
        		  );
        	foreach ($tables as $table) {
        		if (!DBUtil::createTable($table)) {
        		  	return false;
        		}
        	}
        	// Create db indexes
            if (!DBUtil::createIndex('priorityindex', 'mailer_queue', array('priority'))) {
                LogUtil::registerError(_CREATEINDEXFAILED);
                return false;
            }
        	// install system init hook
            if (!pnModRegisterHook('zikula', 'systeminit', 'API', 'Mailer', 'user', 'systeminit')) {
                LogUtil::registerError(_ERRORCREATINGHOOK);
                return false;
            }
    }
    // Update successful
    return true;
}

/**
 * delete the Mailer module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @author Mark West
 * @return bool true if successful, false otherwise
 */
function Mailer_delete()
{
    // delete the system init hook
    $oldqueuetype = (int) pnModGetVar('Mailer','queuetype');
    if ($oldqueuetype == 3) {
        // Disable hook now
        pnModAPIFunc('Modules', 'admin', 'disablehooks', array('callermodname' => 'zikula', 'hookmodname' => 'Mailer'));
    }
    if (!pnModUnregisterHook('zikula', 'systeminit', 'API', 'Mailer', 'user', 'systeminit')) {
        LogUtil::registerError(_ERRORDELETINGHOOK);
        return false;
    }
    // Delete tables
  	$tables = array (
  		'mailer_queue',
  		'mailer_errorlog'
		  );
	foreach ($tables as $table) {
		if (!DBUtil::dropTable($table)) {
		  	return false;
		}
	}
    // Delete any module variables
    pnModDelVar('Mailer');

    // Deletion successful
    return true;
}
