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
 * the main admin function
 * 
 * @return       output
 */
function mailz_admin_main()
{
  /*
    $where = "MyProfile_forenbutler != ''";
    $res = DBUtil::selectObjectArray('myprofile',$where);
    $obj = array();
    foreach ($res as $i) {
        $obj[] = array(
            'nid' => 9,
            'uid' => $i['id'],
            'confirmed' => 1,
            'contenttype' => $i['forenbutler'][0]
        );
    }
//    DBUtil::insertObjectArray($obj,'mailz_subscriptions');
  */
    // Security check
    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();      
    }

  	// load handler class
	Loader::requireOnce('modules/mailz/includes/classes/admin/main.php');

	// Create output and call handler class
	$render = FormUtil::newpnForm('mailz');

    // Return the output
    return $render->pnFormExecute('mailz_admin_main.htm', new mailz_admin_mainHandler());
}

/**
 * group management function
 * 
 * @return       output
 */
function mailz_admin_groups()
{
    // Security check
    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();      
    }

  	// load handler class
	Loader::requireOnce('modules/mailz/includes/classes/admin/groups.php');

	// Create output and call handler class
	$render = FormUtil::newpnForm('mailz');

    // Return the output
    return $render->pnFormExecute('mailz_admin_groups.htm', new mailz_admin_groupsHandler());
}

/**
 * group management function
 * 
 * @return       output
 */
function mailz_admin_send()
{
    // Security check
    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();      
    }

  	// load handler class
	Loader::requireOnce('modules/mailz/includes/classes/admin/send.php');

	// Create output and call handler class
	$render = FormUtil::newpnForm('mailz');

    // Return the output
    return $render->pnFormExecute('mailz_admin_send.htm', new mailz_admin_sendHandler());
}

/**
 * group management function
 * 
 * @return       output
 */
function mailz_admin_plugins()
{
    // Security check
    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();      
    }

  	// load handler class
	Loader::requireOnce('modules/mailz/includes/classes/admin/plugins.php');

	// Create output and call handler class
	$render = FormUtil::newpnForm('mailz');

    // Return the output
    return $render->pnFormExecute('mailz_admin_plugins.htm', new mailz_admin_pluginsHandler());
}

/**
 * newsletter management function
 * 
 * @return       output
 */
function mailz_admin_newsletters()
{
    // Security check
    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();      
    }

  	// load handler class
	Loader::requireOnce('modules/mailz/includes/classes/admin/newsletters.php');

	// Create output and call handler class
	$render = FormUtil::newpnForm('mailz');

    // Return the output
    return $render->pnFormExecute('mailz_admin_newsletters.htm', new mailz_admin_newslettersHandler());
}

/**
 * subscription management function
 * 
 * @return       output
 */
function mailz_admin_subscriptions()
{
    // Security check
    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();      
    }

  	// load handler class
	Loader::requireOnce('modules/mailz/includes/classes/admin/subscriptions.php');

	// Create output and call handler class
	$render = FormUtil::newpnForm('mailz');

    // Return the output
    return $render->pnFormExecute('mailz_admin_subscriptions.htm', new mailz_admin_subscriptionsHandler());
}

/**
 * preview newsleter
 *
 * @param   $args['id']             newsletter id
 * @param   $args['contenttype']    h or t for html or text
 * @return  output
 */
function mailz_admin_preview()
{
    // Security check
    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();      
    }

    // Get parameters
    $id          = (int)    FormUtil::getPassedValue('id');
    $contenttype = (string) FormUtil::getPassedValue('contenttype');

    $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
    if (!$newsletter) {
        print "newsletter could not be loaded";
        return true;
    }
    
    // Proceed with newsletter
    $uid = pnUserGetVar('uid');
    $output = pnModAPIFunc('mailz','common','getNewsletterOutput',array('id' => $id, 'contenttype' => $contenttype, 'uid' => $uid));
    if ($contenttype == 't') {
        print "<pre>";
        print $output;
        print "</pre>";
    } else {
        print $output;
    }
    return true;
}