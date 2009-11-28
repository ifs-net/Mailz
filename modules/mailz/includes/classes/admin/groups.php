<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class mailz_admin_groupsHandler
{
    var $group;
    function initialize(&$render)
    {
        $dom = ZLanguage::getModuleDomain('mailz');
        $groups = pnModAPIFunc('mailz', 'common', 'getGroups');
        $render->assign('groups', $groups);

        // Should group be loaded?
        $id = (int) FormUtil::getPassedValue('id');
        if ($id > 0) {
            $group = pnModAPIFunc('mailz', 'common', 'getGroups', array('id' => $id));
            if (isset($group) && ($group['id'] == $id)) {
                $this->group = $group;
                LogUtil::RegisterStatus(__('Data loaded', $dom));
                $render->assign($this->group);
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
        if ($args['commandName']=='update') {

            // get the pnForm data and do a validation check
            $obj = $render->pnFormGetValues();
            if (!$render->pnFormIsValid()) return false;

            // Should object be deleted?
            if ($obj['delete'] == 1) {
                $this->group['inactive'] = 1;
                $result = DBUtil::updateObject($this->group, 'mailz_groups');
                if ($result) {
                    LogUtil::registerStatus(__('Data deleted', $dom));
                } else {
                    LogUtil::registerError(__('Error during deletion', $dom));
                }
                // Redirect
                return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'groups'));
            }
            
            // Store object
            if (!isset($this->group)) {
                $result = DBUtil::insertObject($obj,'mailz_groups');
                if (!$result) {
                    LogUtil::registerError(__('Error during target group creation', $dom));
                } else {
                    LogUtil::registerStatus(__('Data saved', $dom));
                }
            } else {
                $obj['id'] = $this->group['id'];
                $result = DBUtil::updateObject($obj,'mailz_groups');
                if ($result) {
                    LogUtil::registerStatus(__('Data updated', $dom));
                } else {
                    LogUtil::registerError(__('Error during updating the data', $dom));
                }
            }

            // Redirect
            return $render->pnFormRedirect(pnModURL('mailz','admin','groups'));
        }
    }
}
