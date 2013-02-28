<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/*
 * get groups
 *
 * @param   $args['id']     int     optional, group id
 * @return  array
 */
function mailz_commonapi_getGroups($args)
{
    $id = (int) $args['id'];
    $where = 'inactive != 1';
    if ($id > 0) {
        $result = DBUtil::selectObjectByID('mailz_groups',$id);
        if ($result['inactive'] == 1) {
            $result = false;
        }
    } else {
        $result = DBUtil::selectObjectArray('mailz_groups',$where);
    }
    return $result;
}

/*
 * get newsletters
 *
 * @param   $args['id']             int     optional, newsletter id
 * @param   $args['inactive']       int     optional, show inactive, too (==1)
 * @param   $args['archived']       int     optional, show only newsletters having archived newsletters (==1)
 * @param   $args['subscribable']   int     optional, show only subscribable newsletters (==1)
 * @return  array
 */
function mailz_commonapi_getNewsletters($args)
{
    $id             = (int) $args['id'];
    $subscribable   = (int) $args['subscribable'];
    $inactive       = (int) $args['inactive'];
    $archived       = (int) $args['archived'];
    $whereA = array();
    if ($subscribable == 1) {
        $whereA[] = 'subscribable = 1';
    }
    if ($inactive != 1) {
        $whereA[] = 'inactive != 1';
    }
    if ($id > 0) {
        $result = DBUtil::selectObjectByID('mailz_newsletters',$id);
        if (($result['inactive'] == 1) && ($inactive != 1)) {
            $result = false;
        }
    } else {
        if ($archived == 1) {
            $tables = pnDBGetTables();
            $archivetable = $tables['mailz_archive'];
            $whereA[] = "id in (SELECT DISTINCT nid FROM ".$archivetable.")";
        }
        $where = implode(' AND ',$whereA);
        $result = DBUtil::selectObjectArray('mailz_newsletters',$where);
    }
    return $result;
}

/*
 * set target groups for a newsletter
 *
 * @param   $args['id']     int     newsletter id
 * @param   $args['groups'] array   list of groups
 * @return  boolean
 */
function mailz_commonapi_setNewsletterGroups($args)
{
    // Check for parameters
    if (!isset($args) || !isset($args['id']) || (!($args['id'] > 0))) {
        return false;
    }

    // No we will delete all old associations and write the new associations
    $where = 'nid = '.(int) $args['id'];
    $result = DBUtil::deleteWhere('mailz_group_relations',$where);
    if (!$result) {
        return false;
    }
    foreach ($args['groups'] as $key=>$value) {
        $obj = array(
            'nid'   => (int) $args['id'],
            'gid'   => $value
        );
        $result = DBUtil::insertObject($obj,'mailz_group_relations');
        if (!$result) {
            return false;
        }
    }
    return true;
}

/*
 * get target groups for a newsletter
 *
 * @param   $args['id']     int     newsletter id
 * @return  array
 */
function mailz_commonapi_getNewsletterGroups($args)
{
    // Check for parameters
    if (!isset($args) || !isset($args['id']) || (!($args['id'] > 0))) {
        return false;
    }

    // No we will delete all old associations and write the new associations
    $where = 'nid = '.(int) $args['id'];
    $result = DBUtil::selectObjectArray('mailz_group_relations',$where);
    if (!$result) {
        return false;
    } else {
        $relations = array();
        foreach ($result as $relation) {
            $relations[] = $relation['gid'];
        }
    }
    return $relations;
}

/*
 * get all plugins
 *
 * @return  array
 */
function mailz_commonapi_getNewsletterPlugins($args)
{
    $nid = (int) $args['nid'];
    if (!($nid > 0)) {
        return false;
    }

    $where = "nid = ".$nid;
    $orderby = "position ASC";

    $plugins = DBUtil::selectObjectArray('mailz_plugins',$where,$orderby);
    $pl_result = array();
    foreach ($plugins as $plugin) {
        $plugin['previous'] = $previous;
        $pl_result[] = $plugin;
        $previous = $plugin['id'];

    }
    foreach (array_reverse($pl_result) as $plugin) {
        $plugin['next'] = $next;
        $plx_result[] = $plugin;
        $next = $plugin['id'];

    }
    return array_reverse($plx_result);
}

