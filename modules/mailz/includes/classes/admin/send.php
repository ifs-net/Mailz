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
        $dom = ZLanguage::getModuleDomain('mailz');
        // Get Newsletters
        $newsletters = pnModAPIFunc('mailz', 'common', 'getNewsletters');
 
        $render->assign('newsletters', $newsletters);

        // Should group be loaded?
        $id = (int) FormUtil::getPassedValue('id');
        if ($id > 0) {
            $nl = pnModAPIFunc('mailz', 'common', 'getNewsletters', array('id' => $id));
            if (isset($nl) && ($nl['id'] == $id)) {
                // Load newsletter
                $this->nl = $nl;
                $render->assign($this->nl);
                // Load group relations
                $groups = pnModAPIFunc('mailz', 'common', 'getNewsletterGroups', array('id' => $this->nl['id']));
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
                        LogUtil::registerStatus(__('The newsletter was queued for creation and for sending. You can see the created newsletter in advMailers mail queue.', $dom));
                    } else {
                        LogUtil::registerError(__('An error occured while trying to queue the newsletter for creation.', $dom));
                    }
                }

                // Assign authid
                $render->assign('authid', SecurityUtil::generateAuthKey());

            } else {
                LogUtil::registerError(__('Error loading data', $dom));
            }
        }
		return true;
    }

    function handleCommand(&$render, &$args)
    {
        $dom = ZLanguage::getModuleDomain('mailz');
	    // Security check
	    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
		  	return LogUtil::registerPermissionError();
		}
        if ($args['commandName'] == 'update') {
			// get the pnForm data and do a validation check
		    $obj = $render->pnFormGetValues();
		    if (!$render->pnFormIsValid()) return false;

            // Get username
            $uname   = (string) $obj['uname'];
            $uid     = (int) pnUserGetIDFromName($uname);
            if ($uid > 1) {
                // We will queue the newsletter - the easiest way to send it out...
                $obj = array();
                switch ($this->nl['contenttype']) {
                    case 'c':
                        $queue[] = array(
                            'contenttype'   => 'h',
                            'uid'           => $uid,
                            'nid'           => $this->nl['id']
                        );
                    case 't':
                        $queue[] = array(
                            'contenttype'   => 't',
                            'uid'           => $uid,
                            'nid'           => $this->nl['id']
                        );
                        break;
                    case 'h':
                        $queue[] = array(
                            'contenttype'   => 'h',
                            'uid'           => $uid,
                            'nid'           => $this->nl['id']
                        );
                }
                $result = DBUtil::insertobjectArray($queue,'mailz_queue');
                LogUtil::registerStatus(__('Newsletter was send as a test to the user', $dom) . ' ' . $obj['uname'] . ' (' . $uid . ')');
            } else {
                LogUtil::registerStatus(__('User could not be found', $dom));
            }
			// Redirect
			return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'send', array('id' => $this->nl['id'])));
		}
    }
}
