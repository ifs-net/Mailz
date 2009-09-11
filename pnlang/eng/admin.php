<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

// general
define('_MAILZ_UNREG_READER', 'Guest');
define('_MAILZ_BACKEND', 'Admin backend MAILZ');
define('_MAILZ_ADMIN_MAIN', 'Module settings');
define('_MAILZ_TARGET_GROUPS', 'Target-Groups');
define('_MAILZ_NEWSLETTERS', 'Newsletters and mailings');
define('_MAILZ_PLUGINS', 'Plugins');
define('_MAILZ_ARCHIVE', 'Archive');
define('_MAILZ_YES', 'yes');
define('_MAILZ_NO', 'no');
define('_MAILZ_DELETE', 'Data will be deleted!');
define('_MAILZ_DELETED', 'Data deleted');
define('_MAILZ_UPDATED', 'Data updated');
define('_MAILZ_UPDATE_ERROR', 'Erorr updating database');
define('_MAILZ_DELETE_ERROR', 'Error deleting from database');
define('_MAILZ_STORE_ERROR', 'Error storing data');
define('_MAILZ_HTML', 'html');
define('_MAILZ_PREVIEW_AND_SEND', 'preview and send');
define('_MAILZ_TEXT', 'plain text');
define('_MAILZ_COMBINED', 'combined: text+html');
define('_MAILZ_INACTIVE', 'inactive');
define('_MAILZ_BACK', 'back');
define('_MAILZ_ACTIVE', 'aktive');
define('_MAILZ_SEARCH', 'search');
define('_MAILZ_POWERED_BY', 'powered by');
define('_MAILZ_YOU_LIKE_IT', 'This module is usefull and you like it');
define('_MAILZ_DONATE', 'support this module');

// main
define('_MAILZ_MYPROFILE_NAME_FIELD', 'You can choose a MyProfile data field that will later replace the string %%NAME%% in the sending process');
define('_MAILZ_BACKEND_SETTINGS', 'Global newsletter settings');
define('_MAILZ_CRON_URL', 'If a cron password is specified sending can be done automatically. The call of the cron url will put the newsletter in the sending queue and send the newsletter to all recipients. Please replace URL and ID for each newsletter. URL also can be seen in the newsletter configuration.');
define('_MAILZ_REPLACEMENTS', 'While sending takes place some replacements will be made automatically');

// plugins
define('_MAILZ_PLUGINS_DESCRIPTION', 'You can activate some plugins for your newsletter here');
define('_MAILZ_LOAD_NL_FIRST', 'Please choose a newsletter first!');
define('_MAILZ_ADD', 'add');
define('_MAILZ_PLUGIN_MANAGEMENT_FOR', 'Pluginmanagement for');
define('_MAILZ_PLUGIN_USE_TEXT', 'Just choose the plugin from the list and add some header and footer text for it. Also some parameters can be used.');
define('_MAILZ_PLUGIN_ADDED', 'Plugin was added!');
define('_MAILZ_PLEASE_DESCRIBE', 'Enter your text here');
define('_MAILZ_HEADER_HTML', 'This content will be shown above the output of the plugin for the contenttype html');
define('_MAILZ_FOOTER_HTML', 'This content will be shown below the output of the plugin for the contenttype html');
define('_MAILZ_HEADER_TEXT', 'This content will be shown above the output of the plugin for the contenttype text');
define('_MAILZ_FOOTER_TEXT', 'This content will be shown below the output of the plugin for the contenttype html');
define('_MAILZ_HIDE_PLUGINLIST', 'Hide plugin list');
define('_MAILZ_PLUGIN_PARAMS', 'Plugin parameter. Make pairs and separate them with "&". Parameter and value in a pair is always separated with "=".Example: param1,value1;param2,value2');
define('_MAILZ_NO_PLUGINS', 'No plugins added yet');
define('_MAILT_SWITCH_ERROR', 'Error occured while sorting the plugin order');
define('_MAILZ_POSITION', 'Position');
define('_MAILZ_PL_ID', 'Plugin ID');
define('_MAILZ_PLUGIN_DESCRIPTION', 'Plugin description');
define('_MAILZ_MODULENAME', 'Editor-Module');
define('_MAILZ_PREVIEW', 'Preview');
define('_MAILZ_PREVIEW_TEXT', 'text');
define('_MAILZ_PREVIEW_HTML', 'html');

// groups
define('_MAILZ_GROUP_DESCRIPTION', 'You can define several target groups here. Groups can be used in different newsletters and mailings and newsletters can use more than one group later.');
define('_MAILZ_GROUP_TITLE', 'Target group title');
define('_MAILZ_DESCRIPTION', 'Description for group (internal only)');
define('_MAILZ_SQL_QUERY', 'SQL-Query for resulting list of user IDs');
define('_MAILZ_API_CALL', 'API-Call in format MODNAME:TYPE:FUNC::param1,value1;param2,value2 with a resulting array of user IDs');
define('_MAILZ_STORE', 'store');
define('_MAILZ_SETTINGS_STORED', 'Store data');
define('_MAILZ_GROUP_CREATION_ERROR', 'Error while creating target group');
define('_MAILZ_NO_GROUPS', 'No groupsp defined yet');
define('_MAILZ_MODIFY', 'edit');
define('_MAILZ_ACTION', 'Actions');
define('_MAILZ_TITLE', 'Title');
define('_MAILZ_GROUP_ID', 'Group ID');
define('_MAILZ_QUERY', 'SQL-Query');
define('_MAILZ_API', 'API-Call');
define('_MAILZ_LOADED', 'Data loaded');
define('_MAILZ_LOAD_ERROR', 'Error loading data');
define('_MAILZ_RANGE', 'Range');
define('_MAILZ_SQL_REPLACEMENTS', 'The following replacements will always be applied to a SQL query');
define('_MAILZ_CUR_YEAR', 'actual year, 4 numbers');
define('_MAILZ_CUR_MONTH', 'actual month, 2 numbers');
define('_MAILZ_CUR_DAY', 'actual day, 2 numbers');

