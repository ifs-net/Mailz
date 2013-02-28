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
 * Populate tables array for the module
 *
 * @return       array       The table information.
 */
function mailz_pntables()
{
    // Initialise table array
    $table = array();

	// Get Table Prefix
    $mailz_groups = DBUtil::getLimitedTablename('mailz').'_groups';
    $mailz_group_relations = DBUtil::getLimitedTablename('mailz').'_group_relations';
    $mailz_newsletters = DBUtil::getLimitedTablename('mailz').'_newsletters';
    $mailz_archive = DBUtil::getLimitedTablename('mailz').'_archive';
    $mailz_subscriptions = DBUtil::getLimitedTablename('mailz').'_subscriptions';
    $mailz_plugins = DBUtil::getLimitedTablename('mailz').'_plugins';
    $mailz_queue = DBUtil::getLimitedTablename('mailz').'_queue';

    $table['mailz_groups'] = $mailz_groups;
    $table['mailz_group_relations'] = $mailz_group_relations;
    $table['mailz_newsletters'] = $mailz_newsletters;
    $table['mailz_archive'] = $mailz_archive;
    $table['mailz_subscriptions'] = $mailz_subscriptions;
    $table['mailz_plugins'] = $mailz_plugins;
    $table['mailz_queue'] = $mailz_queue;

    // Columns for tables
    // Groups table
    $table['mailz_groups_column'] = array (
        'id'            => 'id',
        'title'         => 'title',
        'description'   => 'description',
        'query'         => 'query',
        'api'           => 'api',
        'inactive'      => 'inactive'
    );
    $table['mailz_groups_column_def'] = array (
        'id'            => "I AUTOINCREMENT PRIMARY",
        'title'         => "XL NOTNULL",
        'description'   => "XL NOTNULL",
        'query'         => "XL NOTNULL",
        'api'           => "XL NOTNULL",
        'inactive'      => "I(1) NOTNULL DEFAULT 0"
    );
    // Group newsletter relations table
    $table['mailz_group_relations_column'] = array (
        'id'            => 'id',
        'nid'           => 'nid',
        'gid'           => 'gid'
    );
    $table['mailz_group_relations_column_def'] = array (
        'id'            => "I AUTOINCREMENT PRIMARY",
        'nid'           => "I NOTNULL DEFAULT 0",
        'gid'           => "I NOTNULL DEFAULT 0"
    );
    // Newsletter tables
    $table['mailz_newsletters_column'] = array (
        'id'            => 'id',
        'title'         => 'title',
        'description'   => 'description',
        'archive'       => 'archive',
        'contenttype'   => 'contenttype',
        'subscribable'  => 'subscribable',
        'croncode'      => 'croncode',
        'adddate'       => 'adddate',
        'inactive'      => 'inactive',
        'delay'         => 'delay',
        'last'          => 'last',
        'fromaddress'   => 'fromaddress',
        'serialnumber'  => 'serialnumber',
        'ignoretemplates' => 'ignoretemplates',
        'public'        => 'public',
        'priority'      => 'priority'
    );
    $table['mailz_newsletters_column_def'] = array (
        'id'            => "I AUTOINCREMENT PRIMARY",
        'title'         => "XL NOTNULL",
        'description'   => "XL NOTNULL",
        'archive'       => "I(1) NOTNULL DEFAULT 0",
        'contenttype'   => "C(1) NOTNULL DEFAULT 'c'",
        'subscribable'  => "I(1) NOTNULL DEFAULT 0",
        'croncode'      => "XL NOTNULL",
        'adddate'       => "I(1) NOTNULL DEFAULT 0",
        'inactive'      => "I(1) NOTNULL DEFAULT 0",
        'delay'         => "I NOTNULL DEFAULT 0",
        'last'          => "T NOTNULL DEFAULT '0000-00-00 00:00:00'",
        'fromaddress'   => "XL NOTNULL",
        'serialnumber'  => "I NOTNULL DEFAULT 0",
        'ignoretemplates' => "I(1) NOTNULL DEFAULT 0",
        'public'        => "I(1) NOTNULL DEFAULT 0",
        'priority'      => "I NOTNULL DEFAULT 8"
    );
    // Archive table
    $table['mailz_archive_column'] = array (
        'id'            => 'id',
        'nid'           => 'nid',
        'recipients'    => 'recipients',
        'subject'       => 'subject',
        'body_html'     => 'body_html',
        'body_text'     => 'body_text',
        'date'          => 'date',
        'public'        => 'public'
    );
    $table['mailz_archive_column_def'] = array (
        'id'            => "I AUTOINCREMENT PRIMARY",
        'nid'           => "I NOTNULL DEFAULT 0",
        'recipients'    => "I NOTNULL DEFAULT 0",
        'subject'       => "XL NOTNULL",
        'body_html'     => "XL NOTNULL",
        'body_text'     => "XL NOTNULL",
        'date'          => "T NOTNULL DEFAULT '0000-00-00 00:00:00'",
        'public'        => "I(1) NOTNULL DEFAULT 0"
    );
    // queue table
    $table['mailz_queue_column'] = array (
        'id'            => 'id',
        'nid'           => 'nid',
        'uid'           => 'uid',
        'contenttype'   => 'contenttype',
        'email'         => 'email'
    );
    $table['mailz_queue_column_def'] = array (
        'id'            => "I AUTOINCREMENT PRIMARY",
        'nid'           => "I NOTNULL DEFAULT 0",
        'uid'           => "I NOTNULL DEFAULT 0",
        'contenttype'   => "C(1) NOTNULL DEFAULT 'c'",
        'email'         => "XL NOTNULL"
    );
    // Subcription entries table
    $table['mailz_subscriptions_column'] = array (
        'id'            => 'id',
        'nid'           => 'nid',
        'uid'           => 'uid',
        'date'          => 'date',
        'ip'            => 'ip',
        'email'         => 'email',
        'confirmed'     => 'confirmed',
        'code'          => 'code',
        'contenttype'   => 'contenttype'
    );
    $table['mailz_subscriptions_column_def'] = array (
        'id'            => "I AUTOINCREMENT PRIMARY",
        'nid'           => "I NOTNULL DEFAULT 0",
        'uid'           => "I NOTNULL DEFAULT 0",
        'date'          => "T NOTNULL DEFAULT '0000-00-00 00:00:00'",
        'ip'            => "C(30) NOTNULL DEFAULT ''",
        'email'         => "C(125) NOTNULL DEFAULT ''",
        'confirmed'     => "I(1)$ NOTNULL DEFAULT 0",
        'code'          => "C(15) NOTNULL DEFAULT ''",
        'contenttype'   => "C(1) NOTNULL DEFAULT 'c'"
    );
    // Plugins table
    $table['mailz_plugins_column'] = array (
        'id'            => 'id',
        'nid'           => 'nid',
        'position'      => 'position',
        'title'         => 'title',
        'header_html'   => 'header_html',
        'header_text'   => 'header_text',
        'footer_html'   => 'footer_html',
        'footer_text'   => 'footer_text',
        'params'        => 'params',
        'module'        => 'module',
        'pluginid'      => 'pluginid',
        'inactive'      => 'inactive'
    );
    $table['mailz_plugins_column_def'] = array (
        'id'            => "I AUTOINCREMENT PRIMARY",
        'nid'           => "I NOTNULL DEFAULT 0",
        'position'      => "I NOTNULL DEFAULT 0",
        'title'         => "XL NOTNULL",
        'header_html'   => "XL NOTNULL",
        'header_text'   => "XL NOTNULL",
        'footer_html'   => "XL NOTNULL",
        'footer_text'   => "XL NOTNULL",
        'params'        => "XL NOTNULL",
        'module'        => "XL NOTNULL",
        'pluginid'      => "I NOTNULL DEFAULT 0",
        'inactive'      => "I(1) NOTNULL DEFAULT 0"
    );
	// Return table information
	return $table;
}
