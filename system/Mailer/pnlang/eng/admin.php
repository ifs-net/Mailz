<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: admin.php 24342 2008-06-06 12:03:14Z markwest $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_System_Modules
 * @subpackage  Zikula_Mailer
 */

// error log
define('_MAILER_ERRORLOG_EMPTY', 'No mails stored in the error logfile');
define('_MAILER_FOLDER_COUNT', 'failed deliveries');
define('_MAILER_ERROR_LOG_ADMINISTRATION', 'Mail error log view and administration');
define('_MAILER_TAB_FAIL_DATE', 'date of last try');
define('_MAILER_REQUEUE', 'requeue');
define('_MAILER_REQUEUE_ALL', 'Requeue all mails');
define('_MAILER_REALLY_DELETE_ALL', 'Please confirm the deletion of all mails of the error log');
define('_MAILER_ERROR_LOG_DELETED', 'Error log deleted completely');
define('_MAILER_REQUEUE_SECCESSFUL', 'Mails reqeued successfully');
define('_MAILER_REQUEUE_SECCESSFUL_REPEAT', 'mails requeued - please repeat this action until all mails are requeued');
define('_MAILER_ERROR_LOG_DELETION_ERROR', 'An error occured while deleting error log');
define('_MAILER_DELETE_ALL', 'Delete the error log');
define('_MAILER_REQUEUE_ERROR', 'An error occured while trying to requeue mail');
define('_MAILER_MAIL_REQEUEUED', 'Mail was requeued with highest priority and new ID');

// templates
define('_MAILER_TEMPLATE_EXPLANATION', 'You can create templates for all emails that will be sent out that are customized for your site. There are two types of emails goiing out: HTML and plain text. There are some example templates in the Mailer\'s template folder. There are four templates - two for HTML and two for plain text mails. Always the right template is chosen - depending on the mail type. The templates have to be named the following way');
define('_MAILER_DEV_OVERRIDE_OPTION', 'Module developers can override the usage of these templates - if you are a developer, please read the function\'s documentation! Pleas also mention that template changes will affect all mails that are in the mail queue and waiting for delivery!');
define('_MAILER_TPL_FOOTER_HTML', 'Footer for HTML mails');
define('_MAILER_TPL_FOOTER_TEXT', 'Footer for plain text mails');
define('_MAILER_TPL_HEADER_HTML', 'Header for HTML mails');
define('_MAILER_TPL_HEADER_TEXT', 'Header for plain text mails');
define('_MAILER_TPL_USAGE_HINTS', 'Please use theme based template overriding and clear your rendering cache after changing the templates. The templates will be used as they are seen below.');
define('_MAILER_TEMPLATES_PREVIEW', 'Preview');
define('_MAILER_NO_TPL_FOUND',  'No templates found');
define('_MAILER_TEMPLATES_TEXT_PREVIEW', 'Preview for plain text mails');
define('_MAILER_TEMPLATES_HTML_PREVIEW', 'Preview for html mails');
define('_MAILER_TPL_TEXT_EXAMPLE', 'This is some default text that will be replaced by your email\'s content later whenever a mail is being sent out!');
define('_MAILER_TPL_HTML_EXAMPLE', 'This is <font color="red">some default text</font> that will be <b>replaced</b> by your <i>email\'s content</i> later whenever a mail is being sent out!<hr />Have fun! ;-)');
define('_MAILER_TPL_TEXT_LINEBREAKS_HINT', 'Depending on your personal site mailer configuration linebreaks will be added before sending the email. These linebreaks can not be seen here - please send a test email to an own email account for testing this.');

// queue handling
define('_MAILER_QUEUESYSTEMINIT', 'Send mails in background using SystemInit hooks');
define('_MAILER_QUEUECRONJOB', 'Send mails via cronjob');
define('_MAILER_INSTANTDELIVERY', 'Send mails immediately');
define('_MAILER_QUEUE_HANDLING', 'Default method for queue handling');
define('_MAILER_QUEUE_FREQUENCY', 'Number of mails that should be sent with each site or cronjob call');
define('_MAILER_QUEUE_SETTINGS', 'Settings for mail queue handling');
define('_MAILER_QUEUE_CRON_PWD', 'Password for cronjob call');
define('_MAILER_QUEUE_MODE_EXPLANATION', 'Queue modes can make sense of your site is a site with high outgoing mail traffic. Mails are put in a queue and sent mail by mail in the background. Mails can be sent with each site call that is made by any user. Mail sending will be handled in the background. You can choose between to different queue modes: Mails can be sent with each site call (a user or a visitor will make your emails to be sent without seeing this) or you can use a given URL for a cronjob service. Just call the cronjob URL with a password (you can try it out in your browser entering the url) you have specified and this will send the number of mails you specified in this configuration area.');
define('_MAILER_NO_CRON_PWD_STORED_YET', 'Please set a cronjob password to get the URL for this service');
define('_MAILER_CRON_JOB_URL_CALL', 'Call this URL for email delivery regularly');

