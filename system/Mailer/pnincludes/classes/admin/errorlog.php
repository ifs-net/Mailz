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
 * mail queue (error logfile) management handler class
 * @author Florian Schießl
 */

class Mailer_errorlogHandler
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
                $obj = DBUtil::selectObjectByID('mailer_errorlog',$delete);
                if (!$obj) {
                    LogUtil::registerError(_MAILER_MAIL_LOAD_ERROR);
                } else {
                    $result = DBUtil::deleteObject($obj,'mailer_errorlog');
                    if ($result) {
                        LogUtil::registerStatus(_MAILER_MAIL_DELETED);
                    } else {
                        LogUtil::registerError(_MAILER_MAIL_LOAD_ERROR);
                    }
                }
            }
   		  	return $render->pnFormRedirect(pnModURL('Mailer','admin','errorlog'));
        }
        
        // Should a mail be displayed?
        $id = (int) FormUtil::getPassedValue('id');
        if ($id > 0) {
            $result = DBUtil::selectObjectByID('mailer_errorlog',$id);
            if (!$result) {
                LogUtil::registerError(_MAILER_MAIL_LOAD_ERROR);
       		  	return $render->pnFormRedirect(pnModURL('Mailer','admin','errorlog'));
            } else {
                $render->assign('mail', $result);
            }
        }
        
        // Should a mail be requeued for sending?
        $requeue = (int) FormUtil::getPassedValue('requeue');
        if ($requeue > 0) {
            if (!SecurityUtil::confirmAuthkey()) {
                LogUtil::registerAuthIDError();
            } else {
                $result = pnModAPIFunc('Mailer','admin','requeue',array('id' => $requeue));
                if (!$result) {
                    LogUtil::registerError(_MAILER_REQUEUE_ERROR);
                } else {
                    LogUtil::registerStatus(_MAILER_MAIL_REQEUEUED.' '.$result);
                }
            }
            return $render->pnFormRedirect(pnModURL('Mailer','admin','errorlog'));
        }
        // Should all mails be requeued?
        $action = (string) FormUtil::getPassedValue('action');
        if (isset($action) && ($action == 'ra')) {
            if (!SecurityUtil::confirmAuthkey()) {
                LogUtil::registerAuthIDError();
            } else {
                $queue      = pnModAPIFunc('Mailer','admin','getErrorLog',array('limitoffset' => $mailerpager, 'numrows' => $numrows, 'sortmode' => $sortmode));
                $queuecount = pnModAPIFunc('Mailer','admin','getErrorLog',array('countonly' => 1));
                $c = 0;
                foreach ($queue as $item) {
                    $c++;
                    pnModAPIFunc('Mailer','admin','requeue',array('id' => $item['id']));
                }
                $max = 1000;
                if ($queuecount > $max) {
                    LogUtil::registerStatus(_MAILER_REQUEUE_SECCESSFUL_REPEAT);
                } else {
                    LogUtil::registerStatus($c.' '._MAILER_REQUEUE_SECCESSFUL);
                }
            }            
            return $render->pnFormRedirect(pnModURL('Mailer','admin','errorlog'));
        }
        // Delete error log
        if (isset($action) && ($action == 'da')) {
            if (!SecurityUtil::confirmAuthkey()) {
                LogUtil::registerAuthIDError();
            } else {
                $result = DBUtil::truncateTable('mailer_errorlog');
                if ($result) {
                    LogUtil::registerStatus(_MAILER_ERROR_LOG_DELETED);
                } else {
                    LogUtil::registerError(_MAILER_ERROR_LOG_DELETION_ERROR);
                }
            }            
            return $render->pnFormRedirect(pnModURL('Mailer','admin','errorlog'));
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
        $queue      = pnModAPIFunc('Mailer','admin','getErrorLog',array('limitoffset' => $mailerpager, 'numrows' => $numrows, 'sortmode' => $sortmode));
        $queuecount = pnModAPIFunc('Mailer','admin','getErrorLog',array('countonly' => 1));
        $render->assign('queue',        $queue);
        $render->assign('queuecount',   $queuecount);
        $render->assign('numrows',      $numrows);
        $render->assign('authid',       SecurityUtil::generateAuthKey());
    }
}
