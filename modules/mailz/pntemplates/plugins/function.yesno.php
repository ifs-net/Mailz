<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

function smarty_function_yesno($params, &$smarty) 
{
    // TODO: remove that, there is the same plugin in the core

    $dom = ZLanguage::getModuleDomain('mailz');

    $yes  = $params['yes'];
    $item = $params['item'];
    $isYes = (($params['reverse'] == 1) ? ($item != $yes) : ($item == $yes));
    if ($isYes) {
        return '<img src="images/icons/extrasmall/button_ok" alt="' . __('yes', $dom) . '" title="' . __('yes', $dom) . '" />';
    } else {
        return '<img src="images/icons/extrasmall/button_cancel" alt="' . __('no', $dom) . '" title="' . __('no', $dom) . '" />';
    }
}