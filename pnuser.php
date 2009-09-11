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
 * the main user function
 * 
 * @return       output
 */
function mailz_user_main()
{
	// Create output and call handler class
	$render = pnRender::getInstance('mailz');

    // Get newsletters
    $nls = pnModAPIFunc('mailz','common','getNewsletters',array('subscribable' => 1));
    $authid = SecurityUtil::generateAuthKey();
    $newsletters = array();
    foreach ($nls as $nl) {
        if (pnUserLoggedIn()) {
            $nl['subscribed'] = (int) pnModAPIFunc('mailz','common','isSubscribed',array('id' => $nl['id'], 'uid' => pnUserGetVar('uid'))); 
        }
        if ($nl['subscribable'] == 1) {
            if ($nl['public'] == 1) {
                $newsletters[] = $nl;
            } else {
                if (pnUserLoggedIn()) {
                    $newsletters[] = $nl;
                }
            }
        }
    }

    // Assign to template
    $render->assign('newsletters', $newsletters);
    $render->assign('authid',      $authid);
    
    if (pnUserLoggedIn()) {
        $uname = pnUserGetVar('uname');
        $email = pnUserGetVar('email');
        $render->assign('email', $email);
    } else {
        $uname = _MAILZ_GUEST;
    }
    $render->assign('uname', $uname);
    $render->assign('uid',   pnUserGetVar('uid'));

    // Get archived newsletters
   	$newsletters = pnModAPIFunc('mailz','common','getNewsletters',array('archived' => 1));
   	if (count($newsletters > 0)) {
   	    $render->assign('usearchive', 1);
    }


    // Return the output
    return $render->fetch('mailz_user_main.htm');
}

/**
 * let users subscribe to newsletters
 * 
 * @return       output
 */
function mailz_user_subscribe()
{
    // Get Parameters
    $contenttype = (string) FormUtil::getPassedValue('contenttype');
    $action      = (string) FormUtil::getPassedValue('action');
    $email       = (string) FormUtil::getPassedValue('email');
    $id          = (int)    FormUtil::getPassedValue('id');
    if (!($id > 0) ||  ( ( ($action != 'subscribe') && ($action != 'unsubscribe') && ($action != 'update')) )) {
        LogUtil::registerError(_MAILZ_NL_LOAD_ERROR);
        return pnRedirect(pnModURL('mailz'));
    }

    // Get newsletter
    $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
    if (!$newsletter || ($newsletter['id'] != $id)) {
        LogUtil::registerError(_MAILZ_NL_LOAD_ERROR);
    }
    
    // Do action now
    $args = array();
    $args['id'] = $id;
    if (pnUserGetVar('uid') > 1) {
        $args['uid'] = pnUserGetVar('uid');
        $args['contenttype'] = $contenttype;
    } else {
        $args['email'] = $email;
    }
    
    // Check for possible subscription
    $subscribed = pnModAPIFunc('mailz','common','isSubscribed',$args);
    if ($subscribed && ($action == 'subscribe')) {
        LogUtil::registerError(_MAILZ_ALREADY_SUBSCRIBED);
        return pnRedirect(pnModURL('mailz'));
    }
    if ( (!$subscribed && ($action == 'unsubscribe')) ||
         (!$subscribed && ($action == 'update'))
        ) {
        LogUtil::registerError(_MAILZ_NOT_SUBSCRIBED_YET);
        return pnRedirect(pnModURL('mailz'));
    }
    
    if ($action == 'subscribe') {
        $result = pnModAPIFunc('mailz','common','subscribe',$args);
        if (pnUserLoggedIn()) {
            LogUtil::registerStatus(_MAILZ_SUBSCRIBED);
        } else {
            LogUtil::registerStatus(_MAILZ_CONFIRMATION_SENT);
        }
    } else if ($action == 'update') {
        $result = pnModAPIFunc('mailz','common','update',$args);
        LogUtil::registerStatus(_MAILZ_SUBSCRIPTION_UPDATED);
    } else {
        $result = pnModAPIFunc('mailz','common','unsubscribe',$args);
    }
    
    if (!$result) {
        LogUtil::registerError(_MAILZ_ACTION_FAILURE);
    } 
    // redirect to main management page
    return pnRedirect(pnModURL('mailz'));
}

/**
 * function for url cronjob calls
 * 
 * @return       output
 */
