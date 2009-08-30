<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadminapi.php 24342 2008-06-06 12:03:14Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @uses PHPMailer http://phpmailer.sourceforge.net
 * @package Zikula_System_Modules
 * @subpackage Mailer
 */

/**
 * get available admin panel links
 *
 * @author Mark West
 * @return array array of admin links
 */
function Mailer_adminapi_getlinks()
{
    $links = array();

    pnModLangLoad('Mailer', 'admin');

    if (SecurityUtil::checkPermission('Mailer::', '::', ACCESS_ADMIN)) {
        $links[] = array('url' => pnModURL('Mailer', 'admin', 'modifyconfig'), 'text' => _MODIFYCONFIG);
        $links[] = array('url' => pnModURL('Mailer', 'admin', 'templates'), 'text' => _MAILER_TEMPLATES);
        $links[] = array('url' => pnModURL('Mailer', 'admin', 'queue'), 'text' => _MAILER_MAILQUEUE);
        $links[] = array('url' => pnModURL('Mailer', 'admin', 'errorlog'), 'text' => _MAILER_ERRORLOG);
        $links[] = array('url' => pnModURL('Mailer', 'admin', 'testconfig'), 'text' => _MAILER_TESTCONFIG);
    }

    return $links;
}

/**
 * get mail queue
 *
 * @param   int     args['limitoffset']
 * @param   int     args['numrows']
 * @param   int     args['sortmode']        sort list with different criteria
 * @param   int     args['countonly']       optional, only count number of messages if set to 1
 * @param   int     args['skipfuture']      skip mails that are not relevant because time to send them is in future
 * @return  array
 */
function Mailer_adminapi_getQueue($args)
{
    // Get Parameters
    $countonly      = (int) $args['countonly'];
    $sortmode       = (int) $args['sortmode'];
    $skipfuture     = (int) $args['skipfuture'];
    $limitoffset    = $args['limitoffset'];
    $numrows        = $args['numrows'];

    // Sortmodes
    switch ($sortmode) {
        case 8:
            $orderby = 'try DESC';
            break;
        case 7:
            $orderby = 'try ASC';
            break;
        case 6:
            $orderby = 'date ASC';
            break;
        case 5:
            $orderby = 'date DESC';
            break;
        case 4:
            $orderby = 'priority ASC';
            break;
        case 3:
            $orderby = 'priority DESC';
            break;
        case 1:
            $orderby = 'id DESC';
            break;
        case 2:
        default:
            $orderby = 'id ASC';
    }

    if ($skipfuture == 1) {
        $where = "date < '".date("Y-m-d H:i:s",time())."'";
    }

    if ($countonly == 1) {
        $result = DBUtil::selectObjectCount('mailer_queue',$where);
    } else {
        $result = DBUtil::selectObjectArray('mailer_queue',$where,$orderby,$limitoffset,$numrows);
    }
    // Return result
    return $result;
}

/**
 * get mail queue (error logfile)
 *
 * @param   int     args['limitoffset']
 * @param   int     args['numrows']
 * @param   int     args['sortmode']        sort list with different criteria
 * @param   int     args['countonly']       optional, only count number of messages if set to 1
 * @return  array
 */
function Mailer_adminapi_getErrorLog($args)
{
    // Get Parameters
    $countonly      = (int) $args['countonly'];
    $sortmode       = (int) $args['sortmode'];
    $limitoffset    = $args['limitoffset'];
    $numrows        = $args['numrows'];

    // Sortmodes
    switch ($sortmode) {
        case 6:
            $orderby = 'date ASC';
            break;
        case 5:
            $orderby = 'date DESC';
            break;
        case 1:
            $orderby = 'id DESC';
            break;
        case 2:
        default:
            $orderby = 'id ASC';
    }

    if ($countonly == 1) {
        $result = DBUtil::selectObjectCount('mailer_errorlog',$where);
    } else {
        $result = DBUtil::selectObjectArray('mailer_errorlog',$where,$orderby,$limitoffset,$numrows);
    }
    // Return result
    return $result;
}

/**
 * send mails from mail queue
 *
 * @param   int     args['id']          send only one specific email from queue
 * @param   int     args['quiet']       use quiet mode if set to 1 (optional)
 * @return  bool
 */
