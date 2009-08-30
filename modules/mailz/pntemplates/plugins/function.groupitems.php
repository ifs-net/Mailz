<?php

function smarty_function_groupitems($params, &$smarty) 
{
    $group = pnModAPIFunc('mailz','common','getGroups',$params);
    if (!$group) {
        return 'wrong parameters';
    }
    
    // Count recipients
    $recipients = 0;

    // get sql query
    $sql = $group['query'];
    
    if (isset($sql) && ($sql != '')) {
        $sql = pnModAPIFunc('mailz','common','sqlReplacements',array('query' => $sql));
        $result = DBUtil::executeSql($sql);
        if ($result) {
            $recipients+=$result->recordCount();
        }
    }
    
    // api call?
    if ($group['api'] != '') {
        $dummy = explode(':',$group['api']);
        $mod = $dummy[0];
        $type = $dummy[1];
        $func = $dummy[2];
        $args = $dummy[3];
        $paramsdumy = explode(';',$args);
        $argsArray = array();
//        print "mod $mod type $type func $func";
        foreach ($paramsdummy as $item) {
            $d = explode(',',$item);
            $key = $d[0];
            $value = $d[1];
            $argsArray[$key]=$value;
        }
        $args = $argsArray;
        $result = pnModAPIFunc($mod,$type,$func,$args);
        $recipients = count($result) + $recipients;
    }

    return $recipients;
}