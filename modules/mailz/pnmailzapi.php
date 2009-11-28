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
 * get plugins with type / title
 *
 * @param   $args['id']     int     optional, show specific one or all otherwise
 * @return  array
 */
function mailz_mailzapi_getPlugins($args)
{
    $dom = ZLanguage::getModuleDomain('mailz');

    $plugins = array();
    // Add first plugin.. You can add more using more arrays
    $plugins[] = array(
        'pluginid'      => 1,   // internal id for this module
        'title'         => __('HTML-Text-Plugin', $dom),
        'description'   => __('Defineable text that should be added to the newsletter. This plugin does not need any parameters and does not really display any content as plugin. The header (or footer) you define is the content that is later included in the newsletter.', $dom),
        'module'        => 'mailz'
    );
    $plugins[] = array(
        'pluginid'      => 2,   // internal id for this module
        'title'         => __('Table of content', $dom),
        'description'   => __('This plugin will automatically generate a table of content for your newsletter that lists and links all used plugins.', $dom),
        'module'        => 'mailz'
    );
    return $plugins;
}

/*
 * get content for plugins
 *
 * @param   $args['pluginid']       int         id number of plugin (internal id for this module, see getPlugins method)
 * @param   $args['params']         string      optional, show specific one or all otherwise
 * @param   $args['uid']            int         optional, user id for user specific content
 * @param   $args['contenttype']    string      h or t for html or text
 * @param   $args['last']           datetime    timtestamp of last newsletter
 * @return  array
 */
function mailz_mailzapi_getContent($args)
{
    switch($args['pluginid']) {
        case 1:
            // return emtpy string because we do not need anything to display in here...    
            return '';
        case 2:
            // Get Plugins
            $plugins = pnModAPIFunc('mailz', 'common', 'getNewsletterPlugins', array('nid' => $args['nid']));
            if ($args['contenttype'] == 't') {
                $counter = 0;
                $output.="\n";
                foreach ($plugins as $plugin) {
                    $counter++;
                    $output.=$counter.'. '.$plugin['title']."\n";
                }
            } else {
                $render = pnRender::getInstance('mailz');
                $render->assign('plugins', $plugins);
                $output = $render->fetch('mailz_mailz_tableofcontents.htm');
            }
            return $output;
    }
    return '';
}

