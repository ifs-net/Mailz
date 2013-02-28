<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class mailz_admin_subscriptionsHandler
{
    var $nl;
    function initialize(&$render)
    {
        $dom = ZLanguage::getModuleDomain('mailz');
	    // Security check
	    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
		  	return LogUtil::registerPermissionError();
		}
        // Should group be loaded?
        $id = (int) FormUtil::getPassedValue('id');
        if ($id > 0) {
            $nl = pnModAPIFunc('mailz', 'common', 'getNewsletters', array('id' => $id, 'inactive' => 1));
            if (isset($nl) && ($nl['id'] == $id)) {
                // Load newsletter
                $this->nl = $nl;
                $render->assign('nl',$this->nl);
                // Do action
                $action = (string) FormUtil::getPassedValue('action');
                if ($action == 'delete') {
                    if (!SecurityUtil::confirmAuthKey()) {
                        LogUtil::registerAuthIDError();
                    } else {
                        // delete Recipient
                        $item = (int) FormUtil::getPassedValue('item');
                        $obj = DBUtil::selectObjectByID('mailz_subscriptions', $item);
                        if (!obj) {
                            LogUtil::registerError(__('Error deleting a subscription', $dom));
                        } else {
                            $result = DBUtil::deleteObject($obj, 'mailz_subscriptions');
                            if ($result) {
                                LogUtil::registerStatus(__('Subscription deleted', $dom));
                            } else {
                                LogUtil::registerError(__('Error deleting a subscription', $dom));
                            }
                        }
                    }
                }
                // Load recipients
                $numrows = 50;
                $limitoffset = (int) FormUtil::getPassedValue('nlpager');
                if ($limitoffset >= 0) {
                    $limitoffset--;
                }
                $render->assign('numrows', $numrows);
                $recipients_total = pnModAPIFunc('mailz', 'common', 'getSubscriptions', array('id' => $this->nl['id'], 'count' => 1 , 'uname' => FormUtil::getPassedValue('uname'), 'email' => FormUtil::getPassedValue('email'), 'unconfirmed' => 1));
                $render->assign('recipients_total', $recipients_total);
                $recipients = pnModAPIFunc('mailz', 'common', 'getSubscriptions', array('id' => $this->nl['id'], 'numrows' => $numrows, 'limitoffset' => $limitoffset, 'uname' => FormUtil::getPassedValue('uname'), 'email' => FormUtil::getPassedValue('email'), 'unconfirmed' => 1));
                $render->assign('recipients', $recipients);
                $authid = SecurityUtil::generateAuthKey();
                $render->assign('authid', $authid);
            } else {
                LogUtil::registerError(__('Error loading data', $dom));
            }
        } else {
            LogUtil::registerError(__('Error loading data', $dom));
            return $render->pnFormRedirect(pnModURL('mailz','admin','newsletters'));
        }
		return true;
    }

    function handleCommand(&$render, &$args)
    {
        $dom = ZLanguage::getModuleDomain('mailz');
        if ($args['commandName'] == 'update') {
			// get the pnForm data and do a validation check
		    $obj = $render->pnFormGetValues();
		    if (!$render->pnFormIsValid()) return false;
		    $args = array(
		      'id'        => $this->nl['id'],
		      'email'     => $obj['email'],
		      'uname'     => $obj['uname'],
		      'nlpager'   => 0
            );
		    return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'subscriptions', $args));
		}
    }
}
