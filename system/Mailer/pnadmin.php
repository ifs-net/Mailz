<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnadmin.php 26261 2009-08-20 04:50:50Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_System_Modules
 * @subpackage Mailer
 */

/**
 * the main administration function
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments.  As such it can
 * be used for a number of things, but most commonly it either just
 * shows the module menu and returns or calls whatever the module
 * designer feels should be the default function (often this is the
 * view() function)
 * @author Florian Schießl, Mark West
 * @return string HTML string
 */
function Mailer_admin_main()
{
    // security check
    if (!SecurityUtil::checkPermission('Mailer::', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // create a new output object
    $pnRender = & pnRender::getInstance('Mailer', false);

    return $pnRender->fetch('mailer_admin_main.htm');
}

/**
 * This function shows a preview and some more information about
 * the Mailer templates for this zikula installation
 * @author Florian Schießl
 * @return string output
 */
function Mailer_admin_templates()
{
    // security check
    if (!SecurityUtil::checkPermission('Mailer::', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    // get html template
    $html_preview = pnModAPIFunc('Mailer','admin','applyTemplate',array('content' => _MAILER_TPL_HTML_EXAMPLE, 'type' => 'html'));
    $text_preview = pnModAPIFunc('Mailer','admin','applyTemplate',array('content' => _MAILER_TPL_TEXT_EXAMPLE, 'type' => 'text'));
    $text_preview = str_replace('<','&lt;',$text_preview);
    $text_preview = str_replace('>','&gt;',$text_preview);

    // Maybe only the html should be shown?
    $html = (int) FormUtil::getPassedValue('html');
    if ($html == 1) {
        print $html_preview;
        return true;
    }

    // create a new output object
    $pnRender = pnRender::getInstance('Mailer', false);

    // Assign preview
    $pnRender->assign('text_preview', $text_preview);
    $pnRender->assign('html_preview', $html_preview);

    return $pnRender->fetch('mailer_admin_templates.htm');
}

/**
 * the mail queue management function
 *
 * @author      Florian Schießl
 * @return      output
 */
function Mailer_admin_queue()
{
  	// load handler class
	Loader::requireOnce('system/Mailer/pnincludes/classes/admin/queue.php');

	// Create output and call handler class
	$render = FormUtil::newpnForm('Mailer');

    // Return the output
    return $render->pnFormExecute('mailer_admin_queue.htm', new mailer_queueHandler());
}

/**
 * the mail queue management function
 *
 * @author      Florian Schießl
 * @return      output
 */
function Mailer_admin_errorlog()
{
  	// load handler class
	Loader::requireOnce('system/Mailer/pnincludes/classes/admin/errorlog.php');

	// Create output and call handler class
	$render = FormUtil::newpnForm('Mailer');

    // Return the output
    return $render->pnFormExecute('mailer_admin_errorlog.htm', new mailer_errorlogHandler());
}

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 * @author Mark West
 * @return string HTML string
 */
function Mailer_admin_modifyconfig()
{
    // security check
    if (!SecurityUtil::checkPermission('Mailer::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // create a new output object
    $Render = pnRender::getInstance('Mailer', false);

    // assign the module mail agent types
    $Render->assign('mailertypes', array(1 => DataUtil::formatForDisplay(_MAILER_PHPMAIL),
                                     2 => DataUtil::formatForDisplay(_MAILER_SENDMAIL),
                                     3 => DataUtil::formatForDisplay(_MAILER_QMAIL),
                                     4 => DataUtil::formatForDisplay(_MAILER_SMTPMAIL)));

    // assign queue methods
    $Render->assign('queuetypes', array(1 => DataUtil::formatForDisplay(_MAILER_INSTANTDELIVERY),
                                     2 => DataUtil::formatForDisplay(_MAILER_QUEUECRONJOB),
                                     3 => DataUtil::formatForDisplay(_MAILER_QUEUESYSTEMINIT)));

    // assign all module vars
    $Render->assign(pnModGetVar('Mailer'));

    return $Render->fetch('mailer_admin_modifyconfig.htm');
}

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author Mark West
 * @see Mailer_admin_updateconfig()
 * @param int mailertype Mail transport agent
 * @param string charset default character set of the message
 * @param string encoding default encoding
 * @param bool html send html e-mails by default
 * @param int wordwrap word wrap column
 * @param int msmailheaders include MS mail headers
 * @param string sendmailpath path to sendmail
 * @param int smtpauth enable SMTPAuth
 * @param string smtpserver ip address of SMTP server
 * @param int smtpport port number of SMTP server
 * @param int smtptimeout SMTP timeout
 * @param string smtpusername SMTP username
 * @param string smtppassword SMTP password
 * @param int queuefrequency Mails sent at one time for a system init or a cronjob url call
 * @param string queuecronpwd Password for URL that is called via any cron service
 * @param int queuetype for the way sending or queuing mails
 * @return bool true if update successful
 */
function Mailer_admin_updateconfig()
{
    // security check
    if (!SecurityUtil::checkPermission('Mailer::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // confirm our forms authorisation key
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError (pnModURL('Mailer','admin','main'));
    }

    // set our new module variable values
    $oldqueuetype = (int) pnModGetVar('Mailer','queuetype');
    $queuetype = (int)FormUtil::getPassedValue('queuetype', 1, 'POST');
    if (($oldqueuetype != 3) && ($queuetype == 3)) {
        // Activate hook now
        pnModAPIFunc('Modules', 'admin', 'enablehooks', array('callermodname' => 'zikula', 'hookmodname' => 'Mailer'));
    } else if (($oldqueuetype == 3) && ($queuetype != 3)) {
        // Disable hook now
        pnModAPIFunc('Modules', 'admin', 'disablehooks', array('callermodname' => 'zikula', 'hookmodname' => 'Mailer'));
    }
    pnModSetVar('Mailer', 'queuetype', $queuetype);

    $queuefrequency = (int)FormUtil::getPassedValue('queuefrequency', 15, 'POST');
    pnModSetVar('Mailer', 'queuefrequency', $queuefrequency);

    $queuecronpwd = (string)FormUtil::getPassedValue('queuecronpwd', rand(1000000000,9999999999), 'POST');
    pnModSetVar('Mailer', 'queuecronpwd', $queuecronpwd);

    $mailertype = (int)FormUtil::getPassedValue('mailertype', 1, 'POST');
    pnModSetVar('Mailer', 'mailertype', $mailertype);

    $charset = (string)FormUtil::getPassedValue('charset', _CHARSET, 'POST');
    pnModSetVar('Mailer', 'charset', $charset);

    $encoding = (string)FormUtil::getPassedValue('encoding', '8bit', 'POST');
    pnModSetVar('Mailer', 'encoding', $encoding);

    $html = (bool)FormUtil::getPassedValue('html', false, 'POST');
    pnModSetVar('Mailer', 'html', $html);

    $wordwrap = (int)FormUtil::getPassedValue('wordwrap', 50, 'POST');
    pnModSetVar('Mailer', 'wordwrap', $wordwrap);

    $msmailheaders = (bool)FormUtil::getPassedValue('msmailheaders', false, 'POST');
    pnModSetVar('Mailer', 'msmailheaders', $msmailheaders);

    $sendmailpath = (string)FormUtil::getPassedValue('sendmailpath', '/usr/sbin/sendmail', 'POST');
    pnModSetVar('Mailer', 'sendmailpath', $sendmailpath);

    $smtpauth = (bool)FormUtil::getPassedValue('smtpauth', false, 'POST');
    pnModSetVar('Mailer', 'smtpauth', $smtpauth);

    $smtpserver = (string)FormUtil::getPassedValue('smtpserver', 'localhost', 'POST');
    pnModSetVar('Mailer', 'smtpserver', $smtpserver);

    $smtpport = (int)FormUtil::getPassedValue('smtpport', 25, 'POST');
    pnModSetVar('Mailer', 'smtpport', $smtpport);

    $smtptimeout = (int)FormUtil::getPassedValue('smtptimeout', 10, 'POST');
    pnModSetVar('Mailer', 'smtptimeout', $smtptimeout);

    $smtpusername = (string)FormUtil::getPassedValue('smtpusername', '', 'POST');
    pnModSetVar('Mailer', 'smtpusername', $smtpusername);

    $smtppassword = (string)FormUtil::getPassedValue('smtppassword', '', 'POST');
    pnModSetVar('Mailer', 'smtppassword', $smtppassword);

    // Let any other modules know that the modules configuration has been updated
    pnModCallHooks('module', 'updateconfig', 'Mailer', array('module' => 'Mailer'));

    // the module configuration has been updated successfuly
    LogUtil::registerStatus (_CONFIGUPDATED);

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    return pnRedirect(pnModURL('Mailer', 'admin', 'main'));
}

/**
 * This function displays a form to sent a test mail
 * @author Mark West
 * @return string HTML string
 */
function Mailer_admin_testconfig()
{
    // security check
    if (!SecurityUtil::checkPermission('Mailer::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // create a new output object
    $pnRender = & pnRender::getInstance('Mailer', false);

    // Return the output that has been generated by this function
    return $pnRender->fetch('mailer_admin_testconfig.htm');
}

/**
 * This function processes the results of the test form
 * @author Mark West
 * @param string args['toname '] name to the recipient
 * @param string args['toaddress'] the address of the recipient
 * @param string args['subject'] message subject
 * @param string args['body'] message body
 * @param int args['html'] HTML flag
 * @return bool true
 */
function Mailer_admin_sendmessage($args)
{
    // security check
    if (!SecurityUtil::checkPermission('Mailer::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    $toname = (string)FormUtil::getPassedValue('toname', isset($args['toname']) ? $args['toname'] : null, 'POST');
    $toaddress = (string)FormUtil::getPassedValue('toaddress', isset($args['toaddress']) ? $args['toaddress'] : null, 'POST');
    $subject = (string)FormUtil::getPassedValue('subject', isset($args['subject']) ? $args['subject'] : null, 'POST');
    $body = (string)FormUtil::getPassedValue('body', isset($args['body']) ? $args['body'] : null, 'POST');
    $pnmail = (bool)FormUtil::getPassedValue('pnmail', isset($args['pnmail']) ? $args['pnmail'] : false, 'POST');
    $html = (bool)FormUtil::getPassedValue('html', isset($args['html']) ? $args['html'] : false, 'POST');

    // confirm our forms authorisation key
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError (pnModURL('Mailer','admin','main'));
    }

    // set the email
    if ($pnmail) {
        $from = pnConfigGetVar('adminmail');
        $result = pnMail($toaddress, $subject, $body, "From: $from\nX-Mailer: PHP/" . phpversion(), $html);
    } else {
        $result = pnModAPIFunc('Mailer', 'user', 'sendmessage',
                               array('toname' => $toname,
                                     'toaddress' => $toaddress,
                                     'subject' => $subject,
                                     'body' => $body,
                                     'html' => $html,
                                     'priority' => 1,
                                     'noqueue' => $noqueue));
    }

    // check our result and return the correct error code
    if ($result === true) {
        // Success
        LogUtil::registerStatus (_MAILER_MESSAGESENT);
    } elseif ($result === false) {
        // Failiure
        LogUtil::registerError (_MAILER_MESSAGENOTSENT);
    } else {
        // Failiure with error
        LogUtil::registerError (_MAILER_MESSAGENOTSENT . ' - ' . $result);
    }

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    return pnRedirect(pnModURL('Mailer', 'admin', 'testconfig'));
}
