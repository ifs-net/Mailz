<?php

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