/*
 * sort order for newsletter plugins
 *
 * @param   $args['nid']    int     newsletter id
 * @return  boolean;
 */
function mailz_commonapi_setNewsletterPluginOrder($args)
{
    $plugins =
    $nid = (int) $args['nid'];
    if (!($nid > 0)) {
        return false;
    }

    $where = "nid = ".$nid;
    $orderby = "id DESC"; // Plugin that was added latest should become first position

    $result = DBUtil::selectObjectArray('mailz_plugins',$where,$orderby);
    $counter = 0;
    foreach ($result as $obj) {
        $counter++;
        if ($counter != $obj['position']) {
            $obj['position'] = $counter;
            $res = DBUtil::updateObject($obj,'mailz_plugins');
            if (!$res) {
                return false;
            }
        }
    }
    return true;
}

/*
 * get all plugins
 *
 * @param   $args['module']     string      module name, optional
 * @param   $args['pluginid']   int         module's plugin internal id
 * @return  array
 */
function mailz_commonapi_getPlugins($args)
{
    $pluginid   = $args['pluginid'];
    $module     = $args['module'];
    $mods       = pnModGetAllMods();//pnModGetUserMods();
    foreach ($mods as $mod) {
//      	if ($mod['displayname'] != "MyProfile") {
	      	$file_modules	= 'modules/'.$mod['directory'].'/pnmailzapi.php';
	      	$file_system	= 'system/'.$mod['directory'].'/pnmailzapi.php';
	      	$found = false;
	      	if (file_exists($file_system)) {
			    $found = true;
			    $file = $file_system;
			}
	      	else if (file_exists($file_modules)) {
	      	  	$found = true;
			    $file = $file_modules;
			}
			if ($found) {
		        $result = pnModAPIFunc($mod['name'],'mailz','getPlugins');
                foreach ($result as $plugin) {
                    if ($plugin['pluginid'] > 0) {
                        if (!isset($pluginid) && !isset($module)) {
                            $res[] = $plugin;
                        } else if (($pluginid == $plugin['pluginid']) && ($module == $plugin['module'])){
                            $res[] = $plugin;
                        }
                    }
                }
			}
//		}
	}
	return $res;
}

/*
 * get plugin content and return it
 *
 * @param   $args['id']             int         plugin id (not pluginid!)
 * @param   $args['contenttype']    string      h or t for html or text
 * @param   $args['uid']            int         use specific user id
 * @param   $args['nid']            int         newsletter id
 * @param   $args['last']           datetime    timtestamp of last newsletter
 * @return  output
 */
function mailz_commonapi_getPluginContent($args)
{
    $contenttype = (string) $args{'contenttype'};
    if (!isset($contenttype) || (($contenttype != 'h') && ($contenttype != 't'))) {
        return 'NO CONTENT TYPE GIVEN FOR PLUGIN '.$id;
    }
    $uid = (int) $args['uid'];
    $id  = (int) $args['id'];
    // Get plugin
    if (!($id > 0)) {
        return 'NO PLUGIN ID GIVEN!';
    }
    if (!($uid > 1)) {
        $uid = pnUserGetVar('uid');
    }
    // Get plugin and create output
    $plugin = DBUtil::selectObjectByID('mailz_plugins',$id);
    if (!$plugin || (!($plugin['id'] == $id))) {
        return 'PLUGIN '.$id.' NOT FOUND IN DATABASE';
    }

    // extract parameters
    $new_params = array();
    $param_pairs = explode("&",$plugin['params']);
    foreach ($param_pairs as $pair) {
        $dummy = explode("=",$pair);
        $new_params[$dummy[0]] = $dummy[1];
    }
    $params = array (
        'uid'           => $uid,
        'contenttype'   => $contenttype,
        'pluginid'      => $plugin['pluginid'],
        'nid'           => $args['nid'],
        'last'          => $args['last'],
        'params'        => $new_params
    );

    $content = pnModAPIFunc($plugin['module'],'mailz','getContent',$params);
    if ($contenttype == 'h') {
        $output="<p><h2 id=\"".$plugin['id']."\">".$plugin['title']."</h2></p><p>".$plugin['header_html'].$content.$plugin['footer_html']."</p>";
    } else {
        $output="\n\n\n".$plugin['title']."\n--------------------\n".$plugin['header_text'].$content.$plugin['footer_text'];
    }

    return $output;
}

