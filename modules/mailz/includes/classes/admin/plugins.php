<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class mailz_admin_pluginsHandler
{
    var $plugin;
    var $nl;
    function initialize(&$render)
    {
        $dom = ZLanguage::getModuleDomain('mailz');
        // Get newsletter(s)
        $nl = (int) FormUtil::getPassedValue('nl');
        if ($nl > 0) {
            $this->nl = $nl;
            // Get single newsletter to work on with
            $newsletter = pnModAPIFunc('mailz', 'common', 'getNewsletters', array('id' => $nl, 'inactive' => 1));
            $render->assign('newsletter', $newsletter);

            // Add plugins for Newsletter
            $nl_plugins = pnModAPIFunc('mailz', 'common', 'getNewsletterPlugins', array('nid' => $this->nl));
            $render->assign('nl_plugins', $nl_plugins);

            // Should a plugin be added?
            $pluginid = (int)    FormUtil::getPassedValue('pluginid');
            $mod      = (string) FormUtil::getPassedValue('mod');
            if ($pluginid > 0) {
                // Add plugin
                $obj = array (
                    'nid'      => $this->nl,
                    'title'    => __('Please enter the desired text here', $dom),
                    'module'   => $mod,
                    'pluginid' => $pluginid
                );
                $result = DBUtil::insertObject($obj,'mailz_plugins');
                if ($result) {
                    // Register status message, update plugin sort order and redirect
                    LogUtil::registerStatus(__('Plugin has been added and can be edited now', $dom));
                    pnModAPIFunc('mailz','common','setNewsletterPluginOrder',array('nid' => $this->nl));
                    return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'plugins', array('nl' => $this->nl, 'id' => $obj['id'])));
                } else {
                    // Register error message and redirect
                    LogUtil::registerError(__('Error during saving the data', $dom));
                    return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'plugins', array('nl' => $this->nl)));
                }
            }
            
            // Should a plugin be loaded?
            $id = (int) FormUtil::getPassedValue('id');
            if ($id > 0) {
                $plugin = DBUtil::selectObjectByID('mailz_plugins', $id);
                if ($plugin && ($plugin['id'] == $id)) {
                    $this->plugin = $plugin;
                    $render->assign($this->plugin);
                    // Get additional data for plugin
                    $plugindata = pnModAPIFunc('mailz', 'common', 'getPlugins', array('module' => $this->plugin['module'], 'pluginid' => $this->plugin['pluginid']));
                    $render->assign('plugindata', $plugindata[0]);
                    // Maybe preview action is called?
                    $preview = (string) FormUtil::getPassedValue('preview');
                    if (isset($preview) && ( ($preview == 'h') || ($preview == 't') )) {
                        $render->assign('preview', $preview);
                    }
                }
            }

            // Should a plugin be switched (change order)
            $action = (string) FormUtil::getPassedValue('action');
            if (isset($action) && ($action == 'switch')) {
                $p1 = (int) FormUtil::getPassedValue('p1');
                $p2 = (int) FormUtil::getPassedValue('p2');
                $p1 = DBUtil::selectObjectByID('mailz_plugins',$p1);
                $p2 = DBUtil::selectObjectByID('mailz_plugins',$p2);
                if (!$p1 || !$p2 || ($p1['nid'] != $p2['nid']) || ($p1['nid'] != $this->nl)) {
                    LogUtil::registerError(__('An error occured during resorting', $dom));
                } else {
                    $dummy = $p1['position'];
                    $p1['position'] = $p2['position'];
                    $p2['position'] = $dummy;
                    $res = DBUtil::updateObject($p1, 'mailz_plugins');
                    if ($res) {
                        $res = DBUtil::updateObject($p2, 'mailz_plugins');
                        if (!$res) {
                            LogUtil::registerError(__('An error occured during resorting', $dom));
                        }
                    } else {
                        LogUtil::registerError(__('An error occured during resorting', $dom));
                    }
                }
                return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'plugins', array('nl' => $this->nl)));
            }

        } else {
            // Get Newsletters
            $newsletters = pnModAPIFunc('mailz', 'common', 'getNewsletters', array('inactive' => 1));
            $render->assign('newsletters', $newsletters);
        }

    	// add scribite support
    	if (pnModAvailable('scribite')) {
    		$scribite = pnModFunc('scribite', 'user', 'loader', array(
    									'modname' => 'mailz',
    		                            'editor'  => pnModGetVar('mailz', 'editor'),
    		                            'areas'   => array('header_html', 'footer_html')
                                        )
    								);
    	    PageUtil::AddVar('rawtext', $scribite);
        }



        // get all plugins
        $plugins = pnModAPIFunc('mailz','common','getPlugins');
        $render->assign('plugins', $plugins);


        // Assign some standard values
        $inactive_items = array(
            array('text' => __('inactive', $dom), 'value' => 1),
            array('text' => __('active', $dom),   'value' => 0)
        );
 
        $render->assign('inactive_items',    $inactive_items);

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
		    return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'plugins', array('nl' => $this->nl)));
		}
		else if ($args['commandName'] == 'update') {

			// get the pnForm data and do a validation check
		    $obj = $render->pnFormGetValues();
		    if (!$render->pnFormIsValid()) return false;

            $obj['id'] = $this->plugin['id'];

            // Should object be deleted?
            if ($obj['delete'] == 1) {
                $result = DBUtil::deleteObject($obj, 'mailz_plugins');
                if ($result) {
                    LogUtil::registerStatus(__('Data deleted', $dom));
                } else {
                    LogUtil::registerError(__('Error during deletion', $dom));
                }
                return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'plugins', array('nl' => $this->nl)));
            }

		    // Update Object
            $obj['id'] = $this->plugin['id'];
            $result = DBUtil::updateObject($obj,'mailz_plugins');
            if ($result) {
                    // Display status message
                    LogUtil::registerStatus(__('Data updated', $dom));
            } else {
                LogUtil::registerError(__('Error during updating the data', $dom));
            }

			// Redirect
			return $render->pnFormRedirect(pnModURL('mailz', 'admin', 'plugins', array('nl' => $this->nl)));
		}
    }
}
