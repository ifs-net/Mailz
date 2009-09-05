<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class mailz_admin_sendHandler
{
    var $nl;
    function initialize(&$render)
    {
        // Get Newsletters
        $newsletters = pnModAPIFunc('mailz','common','getNewsletters');
 
        $render->assign('newsletters', $newsletters);

        // Should group be loaded?
        $id = (int) FormUtil::getPassedValue('id');
        if ($id > 0) {
            $nl = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
            if (isset($nl) && ($nl['id'] == $id)) {
                // Load newsletter
                $this->nl = $nl;
                $render->assign($this->nl);
                // Load group relations
                $groups = pnModAPIFunc('mailz','common','getNewsletterGroups',array('id' => $this->nl['id']));
                $render->assign('groups', $groups);
                
                // Should send action be processed?
                $send = (int) FormUtil::getPassedValue('send');
                if ($send == 1) {
                    if (!SecurityUtil::confirmAuthKey()) {
                        LogUtil::registerAuthIDError();
                        return $render->pnFormRedirect(pnModURL('mailz','admin','send',array('id' => $this->nl['id'])));
                    }
                    $result = pnModAPIFunc('mailz','common','queueNewsletter',array('id' => $this->nl['id']));
                    if ($result) {
                        LogUtil::registerStatus(_MAILZ_QUEUED_WAIT);
                    } else {
                        LogUtil::registerError(_MAILT_QUEUE_ERROR);
                    }
                }
                
                // Assign authid
                $render->assign('authid', SecurityUtil::generateAuthKey());
                
            } else {
                LogUtil::registerError(_MAILZ_LOAD_ERROR);
            }
        }
		return true;
    }
    function handleCommand(&$render, &$args)
    {
	    // Security check
	    if (!SecurityUtil::checkPermission('lobby::', '::', ACCESS_ADMIN)) {
		  	return LogUtil::registerPermissionError();
		}
        if ($args['commandName']=='update') {
			// get the pnForm data and do a validation check
		    $obj = $render->pnFormGetValues();
		    if (!$render->pnFormIsValid()) return false;
                        die("todo");

            // Get username
            $uid     = (int) pnUserGetIDFromName($obj['uname']);
            $email   = pnUserGetvar('email', $uid);
            if (($uid > 1)) {
                // Send Newsletter in needed formats
                switch ($this->nl['contenttype']) {
                    case 'c':
                        die("todo");
                        break;
                    case 't':
                        die("todo");
                        break;
                    case 'h':
                        die("todo");
                        break;
                }
                LogUtil::registerStatus(_MAILZ_NL_SENT_TO.' '.$obj['uname'].' ('.$uid.')');
            } else {
                LogUtil::registerStatus(_MAILZ_USER_NOT_FOUND);
            }
			// Redirect
			return $render->pnFormRedirect(pnModURL('mailz','admin','send'));
		}
    }
}
