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
 * send queued mails
 * This function can be called by cronjob url services and
 * will send X mails that are in the mail queue.
 *
 * @author Florian Schießl
 * @return string
 */
function Mailer_user_cron()
{
    // cron enabled?
    $queuetype = pnModGetVar('Mailer','queuetype');
    if ($queuetype != 2) {
        print _MAILER_CRON_DISABLED;
        return true;
    }
    // security check
    $pwd = (string) FormUtil::getPassedValue('pwd');
    $queuecronpwd = pnModGetVar('Mailer', 'queuecronpwd');
    if (($pwd == $queuecronpwd) && ($pwd != '') ) {
        print _MAILER_AUTHENTICATION_CORRENCT.'...';
        print _MAILER_CRONJOB_START.'...';
        // No we will get the first queuefrequency entries of the queue sorted by priority
        $queuefrequency = (int)pnModGetVar('Mailer','queuefrequency');
        $queue = pnModAPIFunc('Mailer','admin','getQueue',array('sortmode' => 4, 'numrows' => $queuefrequency, 'skipfuture' => 1));
        if (!$queue) {
            echo _MAILER_NO_MAILS_IN_QUEUE;
        } else {
            $count = count($queue);
            // Send mail from queue
            foreach ($queue as $item) {
                $result = pnModAPIFunc('Mailer','admin','sendQueue',array('id' => $item['id']));
                if ($result) {
                    print _MAILER_SENT_ID.': ['.$item['id'].' ('.($item['try']+1).')]';
                } else {
                    print _MAILER_SENT_ERROR_ID.': ['.$item['id'].'('.($item['try']+1).')]';
                }
            }
        }
    } else {
        print _MAILER_CRONJOB_WRONG_PWD;
    }
    return true;
}