/**
 * make replacements in text
 *
 * @param   $args['text']       string      output
 * @param   $args['email']      string      email address if different from user's email
 * @param   $args['uid']        int         user id for the recpipient
 * @return  string
 */
function mailz_commonapi_outputReplacements($args)
{
    $dom = ZLanguage::getModuleDomain('mailz');

    // Get Parameters
    $output = $args['text'];
    $uid    = $args['uid'];
    $email  = $args['email'];
    if (!(isset($email) && ($email != ''))) {
        $email = pnUserGetVar('email',$uid);
    }

    // Replace some parts of the content now
    $modvars = pnModGetVar('mailz');
    if (isset($modvars['myprofile_name']) && ($modvars['myprofile_name'] != '')) {
        // Load user's profile
        $profile = pnModAPIFunc('MyProfile','user','getProfile',array('uid' => $uid));
        $output = str_replace('%%NAME%%',$profile[$modvars['myprofile_name']]['value'], $output);
    }
    $output = str_replace('%%EMAIL%%', $email, $output);
    $output = str_replace('%%UID%%', $uid, $output);
    if ($uid > 1) {
        $output = str_replace('%%UNAME%%', pnUserGetVar('uname',$uid), $output);
    } else {
        $output = str_replace('%%UNAME%%', __('Guest', $dom), $output);
    }

    // Return replaced output
    return $output;
}

/**
 * get newsletter output
 *
 * @param   $args['id']             int     newsletter id
 * @param   $args['contenttype']    string  h or t
 * @param   $args['uid']            int     user id to create newsletter for
 * @return  string
 */
function mailz_commonapi_getNewsletterOutput($args)
{
    $id          = (int)    $args['id'];
    $contenttype = (string) $args['contenttype'];
    $uid         = (int)    $args['uid'];
    if (($contenttype != 'h') && ($contenttype != 't')) {
        return false;
    }

    // Get newsletter
    $newsletter = pnModAPIFunc('mailz', 'common', 'getNewsletters', array('id' => $id));
    if (!$newsletter) {
        return false;
    }

    // Get plugins
    $plugins = pnModAPIFunc('mailz', 'common', 'getNewsletterPlugins', array('nid' => $id));

    foreach ($plugins as $plugin) {
        $cargs = array(
            'id'            => $plugin['id'],
            'contenttype'   => $contenttype,
            'uid'           => $uid,
            'nid'           => $newsletter['id'],
            'last'          => $newsletter['last']
        );
        $nl_content .= pnModAPIFunc('mailz', 'common', 'getPluginContent', $cargs);
    }
    return $nl_content;
}

/**
 * get recipients for a group
 *
 * @param   $args['id']     int     group id
 * @return  array
 */