function mailz_user_cron() 
{

    $pwd = (string) FormUtil::getPassedValue('pwd');
    $id  = (int)    FormUtil::getPassedValue('id');
    
    // Get newsletter
    $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
    if (!$newsletter) {
        print "NEWSLETTER INVALID";
    } else {
        if ($newsletter['croncode'] != $pwd) {
            print "CRON PWD WRONG";
        } else {
            if ($newsletter['croncode'] == '') {
                print "CRON FOR NEWSLETTER INACTIVE";
            } else {
                // Start sending
                $result = pnModAPIFunc('mailz','common','queueNewsletter',array('id' => $newsletter['id']));
                if ($result) {
                    print "NEWSLETTER QUEUED FOR SENDING!";
                } else {
                    print "ERROR QUEUING NEWSLETTER";
                }
            }
        }
    }
    return true;
}

/**
 * confirm a newsletter subscription
 *
 * @return output
 */
function mailz_user_c()
{
    $i = (int) FormUtil::getPassedValue('i');
    $c = (int) FormUtil::getPassedValue('code');
    $obj = DBUtil::selectObjectByID('mailz_subscriptions',$i);
    if (($obj['id'] != $i) || ($obj['code'] != $c) || ($obj['confirmed'] != 0)) {
        LogUtil::registerError(_MAILZ_VALIDATION_CODE_INVALID);
    } else {
        $obj['confirmed'] = 1;
        $obj['code'] = '';
        DBUtil::updateObject($obj,'mailz_subscriptions');
        LogUtil::registerStatus(_MAILZ_SUBSCRIPTION_VALIDATED);
    }
    return pnRedirect(pnModURL('mailz'));
}

/**
 * confirm newsletter unsubscription
 *
 * @return output
 */
function mailz_user_d()
{
    $i = (int) FormUtil::getPassedValue('i');
    $c = (int) FormUtil::getPassedValue('code');
    $obj = DBUtil::selectObjectByID('mailz_subscriptions',$i);
    if (($obj['id'] != $i) || ($obj['code'] != $c)) {
        LogUtil::registerError(_MAILZ_VALIDATION_CODE_INVALID);
    } else {
        $result = DBUtil::deleteObject($obj,'mailz_subscriptions');
        if ($result) {
            LogUtil::registerStatus(_MAILZ_UNSUBSCRIPTION_DONE);
        } else {
            LogUtil::registerError(_MAILZ_UNSUBSCRIPTION_ERROR);
        }
    }
    return pnRedirect(pnModURL('mailz'));
}

/**
 * show newsletter archive
 *
 * @return output
 */
function mailz_user_archive()
{
    // Security check
    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_OVERVIEW)) {
        return LogUtil::registerPermissionError();      
    }

	// Create output and call handler class
	$render = pnRender::getInstance('mailz');
	
    // Paging for archive:
    $limitoffset = (int) FormUtil::getPassedValue('nlpager');
    if ($limitoffset >= 0) {
        $limitoffset--;
    }
    $numrows = 30;
    
    // If a special newsletter is chosen load data
    $id = (int) FormUtil::getPassedValue('id');
    if ($id > 0) {
        $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
        if ($newsletter) {
            $render->assign('newsletter', $newsletter);
            $item = (int) FormUtil::getPassedValue('item');
            if ($item > 0) {
                $item = pnModAPIFunc('mailz','common','getArchivedNewsletter',array('id' => $item));
                if (!$item || ($item['nid'] != $newsletter['id'])) {
                    return LogUtil::registerPermissionError();
                } else {
                    $render->assign('archivenewsletter', $item);
                }
            }
            // get Archive
            $archive       = pnModAPIFunc('mailz','common','getArchivedNewsletter',array('nid' => $id, 'limitoffset' => $limitoffset, 'numrows' => $numrows));
            $archive_total = pnModAPIFunc('mailz','common','getArchivedNewsletter',array('nid' => $id, 'count' => 1));
            $render->assign('archive',       $archive);
            $render->assign('archive_total', $archive_total);
            $render->assign('numrows',       $numrows);
        }
    }
    if (!$newsletter) {
        // Assign all newsletters otherwise for overview
    	$newsletters = pnModAPIFunc('mailz','common','getNewsletters',array('archived' => 1));
        $render->assign('newsletters', $newsletters);
    }
    
    // Return the output
    return $render->fetch('mailz_user_archive.htm');
   
}