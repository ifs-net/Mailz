<?php

function smarty_function_subscriptions($args, &$smarty) 
{
    $id = (int) $args['id'];
    $where = 'nid = '.$id;
    $res = DBUtil::selectObjectCount('mailz_subscriptions',$where);
    return $res;
}