function mailz_commonapi_getGroupRecipients($args)
{
    $id = (int) $args['id'];
    $recipients = array();
    $group = pnModAPIFunc('mailz', 'common', 'getGroups', array('id' => $id));
    if (!$group) {
        return false;
    } else {
        if ($group['query'] != '') {
            $group['query'] = pnModAPIFunc('mailz', 'common', 'sqlReplacements', $group);
            $result = DBUtil::executeSql($group['query']);
            foreach ($result as $uid) {
                $uid = (int) $uid[0];
                if ($uid > 1) {
                    $recipients[$uid] = $uid;
                }
            }
        }
        if ($group['api'] != '') {
            $dummy = explode(':',$group['api']);
            $mod = $dummy[0];
            $type = $dummy[1];
            $func = $dummy[2];
            $args = $dummy[3];
            $paramsdumy = explode(';',$args);
            $argsArray = array();
            foreach ($paramsdummy as $item) {
                $d = explode(',',$item);
                $key = $d[0];
                $value = $d[1];
                $argsArray[$key] = $value;
            }
            $args = $argsArray;
            $result = pnModAPIFunc($mod, $type, $func, $args);
            if ($result) {
                foreach ($result as $item) {
                    $recipients[$item] = $item;
                }
            }
        }
    }
    return $recipients;
}

/**
 * queue newsletter for sending and creation - the main function that initiates the mailing
 *
 * @param   $args['id']     int     newsletter id
 * @return  boolean
 */
function mailz_commonapi_queueNewsletter($args)
{
    $dom = ZLanguage::getModuleDomain('mailz');

    $id = (int) $args['id'];
    $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));

    // Check for newsletter in queue that was not yet sent
    $where = 'nid = '.$newsletter['id'];
    $res = DBUtil::selectObjectCount('mailz_queue',$where);
    if ($res > 0) {
        // If user has admin permissions for mailz module display message!
        if (SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
            LogUtil::registerError(__('This newsletter is already in the queue and a new creation process can only be started after a sending process is finished!', $dom));
        }
        return false;
    }
    $recipients = false;
    if (!$newsletter) {
        return false;
    } else {
        // get groups
        $groups = pnModAPIFunc('mailz', 'common', 'getNewsletterGroups', array('id' => $id));
        // get recipients

        // Is newsletter subscribable and should these mails also be sent?
        if ($newsletter['subscribable'] == 1) {
            $where = "nid = ".$newsletter['id']." AND confirmed = 1";
            $result = DBUtil::selectObjectArray('mailz_subscriptions',$where);
            $counter = 0;
            foreach ($result as $item) {
                if ($item['uid'] > 1) {
                  $obj[$item['uid']] = array (
                    'nid'           => $id,
                    'contenttype'   => $item['contenttype'],
                    'uid'           => $item['uid']
                  );
                } else {
                    $counter--;
                    $obj[$counter] = array (
                        'nid'           => $id,
                        'contenttype'   => $item['contenttype'],
                        'uid'           => 0,
                        'email'         => $item['email']
                    );
                }
            }
        }

        // Now retrieve target groups
        foreach ($groups as $group) {
            $recipients = pnModAPIFunc('mailz', 'common', 'getGroupRecipients', array('id' => $group));
            // Insert items into working queue
            // If a person is listed in multiple groups that person will only get one mail
            foreach ($recipients as $uid) {
                $obj[$uid] = array(
                        'nid'   => $id,
                        'uid'   => $uid
                    );
            }
        }
        // Object for archivating order
        $counter = (int)count($obj);
        if ($newsletter['archive'] == 1) {
            $obj[] = array(
                'nid'   => $id,
                'uid'   =>  -999,
                'email' => $counter
            );
        }
        // And another for updating newsletter core data
        $obj[] = array(
            'nid'   => $id,
            'uid'   =>  -9999
        );
        // Insert all into DB
        $result = DBUtil::insertObjectArray($obj,'mailz_queue');
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }
}

/**
 * send a newsletter
 *
 * @param   $args['id']          int     newsletter id
 * @param   $args['uid']         int     user id
 * @param   $args['contenttype'] char    h, t or c
 * @param   $args['email']       string  email for unreg. subscribers
 * @return bool
 */
