<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnuserapi.php 25449 2009-02-26 08:38:53Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_System_Modules
 * @subpackage Mailer
 */

/**
 * API function to send e-mail message
 * @author Florian Schießl, Mark West
 * @param   string          args['fromname']            name of the sender
 * @param   string          args['fromaddress']         address of the sender
 * @param   string          args['toname']              name to the recipient
 * @param   string          args['toaddress']           the address of the recipient
 * @param   string          args['replytoname']         name to reply to
 * @param   string          args['replytoaddress']      address to reply to
 * @param   string          args['subject']             message subject
 * @param   string          args['contenttype']         optional contenttype of the mail (default config)
 * @param   string          args['charset']             optional charset of the mail (default config)
 * @param   string          args['encoding']            optional mail encoding (default config)
 * @param   string          args['body']                message body
 * @param   array           args['cc']                  addresses to add to the cc list
 * @param   array           args['bcc']                 addresses to add to the bcc list
 * @param   array/string    args['headers']             custom headers to add
 * @param   int             args['html']                HTML flag
 * @param   array           args['attachments']         array of either absolute filenames to attach to the mail or array of arays in format array($string,$filename,$encoding,$type)
 * @param   array           args['stringattachments']   array of arrays to treat as attachments, format array($string,$filename,$encoding,$type)
 * @param   array           args['embeddedimages']      array of absolute filenames to image files to embed in the mail
 * @param   int             args['notemplates']         optional, for modules sending emails without predefined templates
 * @param   int             args['priority']            optional, highest priority 1, lowest 10, default 5
 * @param   int             args['noqueue']             optional, do not use queue even if configured
 * @param   string          args['date']                optional, date when email should be started to deliver (only if mail queuing mode is enabled in admin backend). Format: 0000-00-00 00:00:00
 * @param   int             args['quiet']               optional, set to 1 if no errors or messages should be logged
 *
 * @todo Loading of language file based on Zikula language
 * @return bool true if successful, false otherwise
 */
