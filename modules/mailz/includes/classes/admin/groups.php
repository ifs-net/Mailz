<?php

class mailz_admin_groupsHandler
{
    var $group;
    function initialize(&$render)
    {
        $groups = pnModAPIFunc('mailz','common','getGroups');
        $render->assign('groups', $groups);
        
        // Should group be loaded?
        $id = (int) FormUtil::getPassedValue('id');
        if ($id > 0) {
            $group = pnModAPIFunc('mailz','common','getGroups',array('id' => $id));
            if (isset($group) && ($group['id'] == $id)) {
                $this->group = $group;
                LogUtil::RegisterStatus(_MAILZ_LOADED);
                $render->assign($this->group);
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
		if ($args['commandName']=='update') {

			// get the pnForm data and do a validation check
		    $obj = $render->pnFormGetValues();
		    if (!$render->pnFormIsValid()) return false;

            // Should object be deleted?
            if ($obj['delete'] == 1) {
                $this->group['inactive'] = 1;
                $result = DBUtil::updateObject($this->group, 'mailz_groups');
                if ($result) {
                    LogUtil::registerStatus(_MAILZ_DELETED);
                } else {
                    LogUtil::registerError(_MAILZ_DELETE_ERROR);
                }
                // Redirect
                return $render->pnFormRedirect(pnModURL('mailz','admin','groups'));
            }
		    
		    // Store object
		    if (!isset($this->group)) {
                $result = DBUtil::insertObject($obj,'mailz_groups');
                if (!$result) {
                    LogUtil::registerError(_MAILZ_GROUP_CREATION_ERROR);
                } else {
        			LogUtil::registerStatus(_MAILZ_SETTINGS_STORED);
                }
            } else {
                $obj['id'] = $this->group['id'];
                $result = DBUtil::updateObject($obj,'mailz_groups');
                if ($result) {
                    LogUtil::registerStatus(_MAILZ_UPDATED);
                } else {
                    LogUtil::registerError(_MAILZ_UPDATE_ERROR);
                }
            }

			// Redirect
			return $render->pnFormRedirect(pnModURL('mailz','admin','groups'));
		}
    }
}
