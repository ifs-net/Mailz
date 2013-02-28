<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

function smarty_function_subscriptions($args, &$smarty) 
{
    $id = (int) $args['id'];
    $where = 'nid = '.$id;
    $res = DBUtil::selectObjectCount('mailz_subscriptions',$where);
    return $res;
}