function mailz_commonapi_sendNewsletter($args)
{
    // Cache newsletter
    static $nl_cache;
    $id = (int) $args['id'];
    if (!($id > 0)) {
        return false;
    }

    // Check parameters, get and cache newsletter
    if (!isset($nl_cache) || !isset($nl_cache[$id]) || ($nl_cache[$id]['id'] != $id)) {
        $newsletter = pnModAPIFunc('mailz', 'common', 'getNewsletters', array('id' => $id));
        if (!$newsletter) {
            return false;
        } else {
            $nl_cache[$id] = $newsletter;
        }
    }
    $newsletter = $nl_cache[$id];

    // Get email address for the user.
    $uid = $args['uid'];
    if ($uid > 1) {
        $toaddress = pnUserGetVar('email',$uid);
    } else {
        $toaddress = $args['email'];
    }

    if ((!isset($toaddress) || ($toaddress == '')) && ($uid >= 0)) {
        return false;
    }

    // If the newsletter is available in both formats and there is no format
    // specified we will take html format - Todo: Take user's preferences later
    if ($newsletter['contenttype'] == 'c') {
        $uc = (string) $args['contenttype'];
        if ($uc == 'h') {
            $newsletter['contenttype'] = 'h';
        } else if ($uc == 't') {
            $newsletter['contenttype'] = 't';
        } else {
            $newsletter['contenttype'] = 'h';
        }
    }

    // Send mail now
    // Create body and subject for email and set content tyoe
    $subject = $newsletter['title'];
    $body = pnModAPIFunc('mailz','common','getNewsletterOutput',array('id' => $newsletter['id'], 'uid' => $uid, 'contenttype' => $newsletter['contenttype']));
    $html = ($newsletter['contenttype'] == 'h');

    // Set name or handle archive and core data updates for newsletter's core data here
    if ($uid > 1) {
        $toname = pnUserGetVar('uname');
    } else  if ($uid == -999) {
        // put into archive!
        if ($newsletter['archive'] == 0) {
            // Just return true if no archive should be done. This case should never
            // happen because if there is no archive to do queueNewsletter function
            // should not put this element inside the mailz working queue
            return true;
        }
        // Create newsletter for archive now and insert into DB
        $obj = array(
                'nid'           => $newsletter['id'],
                'subject'       => $subject,
                'body_html'     => pnModAPIFunc('mailz','common','getNewsletterOutput',array('id' => $newsletter['id'], 'uid' => 0, 'contenttype' => 'h')),
                'body_text'     => pnModAPIFunc('mailz','common','getNewsletterOutput',array('id' => $newsletter['id'], 'uid' => 0, 'contenttype' => 't')),
                'public'        => $newsletter['public'],
                'date'          => date("Y-m-d H:i:s",time()),
                'recipients'    => $args['email']   // Email field is used to transport the number or recipients
            );
        $result = DBUtil::insertObject($obj,'mailz_archive');
        // Object inserted into archive table and returning result now.
        return $result;
    } else if ($uid == -9999) {
        // update newsletter data
        $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
        $newsletter['last'] = date("Y-m-d H:i:s",time());
        $newsletter['serialnumber']++;
        // Update newsletter object and return result
        $result = DBUtil::updateObject($newsletter,'mailz_newsletters');
        return $result;
    }

    // Replacements
    $subject = pnModAPIFunc('mailz','common','outputReplacements',array('text' => $subject, 'uid' => $uid, 'email' => $toaddress));
    $body    = pnModAPIFunc('mailz','common','outputReplacements',array('text' => $body,    'uid' => $uid, 'email' => $toaddress));
    $body    = str_replace('="/','="'.pnGetBaseURL(), $body); // make relative urls absolute

    // Call mailer api
    if ($newsletter['delay'] > 0) {
        $date = date("Y-m-d H:i:s",(time()+(60*$newsletter['delay'])));
    }
    if ($newsletter['adddate'] == 1) {
        $subject = $subject.' ('.date("d.m.Y",time()).')';
    }
    $mailerargs = array(
            'html'          => $html,
//            'toaddress'     => 'netbiker@netbiker.de',
            'toaddress'     => $toaddress,
            'subject'       => $subject,
            'notemplates'   => $newsletter['ignoretemplates'],
            'priority'      => $newsletter['priority'],
            'body'          => $body,
            'date'          => $date,
            'fromaddress'   => $newsletter['fromaddress']
        );
    $result = pnModAPIFunc('Mailer','user','sendMessage',$mailerargs);
    return $result;
}

