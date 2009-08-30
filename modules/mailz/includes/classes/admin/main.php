<?php

class mailz_admin_mainHandler
{
    function initialize(&$render)
    {	    
      	// Assign module variables
		$modvars = pnModGetVar('mailz');
		$render->assign($modvars);
		// get all fieldtypes
		if (pnModAvailable('MyProfile')) {
            $fields = pnModAPIFunc('MyProfile','admin','getFields');
            $fields_items = array();
            foreach ($fields as $field) {
                $fields_items[] = array(
                        'text'  =>  $field['identifier']." (".$field['description'].")",
                        'value' =>  $field['identifier']
                    );
            }
            $render->assign('fields_items', $fields_items);
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

            pnModSetVar('mailz','myprofile_name',$obj['myprofile_name']);

		    // Register status message
			LogUtil::registerStatus(_MAILZ_UPDATED);

			// redirect
			return $render->pnFormRedirect(pnModURL('mailz','admin','main'));
		}
    }
}