function Mailer_userapi_sendmessage($args)
{
    // Get some parameters
    $quiet = (((int) $args['quiet']) == 1);

    // Load language
    pnModLangLoad('Mailer');

    // include php mailer class file
    if (file_exists($file = "system/Mailer/pnincludes/class.phpmailer.php")) {
        Loader::requireOnce($file);
    } else {
        return false;
    }

    // If queue handling is activated we will just put the function call into the queue and process this later
    $noqueue    = (int) $args['noqueue'];
    $queuetype  = pnModGetVar('Mailer', 'queuetype');
    if (($queuetype > 1) && ($noqueue != 1)) {
        // Get priority of email and set to default value if no priority was specified
        $priority = (int) $args['priority'];
        if ($priority == 0) {
            $priority = 5;
        }
        // Get date for first delivery
        $date = $args['date'];
        if (isset($date)) {
            // Create timestamp
            $date = (int) strtotime($date);
            if (!($date > time())) {
                // No valid date or date already passed - DB will use DB field default value (0000-00-00 00:00:00) for instant delivery
                unset($date);
            } else {
                // Set new date this way to be sure to have right format for Database
                $date = date("Y-m-d H:i:s", $date);
            }
        }
        $obj = array(
            'priority'  => $priority,
            'content'   => serialize($args),
            'date'      => $date
          );
        $result = DBUtil::insertObject($obj,'mailer_queue');
        if ($result) {
            return true;
        } else {
            if (!$quiet) {
                LogUtil::registerError(_MAILER_STORE_ERROR);
            }
            return false;
        }

    }

    // create new instance of mailer class
    $mail = new phpmailer();

    // set default message parameters
    $mail->PluginDir = "system/Mailer/pnincludes/";
    $mail->ClearAllRecipients();
    $mail->ContentType = isset($args['contenttype']) ? $args['contenttype'] : pnModGetVar('Mailer', 'contenttype');
    $mail->CharSet     = isset($args['charset'])     ? $args['charset']     : pnModGetVar('Mailer', 'charset');
    $mail->Encoding    = isset($args['encoding'])    ? $args['encoding']    : pnModGetVar('Mailer', 'encoding');
    $mail->WordWrap    = pnModGetVar('Mailer', 'wordwrap');

    // load the language file
    $mail->SetLanguage('en', $mail->PluginDir . 'language/');

    // get MTA configuration
    if (pnModGetVar('Mailer', 'mailertype') == 4) {
        $mail->IsSMTP();  // set mailer to use SMTP
        $mail->Host = pnModGetVar('Mailer', 'smtpserver');  // specify server
        $mail->Port = pnModGetVar('Mailer', 'smtpport');    // specify port
    } else if (pnModGetVar('Mailer', 'mailertype') == 3) {
        $mail->IsQMail();  // set mailer to use QMail
        $mail->Host = pnModGetVar('Mailer', 'smtpserver');  // specify server
    } else if (pnModGetVar('Mailer', 'mailertype') == 2) {
        ini_set("sendmail_from", $args['fromaddress']);
        $mail->IsSendMail();  // set mailer to use SendMail
    } else {
        $mail->IsMail();  // set mailer to use php mail
    }

    // set authentication paramters if required
    if (pnModGetVar('Mailer', 'smtpauth') == 1) {
        $mail->SMTPAuth = true; // turn on SMTP authentication
        $mail->Username = pnModGetVar('Mailer', 'smtpusername');  // SMTP username
        $mail->Password = pnModGetVar('Mailer', 'smtppassword');  // SMTP password
    }

    // set HTML mail if required
    if (isset($args['html']) && is_bool($args['html'])) {
        $mail->IsHTML($args['html']); // set email format to HTML
        if ($args['html']) {
            $mailtype = 'html';
        } else {
            $mailtype = 'text';
        }
    } else {
        $html = pnModGetVar('Mailer', 'html');
        $mail->IsHTML($html); // set email format to the default
        if ($html) {
            $mailtype = 'html';
        } else {
            $mailtype = 'text';
        }
    }

    // Add template
    $notemplates = (int) $args['notemplates'];
    if ($notemplates != 1) {
        $args['body'] = pnModAPIFunc('Mailer','admin','applyTemplate',array('content' => $args['body'], 'type' => $mailtype));
    }

    // set fromname and fromaddress, default to 'sitename' and 'adminmail' config vars
    $mail->FromName = (isset($args['fromname']) && $args['fromname']) ? $args['fromname'] : pnConfigGetVar('sitename');
    $mail->From     = (isset($args['fromaddress']) && $args['fromaddress']) ? $args['fromaddress'] : pnConfigGetVar('adminmail');

    // add any to addresses
    if (is_array($args['toaddress'])) {
        $i = 0;
        foreach ($args['toaddress'] as $toadd) {
            isset($args['toname'][$i]) ? $tona = $args['toname'][$i] : $tona = $toadd;
            $mail->AddAddress($toadd, $tona);
            $i++;
        }
    } else {
         // $toaddress is not an array -> old logic
        $tona = '';
        if (isset($args['toname'])) {
            $tona = $args['toname'];
        }
        // process multiple names entered in a single field separated by commas (#262)
        foreach (explode(',', $args['toaddress']) as $toadd) {
            $mail->AddAddress($toadd, ($tona == '') ? $toadd : $tona);
        }
    }

    // if replytoname and replytoaddress have been provided us them
    // otherwise take the fromaddress, fromname we build earlier
    if (!isset($args['replytoname']) || empty($args['replytoname'])) {
        $args['replytoname'] = $mail->FromName;
    }
    if (!isset($args['replytoaddress'])  || empty($args['replytoaddress'])) {
          $args['replytoaddress'] = $mail->From;
    }
    $mail->AddReplyTo($args['replytoaddress'], $args['replytoname']);

    // add any cc addresses
    if (isset($args['cc']) && is_array($args['cc'])) {
        foreach ($args['cc'] as $email) {
            if (isset($email['name'])) {
                $mail->AddCC($email['address'], $email['name']);
            } else {
                $mail->AddCC($email['address']);
            }
        }
    }

    // add any bcc addresses
    if (isset($args['bcc']) && is_array($args['bcc'])) {
        foreach ($args['bcc'] as $email) {
            if (isset($email['name'])) {
                $mail->AddBCC($email['address'], $email['name']);
            } else {
                $mail->AddBCC($email['address']);
            }
        }
    }

    // add any custom headers
    if (isset($args['headers']) && is_string($args['headers'])) {
        $args['headers'] = explode ("\n", $args['headers']);
    }
    if (isset($args['headers']) && is_array($args['headers'])) {
        foreach ($args['headers'] as $header) {
            $mail->AddCustomHeader($header);
        }
    }

    // add message subject and body
    $mail->Subject = $args['subject'];
    $mail->Body    = $args['body'];

    // add attachments
    if (isset($args['attachments']) && !empty($args['attachments'])) {
        foreach($args['attachments'] as $attachment) {
            if (is_array($attachment)) {
                if (count($attachment) != 4) {
                    // skip invalid arrays
                    continue;
                }
                $mail->AddAttachment($attachment[0], $attachment[1], $attachment[2], $attachment[3]);
            } else {
                $mail->AddAttachment($attachment);
            }
        }
    }

    // add string attachments.
    if (isset($args['stringattachments']) && !empty($args['stringattachments'])) {
        foreach($args['stringattachments'] as $attachment) {
            if (is_array($attachment) && count($attachment) == 4) {
                $mail->AddStringAttachment($attachment[0], $attachment[1], $attachment[2], $attachment[3]);
            }
        }
    }

    // add embedded images
    if (isset($args['embeddedimages']) && !empty($args['embeddedimages'])) {
        foreach($args['embeddedimages'] as $embeddedimage) {
           $mail->AddEmbeddedImage($embeddedimage);
        }
    }

    // send message
    if (!$mail->Send()) {
        // message not send
        $args['errorinfo'] = ($mail->IsError()) ? $mail->ErrorInfo : pnML(_ERROR_UNKNOWNMAILERERROR);
        if (!$quiet) {
            LogUtil::log(pnML(_ERROR_SENDINGMAIL_ADMINLOG, $args));
            LogUtil::registerError(_ERROR_SENDINGMAIL);
        }
        return false;
    }
    return true; // message sent
}

/**
 * Systeminit function is needed to send mails with each site call
 * @return  void
 */
function Mailer_userapi_systeminit()
{
    // We will not send mails whenever the actual user is using a backend
    // Maybe the admin has to fix some errors
    $type = (string) FormUtil::getPassedValue('type');
    $type = strtolower($type);
    if (!isset($type) || ($type != 'admin')) {
        // Calling sendQueue without parameters lets the mails be sent out
        pnModAPIFunc('Mailer','admin','sendQueue');
    }
    // Nothing to return - otherwise errors would be displayed for the actual user
    return;
}