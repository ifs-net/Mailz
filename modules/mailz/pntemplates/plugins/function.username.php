<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

function smarty_function_username($params, &$smarty) 
{
    $uid = $params['uid'];
    $uname = pnUserGetVar('uname',$uid);
    if (!isset($uname) && ($uname == '') && (!($uid > 1))) {
        return;
    } else {
        return $uname;
    }
}