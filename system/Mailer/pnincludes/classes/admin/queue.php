<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadmin.php 24342 2008-06-06 12:03:14Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_System_Modules
 * @subpackage Mailer
 */

/**
 * mail queue management handler class
 * @author Florian Schießl
 */

class Mailer_queueHandler
{
    function initialize(&$render)
    {
        // security check
        if (!SecurityUtil::checkPermission('Mailer::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        
        // Is there an action to be done?
        // Should a mail be deleted and removed from the queue?
        $delete = (int) FormUtil::getPassedValue('delete');
        if ($delete > 0) {
            // Check for valid auth key
            if (!SecurityUtil::confirmAuthkey()) {
                LogUtil::registerAuthIDError();
            } else {
                $obj = DBUtil::selectObjectByID('mailer_queue',$delete);
                if (!$obj) {
                    LogUtil::registerError(_MAILER_MAIL_LOAD_ERROR);
                } else {
                    $result = DBUtil::deleteObject($obj,'mailer_queue');
                    if ($result) {
                        LogUtil::registerStatus(_MAILER_MAIL_DELETED);
                    } else {
                        LogUtil::registerError(_MAILER_MAIL_LOAD_ERROR);
                    }
                }
            }
   		  	return $render->pnFormRedirect(pnModURL('Mailer','admin','queue'));
        }
        
        // Should a mail be displayed?
        $id = (int) FormUtil::getPassedValue('id');
        if ($id > 0) {
            $result = DBUtil::selectObjectByID('mailer_queue',$id);
            if (!$result) {
                LogUtil::registerError(_MAILER_MAIL_LOAD_ERROR);
       		  	return $render->pnFormRedirect(pnModURL('Mailer','admin','queue'));
            } else {
                $render->assign('mail', $result);
            }
        }
        
        // Should a mail be sent manually
        $send = (int) FormUtil::getPassedValue('send');
        if ($send > 0) {
            if (!SecurityUtil::confirmAuthkey()) {
                LogUtil::registerAuthIDError();
            } else {
                $result = pnModAPIFunc('Mailer','admin','sendQueue',array('id' => $send));
                if (!$result) {
                    LogUtil::registerError(_MAILER_SEND_ERROR);
                } else {
                    LogUtil::registerStatus(_MAILER_MAIL_SENT);
                }
            }
            return $render->pnFormRedirect(pnModURL('Mailer','admin','queue'));
        }
        
        // Get mail queue and parameters
        $mailerpager = (int) FormUtil::getPassedValue('mailerpager');
        if ($mailerpager > 0) {
            $mailerpager--;
        } else {
            $mailerpager = 0;
        }
        $numrows    = 20;
        $sortmode   = (int) FormUtil::getPassedValue('sortmode');
        $queue      = pnModAPIFunc('Mailer','admin','getQueue',array('limitoffset' => $mailerpager, 'numrows' => $numrows, 'sortmode' => $sortmode));
        $queuecount = pnModAPIFunc('Mailer','admin','getQueue',array('countonly' => 1));
        $render->assign('queue',        $queue);
        $render->assign('queuecount',   $queuecount);
        $render->assign('numrows',      $numrows);
        $render->assign('authid',       SecurityUtil::generateAuthKey());
    }
}