// queue
define('_MAILER_QUEUE_ADMINISTRATION', 'Mail queue view and administration');
define('_MAILER_QUEUE_EXPLANATION', 'Here all mails from the mail queue are listed. Please do not forget that mails might be sent instantly after they were listed here. You can access the mail if you click on the title.');
define('_MAILER_TAB_NR', 'Mail ID');
define('_MAILER_QUEUE_COUNT', 'mails in queue');
define('_MAILER_TAB_TO', 'recipient');
define('_MAILER_TAB_STATUS', 'status');
define('_MAILER_TAB_PRIORITY', 'priority');
define('_MAILER_TAB_DATE', 'planed date');
define('_MAILER_TAB_TITLE', 'mail subject');
define('_MAILER_TAB_ACTION', 'action');
define('_MAILER_NOW', 'now');
define('_MAILER_STATUS_RETRYLAST', 'failed... waiting for last try');
define('_MAILER_DELETE', 'delete');
define('_MAILER_STATUS_RETRY', 'failed... waiting for retry');
define('_MAILER_STATUS_WAITING', 'waiting for delivery');
define('_MAILER_MAIL_LOAD_ERROR', 'An error occured - the mail could not be loaded. Maybe it was delivered or moved to error logfile in the time between creating the last overview and your action.');
define('_MAILER_MAIL_DELETED', 'The mail was successfully removed and deleted from the mail queue');
define('_MAILER_MAIL_VIEW', 'Mail content');
define('_MAILER_SEND_NOW', 'send manually');
define('_MAILER_SEND_ERROR', 'An error occured while trying to send the selected email manualy');
define('_MAILER_MAIL_SENT', 'The email was sent successfully and removed from the mail queue');
define('_MAILER_MESSAGE_MOVED_ERROR_LOG', 'Message was moved to error log - delivery cancelled');
define('_MAILER_QUEUE_EMPTY', 'Mail queue emtpy - nothing to display');

// general
define('_MAILER_ERRORLOG', 'Error-log');
define('_MAILER_TEMPLATES', 'Templates');
define('_MAILER_MAILQUEUE', 'Mail-Queue');
define('_MAILER','Zikula mailer');

// navigation
define('_MAILER_TESTCONFIG', 'Test configuration');

// modify config
define('_MAILER_CHARSET', 'Default character set (default: %charset%)');
define('_MAILER_CONTENTTYPE', 'Send HTML e-mails by default');
define('_MAILER_ENCODING', 'Default encoding (default: 8-bit)');
define('_MAILER_GENERALSETTINGS', 'General settings');
define('_MAILER_MSMAILHEADERS', 'Use Microsoft mail client headers');
define('_MAILER_SENDMAILSETTINGS', 'SendMail settings');
define('_MAILER_TRANSPORT', 'Default mail transport');
define('_MAILER_WORDWRAP', 'Word wrap (default: 50)');

// send mail settings
define('_MAILER_SENDMAIL', 'Sendmail');
define('_MAILER_SENDMAILPATH', 'Sendmail path');

// smtp settings
define('_MAILER_SMTPAUTH', 'Enable SMTP authentication');
define('_MAILER_SMTPMAIL', 'SMTP');
define('_MAILER_SMTPPASSWORD', 'SMTP password');
define('_MAILER_SMTPPORT','SMTP port (default: 25)');
define('_MAILER_SMTPSERVER','SMTP server (default: localhost)');
define('_MAILER_SMTPSETTINGS', 'SMTP Settings');
define('_MAILER_SMTPTIMEOUT', 'SMTP time-out (in seconds -- default: 10)');
define('_MAILER_SMTPUSERNAME', 'SMTP user name');

// other mail transports
define('_MAILER_PHPMAIL', 'PHP mail()');
define('_MAILER_QMAIL', 'QMail');

// test configuration
define('_MAILER_HTML', 'HTML-formatted message');
define('_MAILER_BODY', 'Message');
define('_MAILER_FROMADDRESS', 'From address');
define('_MAILER_FROMNAME', 'From name');
define('_MAILER_PNMAIL', 'Send message via pnMail API');
define('_MAILER_SUBJECT', 'Subject');
define('_MAILER_TOADDRESS', 'To address');
define('_MAILER_TONAME', 'To name');
define('_MAILER_NO_QUEUE_MODE', 'Override mail queue settings (not usable with pnMail API)');

// errors/statuses
define('_MAILER_MESSAGESENT', 'Message sent');
define('_MAILER_MESSAGENOTSENT', 'Message not sent');