/**
 * system init function for mail queue processing
 *
 * @return void
 */
function mailz_commonapi_systeminit()
{
    if (pnUserLoggedIn()) {
        // Get lock; lock is timestamp
        $lock = (int) pnModGetVar('mailz','lock');
        $lock = $lock + (3*60);
        $ts = time();
        if ($lock > $ts) {
            // lock is existing and not old enough to be destroyed
        } else {
            // Set lock
            pnModSetVar('mailz','lock',$ts);
            // numbers of newsletters that should be generated with one site call
            $numrows = 10;
            // get $numrows items of mail queue
            // Order: newsletter by newsletter, lowest id (archivate order!) last, highest first
            $orderby = 'nid ASC, uid DESC, email DESC';
            $items = DBUtil::selectObjectArray('mailz_queue','',$orderby,'',$numrows);
            // process items
            foreach ($items as $item) {
                    $result = pnModAPIFunc('mailz','common','sendNewsletter',array('id' => $item['nid'], 'uid' => $item['uid'], 'email' => $item['email'], 'contenttype' => $item['contenttype']));
    //                if ($result) {
    // We will handle each mail as sent at the moment. Error handling is written down on the ToDo list ;-)
                        DBUtil::deleteObject($item,'mailz_queue');
    //                }
            }
            pnModDelVar('mailz','lock');
        }
    }
}

/**
 * replace sql query
 *
 * @param   $args['query']      string          sql query
 * @return  string
 */
function mailz_commonapi_sqlReplacements($args)
{
    $t = $args['query'];
    $t = str_replace('$$$YEAR$$$',  date("Y",time()), $t);
    $t = str_replace('$$$MONTH$$$', date("m",time()), $t);
    $t = str_replace('$$$DAY$$$',   date("d",time()), $t);
    return $t;
}

/**
 * subscibe to a newsletter
 *
 * @param   $args['uid']            int     optional user id
 * @param   $args['email']          string  optional email address
 * @param   $args['contenttype']    string  optional content type
 * @param   $args['id']             int     id of newsletter to apply action
 * @return  boolean
 */
function mailz_commonapi_subscribe($args)
{
    $dom = ZLanguage::getModuleDomain('mailz');

    $id          = (int) $args['id'];
    $uid         = (int) $args['uid'];
    $contenttype = (string) $args['contenttype'];
    $email       = (string) $args['email'];
    $email       = strtolower($email);
    $newsletter  = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
    if (!$newsletter || ($newsletter['id'] != $id)) {
        return false;
    }

    if (($contenttype != 'h') && ($contenttype != 't')) {
        $contenttype = 'c';
    }

    // Check for existing subscription
    if (pnModAPIFunc('mailz','common','isSubscribed',$args)) {
        return false;
    }

    // Construct object
    if ($uid > 1) {
        $obj = array(
            'nid'           => $id,
            'uid'           => $uid,
            'date'          => date("Y-m-d H:i:s"),
            'ip'            => $_SERVER['SERVER_ADDR'],
            'contenttype'   => $contenttype,
            'confirmed'     => 1
        );
    } else {
        $code = rand(100000000000000,999999999999999);
        $obj = array(
            'nid'           => $id,
            'date'          => date("Y-m-d H:i:s"),
            'ip'            => $_SERVER['SERVER_ADDR'],
            'contenttype'   => $contenttype,
            'email'         => $email,
            'code'          => $code
        );
    }

    // Write object to DB if code == ''
    $result = DBUtil::insertObject($obj,'mailz_subscriptions');
    if (($code != '') && $result) {
        $subject = __('Please confirm newsletter subscription', $dom);
        $render = pnRender::getInstance('mailz');
        $render->assign('obj', $obj);
        $render->assign('newsletter', $newsletter);
        $body = $render->fetch('mailz_email_confirm.htm');
        // Send email
        $margs = array (
            'toaddress' => $email,
            'subject'   => $subject,
            'body'      => $body,
            'html'      => true,
            'priority'  => 1,
            'quiet'     => 1
        );
        $mail = pnModAPIFunc('Mailer','user','sendmessage',$margs);
        if (!$mail) {
            DBUtil::deleteObject($obj,'mailz_subscriptions');
            return false;
        } else {
            return true;
        }
    } else {
        return $result;
    }
}
/**
 * unsubscibe to a newsletter
 *
 * @param   $args['uid']            int     optional user id
 * @param   $args['email']          string  optional emaila ddress
 * @param   $args['contenttype']    string  optional emaila ddress
 * @param   $args['id']             int     id of newsletter to apply action
 * @return  boolean
 */
