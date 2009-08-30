<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2009, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_System_Modules
 * @subpackage Mailer
 */

/**
 * initialise tables
 * @author Florian Schießl
 * @return bool true if successful, false otherwise
 */
function Mailer_pntables()
{
    // Initialise table array
    $table = array();

	// Get Table Prefix
    $mailer_queue    = DBUtil::getLimitedTablename('mailer').'_queue';
    $mailer_log      = DBUtil::getLimitedTablename('mailer').'_log';
    $mailer_errorlog = DBUtil::getLimitedTablename('mailer').'_errorlog';

    $table['mailer_queue']    = $mailer_queue;
	$table['mailer_errorlog'] = $mailer_errorlog;

    // Columns for tables
    // Table for mail queue
    $table['mailer_queue_column'] = array (
    			'id'         => 'id',
    			'try'        => 'try',
    			'priority'   => 'priority',
    			'date'       => 'date',
    			'content'    => 'content'
    			);
    $table['mailer_queue_column_def'] = array (
    			'id'         => "I AUTOINCREMENT PRIMARY",
    			'try'        => "I(1) NOTNULL DEFAULT 0",
    			'priority'   => "I(1) NOTNULL DEFAULT 5",
    			'date'       => "T NOTNULL DEFAULT 0",
    			'content'    => "XL NOTNULL'"
    			);
    // Table for error log
    $table['mailer_errorlog_column'] = array (
    			'id'         => 'id',
    			'priority'   => 'priority',
    			'date'       => 'date',
    			'content'    => 'content'
    			);
    $table['mailer_errorlog_column_def'] = array (
    			'id'         => "I PRIMARY",
    			'priority'   => "I(1) NOTNULL DEFAULT 5",
    			'date'       => "T NOTNULL DEFAULT 0",
    			'content'    => "XL NOTNULL'"
    			);

	// Return table information
	return $table;
}
