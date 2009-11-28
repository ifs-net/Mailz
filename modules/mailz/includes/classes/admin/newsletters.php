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
        $dom = ZLanguage::getModuleDomain('mailz');
        // Get groups for target group selection
        $groups = pnModAPIFunc('mailz', 'common', 'getGroups');

        // Get Newsletters
        $newsletters = pnModAPIFunc('mailz', 'common', 'getNewsletters', array('inactive' => 1));

        // Assign some standard values
        $yesno_items = array (
            array('text' => __('yes', $dom), 'value' => 1),
            array('text' => __('no', $dom),  'value' => 0)
        );
        $inactive_items = array (
            array('text' => __('inactive', $dom), 'value' => 1),
            array('text' => __('active', $dom),   'value' => 0)
        );
        $contenttype_items = array (
            array('text' => __('html', $dom),                   'value' => 'h'),
            array('text' => __('text only', $dom),              'value' => 't'),
            array('text' => __('combined: text+html', $dom),    'value' => 'c')
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
            $nl = pnModAPIFunc('mailz', 'common', 'getNewsletters', array('id' => $id, 'inactive' => 1));
            if (isset($nl) && ($nl['id'] == $id)) {
                // Load newsletter
                $this->nl = $nl;
                $render->assign($this->nl);
                // Load group relations
                $groups = pnModAPIFunc('mailz', 'common', 'getNewsletterGroups', array('id' => $this->nl['id']));
                $render->assign('groups', $groups);
            } else {
                LogUtil::registerError(__('Error during loading the data', $dom));
            }
        }
		return true;
    }

    function handleCommand(&$render, &$args)
    {
        $dom = ZLanguage::getModuleDomain('mailz');
	    // Security check
	    if (!SecurityUtil::checkPermission('mailz::', '::', ACCESS_ADMIN)) {
		  	return LogUtil::registerPermissionError();
		}
		if ($args['commandName'] == 'back') {
		    return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'newsletters'));
		} else if ($args['commandName'] == 'update') {
			// get the pnForm data and do a validation check
		    $obj = $render->pnFormGetValues();
		    if (!$render->pnFormIsValid()) return false;

		    // Store object
		    if (!isset($this->nl)) {
                $result = DBUtil::insertObject($obj,'mailz_newsletters');
                if (!$result) {
                    LogUtil::registerError(__('Error during saving the newsletter', $dom));
                } else {
                    // Store the assigned target groups
        			LogUtil::registerStatus(__('Data saved', $dom));
                    // Store group associations
                    $result = pnModAPIFunc('mailz','common','setNewsletterGroups', $obj);
                    if (!$result) {
                        LogUtil::registerError(__('Error during saving the group assignments', $dom));
                        return false;
                    }
                }
            } else {
                $obj['id'] = $this->nl['id'];
                $result = DBUtil::updateObject($obj, 'mailz_newsletters');
                if ($result) {
                    // Update target groups
                    // Store group associations
                    $result = pnModAPIFunc('mailz','common','setNewsletterGroups',$obj);
                    if (!$result) {
                        LogUtil::registerError(__('Error during saving the group assignments', $dom));
                        return false;
                    }
                    // Display status message
                    LogUtil::registerStatus(__('Data updated', $dom));
                } else {
                    LogUtil::registerError(__('Error during updating the data', $dom));
                }
            }

			// Redirect
			return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'newsletters'));
		}
    }
}
