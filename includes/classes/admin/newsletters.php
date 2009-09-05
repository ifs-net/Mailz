<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class mailz_admin_newslettersHandler
{
    var $nl;
    function initialize(&$render)
    {
        // Get groups for target group selection
        $groups = pnModAPIFunc('mailz','common','getGroups');
        if (!$groups || (!(count($groups) > 0))) {
            LogUtil::registerStatus(_MAILZ_DEFINE_GROUPS_FIRST);
            return $render->pnFormRedirect(pnModURL('mailz','admin','groups'));
        }

        // Get Newsletters
        $newsletters = pnModAPIFunc('mailz','common','getNewsletters',array('inactive' => 1));
 
        // Assign some standard values
        $yesno_items = array (
            array('text' => _MAILZ_YES, 'value' => 1),
            array('text' => _MAILZ_NO,  'value' => 0)
        );
        $inactive_items = array (
            array('text' => _MAILZ_INACTIVE, 'value' => 1),
            array('text' => _MAILZ_ACTIVE,   'value' => 0)
        );
        $contenttype_items = array (
            array('text' => _MAILZ_HTML,     'value' => 'h'),
            array('text' => _MAILZ_TEXT,     'value' => 't'),
            array('text' => _MAILZ_COMBINED, 'value' => 'c')
        );
        $groups_items = array();
        foreach ($groups as $group) {
            $groups_items[] = array ('text' => $group['title'], 'value' => $group['id']);
        }
 
        $render->assign('yesno_items',       $yesno_items);
        $render->assign('inactive_items',    $inactive_items);
        $render->assign('contenttype_items', $contenttype_items);
        $render->assign('groups_items',      $groups_items);
        $render->assign('newsletters',       $newsletters);

        // Should group be loaded?
        $id = (int) FormUtil::getPassedValue('id');
        if ($id > 0) {
            $nl = pnModAPIFunc('mailz','common','getNewsletters',array('id' => $id, 'inactive' => 1));
            if (isset($nl) && ($nl['id'] == $id)) {
                // Load newsletter
                $this->nl = $nl;
                $render->assign($this->nl);
                // Load group relations
                $groups = pnModAPIFunc('mailz','common','getNewsletterGroups',array('id' => $this->nl['id']));
                $render->assign('groups', $groups);
            } else {
                LogUtil::registerError(_MAILZ_LOAD_ERROR);
            }
        }
		return true;
    }
    function handleCommand(&$render, &$args)
    {
	    // Security check
	    if (!SecurityUtil::checkPermission('lobby::', '::', ACCESS_ADMIN)) {
		  	return LogUtil::registerPermissionError();
		}
		if ($args['commandName']=='back') {
		    return $render->pnFormRedirect(pnModURL('mailz','admin','newsletters'));
		} else if ($args['commandName']=='update') {
			// get the pnForm data and do a validation check
		    $obj = $render->pnFormGetValues();
		    if (!$render->pnFormIsValid()) return false;

		    // Store object
		    if (!isset($this->nl)) {
                $result = DBUtil::insertObject($obj,'mailz_newsletters');
                if (!$result) {
                    LogUtil::registerError(_MAILZ_NEWSLETTER_CREATION_ERROR);
                } else {
                    // Store the assigned target groups
        			LogUtil::registerStatus(_MAILZ_SETTINGS_STORED);
                    // Store group associations
                    $result = pnModAPIFunc('mailz','common','setNewsletterGroups',$obj);
                    if (!$result) {
                        LogUtil::registerError(_MAILZ_GROUPS_SET_ERROR);
                        return false;
                    }
                }
            } else {
                $obj['id'] = $this->nl['id'];
                $result = DBUtil::updateObject($obj,'mailz_newsletters');
                if ($result) {
                    // Update target groups
                    // Store group associations
                    $result = pnModAPIFunc('mailz','common','setNewsletterGroups',$obj);
                    if (!$result) {
                        LogUtil::registerError(_MAILZ_GROUPS_SET_ERROR);
                        return false;
                    }
                    // Display status message
                    LogUtil::registerStatus(_MAILZ_UPDATED);
                } else {
                    LogUtil::registerError(_MAILZ_UPDATE_ERROR);
                }
            }

			// Redirect
			return $render->pnFormRedirect(pnModURL('mailz','admin','newsletters'));
		}
    }
}