// newsletters
define('_MAILZ_NEWSLETTERS_DESCRIPTION', 'You are now able to define newsletters and mailings here. Active newsletters are subscribable by your users immediately.');
define('_MAILZ_NL_DESCRIPTION', 'Public description');
define('_MAILZ_USE_ARCHIVE', 'Put sent newsletters into the archive');
define('_MAILZ_CONTENT_TYPE', 'Content type of sent mails');
define('_MAILZ_IS_SUBSCRIBABLE', 'Newsletter should be subscribable');
define('_MAILZ_IS_PUBLIC', 'Sending and subscription should be public (guests will be able to subscribe');
define('_MAILZ_IS_INACTIVE', 'Newsletter activated');
define('_MAILZ_NL_CRONCODE', 'Cronjob-Password for automatical sending (optional, emtpy field = disabled)');
define('_MAILZ_NO_NEWSLETTERS', 'No Newsletters defined yet');
define('_MAILZ_NEWSLETTER_CREATION_ERROR', 'Error storing newsletter data');
define('_MAILZ_DEFINE_GROUPS_FIRST', 'Please create some target groups first!');
define('_MAILZ_MIN_DELAY', 'Individual delay for each mail');
define('_MAILZ_ID', 'ID');
define('_MAILZ_NL_DESCRIPTION', 'Public description for the newsletter');
define('_MAILZ_MANAGE_PREVIEW_SEND', 'Preview and send');
define('_MAILZ_SHOW_CREATE_NL_FORM', 'Show create new newsletter form');
define('_MAILZ_SUBSCRIPTIONS', 'Subscriptions');
define('_MAILZ_HIDE_CREATE_NL_FORM', 'Hide form');
define('_MAILZ_SERIAL_NR', 'Passes');
define('_MAILZ_NL_USE_ARCHIVE', 'Archive');
define('_MAILZ_NL_CONTENT_TYPE', 'Content');
define('_MAILZ_ADD_DATE_TO_SUBJECT', 'Add actual date to mail subject');
define('_MAILZ_FROMADDRESS', 'Use another emailadress than it is configures in Mailer module');
define('_MAILZ_PRIORITY', 'Priority for mail queue (1=high, 10=low)');
define('_MAILZ_NL_PUBLIC', 'Public');
define('_MAILZ_ACTIVE', 'active');
define('_MAILZ_GROUPS_SET_ERROR', 'Error storing relation');
define('_MAILZ_IGNORE_MAILER_TEMPLATES', 'Ignore the templates set up in advMailer module');

// send
define('_MAILZ_LOAD_NEWSLETTER_FIRST', 'Please load the newsletter first');
define('_MAILZ_SEND_TO_USER', 'Send mail with newsletter content to a specified user');
define('_MAILZ_SEND', 'send');
define('_MAILZ_USER_NOT_FOUND', 'User could not be found');
define('_MAILZ_SENDING_HINTS', 'The sending process will completely take place in the background.After the newsletter is created and sent successfully the newsletter data is being updated and - if configured - the newsletter is stored in the archive.');
define('_MAILZ_OPEN_PREVIEW', 'Open preview');
define('_MAILZ_NL_SENT_TO', 'Newsletter was send as a test to the user');
define('_MAILZ_SEND_PROCESS', 'Start creation and sending process');
define('_MAILZ_SEND_NOW', 'Start sending process now');
define('_MAILZ_QUEUED_WAIT', 'The newsletter was queued for creation and for sending. You can see the created newsletter in advMailers mail queue.');
define('_MAILT_QUEUE_ERROR', 'An error occured while trying to queue the newsletter for creation.');
define('_MAILZ_NEWSLETTER_ALREADY_IN_QUEUE', 'This newsletter is already in the queue and a new creation process can only be started after a sending process is finished!');


// subscriptions
define('_MAILZ_SUBSCRIPTIONS_FOR', 'Newsletter subscriptions');
define('_MAILZ_IP', 'IP adress');
define('_MAILZ_DATE', 'Date');
define('_MAILZ_CONFIRMED', 'confirmed');
define('_MAILZ_EMAIL', 'mail adresse');
define('_MAILZ_UNAME', 'user name');
define('_MAILZ_CONTENTTYPE', 'content type preference');
define('_MAILZ_SEARCH_UNAME', 'Search for a user');
define('_MAILZ_SEARCH_EMAIL', 'Use filter for email address');
define('_MAILZ_NO_SUBSCRIPTIONS', 'N osubscriptions found. If you used the search form, the user or the email address could not be found');
define('_MAILZ_SUBSCRIPTION_DELETION_ERROR', 'Error deleting a subscription');
define('_MAILZ_SUBSCRIPTION_DELETED', 'Subscription deleted');

// archive
define('_MAILZ_SHOW_ARCHIVE', 'Show newsletter archive');
define('_MAILZ_ARCHIVE_HINTS', 'You can view the archive in the user interface.');
