<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

function smarty_function_subscribedinfo($args, &$smarty) 
{
    if (!pnUserLoggedIn()) {
        return "";
    }
    $result = pnModAPIFunc('mailz','common','isSubscribed',$args);
    if ($result) {
        return ", "._MAILZ_IS_SUBSCRIBED;
    } else {
        return ", "._MAILZ_IS_NOT_SUBSCRIBED;
    }
}