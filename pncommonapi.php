<?php

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
 * @param   $args['id']         int     optional, newsletter id
 * @param   $args['inactive']   int     optional, show inactive, too (==1)
 * @return  array
 */
function mailz_commonapi_getNewsletters($args)
{
    $id         = (int) $args['id'];
    $inactive   = (int) $args['inactive'];
    if ($inactive != 1) {
        $where = 'inactive != 1';
    }
    if ($id > 0) {
        $result = DBUtil::selectObjectByID('mailz_newsletters',$id);
        if (($result['inactive'] == 1) && ($inactive != 1)) {
            $result = false;
        }
    } else {
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
    $mods       = pnModGetUserMods();
    foreach ($mods as $mod) {
      	if ($mod['displayname'] != "MyProfile") {
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
		}
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

    $output = pnModAPIFunc('mailz','common','outputReplacements',array('text' => $output, 'uid' => $uid));
    return $output;
}

/**
 * make replacements in text
 *
 * @param   $args['text']       string      output 
 * @return  string
 */
function mailz_commonapi_outputReplacements($args)
{
    $output = $args['text'];
    $uid = $args['uid'];
    // replace name if needed
    $modvars = pnModGetVar('mailz');
    if (isset($modvars['myprofile_name']) && ($modvars['myprofile_name'] != '')) {
        // Load user's profile
        $profile = pnModAPIFunc('MyProfile','user','getProfile',array('uid' => $uid));
        $output = str_replace('%%NAME%%',$profile[$modvars['myprofile_name']]['value'], $output);
    }
    $output = str_replace('%%EMAIL%%', pnUserGetVar('email',$uid), $output);
    $output = str_replace('%%UID%%', $uid, $output);
    $output = str_replace('%%UNAME%%', pnUserGetVar('uname',$uid), $output);

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
    if (!($uid >= 1) || (!($id > 0)) || (($contenttype != 'h') && ($contenttype != 't'))) {
        return false;
    }

    // Get newsletter
    $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
    if (!$newsletter) {
        return false;
    }
    
    // Get plugins
    $plugins = pnModAPIFunc('mailz','common','getNewsletterPlugins',array('nid' => $id));

    foreach ($plugins as $plugin) {
        $cargs = array(
            'id'            => $plugin['id'],
            'contenttype'   => $contenttype,
            'uid'           => $uid,
            'nid'           => $newsletter['id'],
            'last'          => $newsletter['last']
        );
    $nl_content.= pnModAPIFunc('mailz','common','getPluginContent',$cargs);
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
    $group = pnModAPIFunc('mailz','common','getGroups',array('id' => $id));
    if (!$group) {
        return false;
    } else {
        if ($group['query'] != '') {
            $group['query'] = pnModAPIFunc('mailz','common','sqlReplacements',$group);
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
    //        print "mod $mod type $type func $func";
            foreach ($paramsdummy as $item) {
                $d = explode(',',$item);
                $key = $d[0];
                $value = $d[1];
                $argsArray[$key]=$value;
            }
            $args = $argsArray;
            $result = pnModAPIFunc($mod,$type,$func,$args);
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
 * queue newsletter for sending and creation
 *
 * @param   $args['id']     int     newsletter id
 * @return  boolean
 */
function mailz_commonapi_queueNewsletter($args)
{
    $id = (int) $args['id'];
    $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
    
    // Check for newsletter in queue that was not yet sent
    $where = 'nid = '.$newsletter['id'];
    $res = DBUtil::selectObjectCount('mailz_queue',$where);
    print $res;
    if ($res > 0) {
        // If user has admin permissions for mailz module display message!
        if (SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
            LogUtil::registerError(_MAILZ_NEWSLETTER_ALREADY_IN_QUEUE);
        }
        return false;
    }
    $recipients = false;
    if (!$newsletter) {
        return false;
    } else {
        // get groups
        $groups = pnModAPIFunc('mailz','common','getNewsletterGroups',array('id' => $id));
        if (!$groups) {
            return false;
        } else {
            // get recipients
            foreach ($groups as $group) {
                $recipients = pnModAPIFunc('mailz','common','getGroupRecipients',array('id' => $group));
                if (!$recipients) {
                    return false;
                } else {
                    // Insert items into working queue
                    foreach ($recipients as $uid) {
                        $obj[] = array(
                                'nid'   => $id,
                                'uid'   => $uid
                            );
                    }
                    // Last object for archivating roder
                    $obj[0] = array(
                        'nid'   => $id,
                        'uid'   =>  -999
                    );
                    // And another for updating newsletter data
                    $obj[0] = array(
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
        }
    }
}

/**
 * send a newsletter
 *
 * @param   $args['id']         int     newsletter id
 * @param   $args['uid']        int     user id
 * @param   $args['email']      string  email for unreg. subscribers
 * @return bool
 */
function mailz_commonapi_sendNewsletter($args)
{
    static $nl_cache;
    $id = (int) $args['id'];
    if (!($id > 0)) {
        return false;
    }
    
    if (!isset($nl_cache) || !isset($nl_cache[$id]) || ($nl_cache[$id]['id'] != $id)) {
        $newsletter = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id));
        if (!$newsletter) {
            return false;
        } else {
            $nl_cache[$id] = $newsletter;
        }
    }
    
    $newsletter = $nl_cache[$id];
//    prayer($newsletter);

    // Send this Newsletter for this user id now
    $uid = $args['uid'];
    $toaddress = pnUserGetVar('email',$uid);
    if ( !isset($toaddress) || ($toaddress == '') && ($uid >= 0)) {
        return false;
    }

    // ToDo later maybe
    if ($newsletter['contenttype'] == 'c') {
        $newsletter['contenttype'] = 'h';
    }
    
    // Send mail
    $subject = $newsletter['title'];
    $subject = pnModAPIFunc('mailz','common','outputReplacements',array('text' => $subject, 'uid' => $uid));

    $body = pnModAPIFunc('mailz','common','getNewsletterOutput',array('id' => $newsletter['id'], 'uid' => $uid, 'contenttype' => $newsletter['contenttype']));
    
    if ($newsletter['contenttype'] == 'h') {
        $header = array('header' => '\nMIME-Version: 1.0\nContent-type: text/html');
        $html = true;
    } else {
        $header = array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain');
        $html = false;
    }
    
    if ($uid > 1) {
        $toname = pnUserGetVar('uname');
    } else  if ($uid == -999) {
        // put into archive!
        if ($newsletter['archive'] == 0) {
            return true;
        }
        $obj = array(
                'nid'       => $newsletter['id'],
                'subject'   => $subject,
                'body_html' => pnModAPIFunc('mailz','common','getNewsletterOutput',array('id' => $newsletter['id'], 'uid' => 1, 'contenttype' => 'h')),
                'body_text' => pnModAPIFunc('mailz','common','getNewsletterOutput',array('id' => $newsletter['id'], 'uid' => 1, 'contenttype' => 't')),
                'public'    => $newsletter['public'],
                'date'      => date("Y-m-h H:i:s",time())
            );
        $result = DBUtil::insertObject($obj,'mailz_archive');
        return $result;
    } else if ($uid == -9999) {
        // update newsletter data
        $newsletter['last'] = date("Y-m-d H:i:s",time());
        $newsletter['serialnumber']++;
        $result = DBUtil::updateObject($newsletter,'mailz_newsletters');
        return $result;
    }

    // Call mailer api
    if ($newsletter['delay'] > 0) {
        $date = date("Y-m-d H:i:s",(time()+(60*$newsletter['delay'])));
    }
    if ($newsletter['adddate'] == 1) {
        $subject = $subject.' ('.date("d.m.Y",time()).')';
    }
    // Replace some strings
    $body = str_replace('="/','="'.pnGetBaseURL(), $body);
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
    $uid = pnUserGetVar('uid');
    $numrows = 10;
    if ($uid > 1) {
        // Get lock; lock is timestamp
        $lock = (int) pnModGetVar('mailz','lock');
        $lock = $lock + (3*60);
        $ts = time();
        if ($lock > $ts) {
            // lock is existing and not old enough to be destroyed
        } else {
            // Set lock
            pnModSetVar('mailz','lock',$ts);
            // get $numrows items of mail queue
            // Order: newsletter by newsletter, lowest id (archivate order!) last, highest first
            $orderby = 'email DESC, nid ASC, uid DESC';
            $items = DBUtil::selectObjectArray('mailz_queue',$where,$orderby,-1,$numrows);
            // process items
            foreach ($items as $item) {
                $result = pnModAPIFunc('mailz','common','sendNewsletter',array('id' => $item['nid'], 'uid' => $item['uid'], 'email' => $item['email']));
                if ($result) {
                    DBUtil::deleteObject($item,'mailz_queue');
                }
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