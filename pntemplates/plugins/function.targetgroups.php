<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

function smarty_function_targetgroups($params, &$smarty) 
{
    $groups = $params['groups'];
    if (!$groups) {
        return 'N/A';
    }
    $output = array();
    foreach ($groups as $group) {
        $res = pnModAPIFunc('mailz','common','getGroups',array('id' => $group));
        $title = $res['title'];
        $output[] = $title;
    }

    return implode(', ',$output);
}