function Mailer_adminapi_sendQueue($args)
{
    // Get Parameters
    $id     = (int) $args['id'];
    $quiet  = (int) $args['quiet'];
    $quietmode = ($quiet == 1);

    // Check for single mail that should be sent
    if ($id > 0) {
        $mail = DBUtil::selectObjectByID('mailer_queue',$id);
        if (!$mail) {
            return false;
        } else {
            // Construct parameters for send mail function
            $obj = unserialize($mail['content']);
            $obj['quiet'] = 1;
            $obj['noqueue'] = 1;
            // Send mail
            $result = pnModAPIFunc('Mailer','user','sendmessage',$obj);
            if (!$result) {
                // An error occured. Now make try++ or move to error log
                $mail['try']++;
                if ($mail['try'] > 2) {
                    $mail['date'] = date("Y-m-d H:i:s",time());
                    $result = DBUtil::insertObject($mail,'mailer_errorlog',true);
                    if ($result) {
                        $result = DBUtil::deleteObject($mail,'mailer_queue');
                        if (!$quietmode && !$result) {
                            pnModLangLoad('Mailer', 'admin');
                            LogUtil::registerError(_MAILER_MESSAGE_MOVED_ERROR_LOG);
                        }
                        return false;
                    }
                }
                DBUtil::updateObject($mail,'mailer_queue');
                return false;
            } else {
                DBUtil::deleteObject($mail,'mailer_queue');
                return true;
            }
        }
    } else {
        // Sent more mails otherwise
        // Get mail queue
        $queue = pnModAPIFunc('Mailer','admin','getQueue',array('countonly' => 1));
        if ($queue > 0) {
            // Now we have to check if there is a lock by another
            // user that is sending via systeminit hook
            $lock = (int) pnModGetVar('Mailer','locked');
            if ($lock > 0) {
                // If lock is to old the other process might be crashed (5 min)
                $diffdate = (time()-(5*60));
                if ($lock < $diffdate) {
                    // Delete module variable - sending will go on with next site call
                    pnModDelVar('Mailer','lock');
                    return true;
                }
            } else {
                // Lock for sending
                pnModSetVar('Mailer','lock',time());
                // send now...
                $queuefrequency = (int) pnModGetVar('Mailer','queuefrequency');
                $queue = pnModAPIFunc('Mailer','admin','getQueue',array('numrows' => $queuefrequency));
                foreach ($queue as $mail) {
                    // We don't want to see any messages.. The user does not know that he is sending mails ;-)
                    $mail['quiet'] = 1;
                    // Call function recursively
                    if ($mail['id'] > 0) {
                        Mailer_adminapi_sendQueue($mail);
                    } else {
                        return false;
                    }
                }
                return true;
            }
        }
    }
}

/**
 * re-queue mails
 * mails that could not be sent can be put back into the mail queue manually
 *
 * @param       int     args['id']      mail that should be requeued
 * @return int
 */
function Mailer_adminapi_requeue($args)
{
    // Little check
    $id = (int) $args['id'];
    if (!($id > 0)) {
        return false;
    }

    // Get mail
    $mail = DBUtil::selectObjectByID('mailer_errorlog',$id);
    if (!$mail) {
        return false;
    }

    // Store backup
    $backup = $mail;

    // Change some parameters
    $mail['priority'] = 1;
    $mail['try'] = 0;
    unset($mail['id']);

    // Insert into Queue
    $result = DBUtil::insertObject($mail,'mailer_queue');
    if (!$result) {
        return false;
    } else {
        DBUtil::deleteObject($backup,'mailer_errorlog');
        return $result['id'];
    }
}

/**
 * apply template to a mail content
 *
 * @param   string  args['content']     mail content
 * @param   string  args['type']        'text' or 'html'
 * @return  string
 */
function Mailer_adminapi_applyTemplate($args)
{
    // Get Parameters
    $type    = (string) $args['type'];
    $content = (string) $args['content'];

    // Get type for email content
    if (($type != 'html') && ($type != 'text')) {
        return $content;
    }

    // Get header and footer if templates exist
    $render = pnRender::getInstance('Mailer');
    if ($render->template_exists('mailer_'.$type.'_header.htm')) {
        $tpl_header = $render->fetch('mailer_'.$type.'_header.htm');
    }
    $render = pnRender::getInstance('Mailer');
    if ($render->template_exists('mailer_'.$type.'_footer.htm')) {
        $tpl_footer = $render->fetch('mailer_'.$type.'_footer.htm');
    }
    // Return transformed body as mail content
    return $tpl_header.$content.$tpl_footer;
}