function mailz_commonapi_unsubscribe($args)
{
    $dom = ZLanguage::getModuleDomain('mailz');

    $id          = (int) $args['id'];
    $uid         = (int) $args['uid'];
    $email       = (string) $args['email'];
    $email       = strtolower($email);
    $newsletter  = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
    if (!$newsletter || ($newsletter['id'] != $id)) {
        return false;
    }

    // Check for existing subscription
    if (!pnModAPIFunc('mailz','common','isSubscribed',$args)) {
        return false;
    }

    if ($uid > 1) {
        $where = 'nid = '.$id.' AND uid = '.$uid;
        $obj = DBUtil::selectObject('mailz_subscriptions',$where);
        if (!$obj) {
            return false;
        } else {
            $result = DBUtil::deleteObject($obj,'mailz_subscriptions');
            if ($result) {
                LogUtil::registerStatus(__('Subscription cancelled!', $dom));
            }
            return $result;
        }

    } else {
        // Send a email to recipient with a generated validation code
        $where = "email like '".$email."' AND nid = ".$id." AND uid = 0";
        $obj = DBUtil::selectObject('mailz_subscriptions',$where);
        if (!$obj) {
            return false;
        } else {
            // Generate Code for user
            $obj['code'] = rand(100000000000000,999999999999999);
            $subject = __('Please confirm the deletion of your subscription', $dom);
            $render = pnRender::getInstance('mailz');
            $render->assign('obj', $obj);
            $render->assign('newsletter', $newsletter);
            $body = $render->fetch('mailz_email_confirm_deletion.htm');
            // Send email
                $margs = array (
                'toaddress' => $obj['email'],
                'subject'   => $subject,
                'body'      => $body,
                'html'      => true,
                'priority'  => 1,
                'quiet'     => 1
            );
            $mail = pnModAPIFunc('Mailer','user','sendmessage',$margs);
            if (!$mail) {
                return false;
            } else {
                DBUtil::updateObject($obj,'mailz_subscriptions');
                LogUtil::registerStatus(__('An email with a confirmation code was sent!', $dom));
                return true;
            }
        }
    }

    return false;
}

/**
 * update subscription to a newsletter
 *
 * @param   $args['uid']            int     optional user id
 * @param   $args['email']          string  optional emaila ddress
 * @param   $args['contenttype']    string  optional emaila ddress
 * @param   $args['id']             int     id of newsletter to apply action
 * @return  boolean
 */
function mailz_commonapi_update($args)
{
    $id          = (int) $args['id'];
    $uid         = (int) $args['uid'];
    $contenttype = (string) $args['contenttype'];
    $newsletter  = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
    if (!$newsletter || ($newsletter['id'] != $id)) {
        return false;
    }

    // Check for existing subscription
    if (!pnModAPIFunc('mailz','common','isSubscribed',$args)) {
        return false;
    }

    $where = 'nid = '.$id.' AND uid = '.$uid;
    $obj = DBUtil::selectObject('mailz_subscriptions',$where);
    if (!$obj) {
        return false;
    } else {
        $obj['contenttype'] = $contenttype;
        $result = DBUtil::updateObject($obj,'mailz_subscriptions');
        return $result;
    }

    return false;
}

/**
 * check for subscription
 *
 * @param   $args['id']     int     newsletter id
 * @param   $args['uid']    int     optional, uid
 * @param   $args['email']  string  optional, email address
 * @return  boolean
 */
function mailz_commonapi_isSubscribed($args)
{
    // Get parameters
    $id    = (int)    $args['id'];
    $uid   = (int)    $args['uid'];
    $email = (string) $args['email'];

    if (!($id > 0)) {
        return false;
    }

    // Check for subscription
    if ($uid > 1) {
        $where2 = 'uid = '.$uid;
    } else {
        $where2 = "email like '".DataUtil::formatForStore($email)."'";
    }
    $where = 'nid = '.$id.' AND '.$where2;
    $result = DBUtil::selectObjectCount('mailz_subscriptions',$where);
    if ($result && ($result > 0)) {
        return true;
    } else {
        return false;
    }

}

/**
 * get Subscriptions for a Newsletter
 *
 * @param   $args['id']             int     newsletter id
 * @param   $args['unconfirmed']    int     ==1, optional to show unconfirmed, too
 * @param   $args['limitoffset']    int     for pager usage
 * @param   $args['numrows']        int     for pager - items to show on a single page
 * @param   $args['uname']          string  optional for user filter
 * @param   $args['email']          string  optional for email filter
 * @return  array
 */
function mailz_commonapi_getSubscriptions($args)
{
    // Check parameters
    $nl          = pnModAPIFunc('mailz','common','getNewsletters',$args);
    $id          = (int)    $args['id'];
    $unconfirmed = (int)    $args['unconfirmed'];
    $uname       = (string) $args['uname'];
    $email       = (string) $args['email'];
    if (!$nl || ($nl['id'] != $id)) {
        return false;
    }
    // Construct SQL where part
    $wa = array();
    if ($unconfirmed != 1) {
        $wa[] = 'confirmed != 0';
    }
    $wa[] = 'nid = '.$id;
    if (isset($email) && ($email != '')) {
        $wa[] = "email like '%".DataUtil::formatForStore($email)."%'";
    }
    if (isset($uname) && ($uname != '')) {
        $uid = pnUserGetIDFromName($uname);
        if ($uid > 2) {
            $wa[] = "uid = ".$uid;
        } else {
            return false;
        }
    }
    $where = implode(' AND ',$wa);
    // Get objects
    if ($count == 1) {
        $recipients = DBUtil::selectObjectCount('mailz_subscriptions',$where);
    } else {
        $recipients = DBUtil::selectObjectArray('mailz_subscriptions',$where,'',$args['limitoffset'],$args['numrows']);
    }
    return $recipients;
}

/**
 * get Newsletter archive
 *
 * @param   $args['nid']            int     general newsletter id
 * @param   $args['id']             int     get specific newsletter
 * @param   $args['count']          int     count items only
 * @param   $args['limitoffset']    int     optional limit offset
 * @param   $args['numrows']        int     optional items to display
 * @return  object
 */
function mailz_commonapi_getArchivedNewsletter($args)
{
    $id    = (int) $args['id'];
    $nid   = (int) $args['nid'];
    $count = (int) $args['count'];
    if (!($id > 0) && !($nid > 0)) {
        return false;
    }
    if ($id > 0) {
        $result = DBUtil::selectObjectByID('mailz_archive',$id);
        if (($count == 1) && $result) {
            return 1;
        }
    } else {
        $where   = 'nid = '.$nid;
        $orderby = 'date DESC';
        if ($count == 1) {
            $result = DBUtil::selectObjectCount('mailz_archive',$where);
        } else {
            $result = DBUtil::selectObjectArray('mailz_archive',$where,$orderby,$args['limitoffset'],$args['numrows']);
        }
    }
    return $result;
}

