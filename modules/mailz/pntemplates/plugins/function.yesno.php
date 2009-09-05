<?php

function smarty_function_yesno($params, &$smarty) 
{
    $yes  = $params['yes'];
    $item = $params['item'];
    if ($params['reverse'] == 1) {
        if ($item != $yes) {
            return '<img src="images/icons/extrasmall/button_ok" alt="'._MAILZ_YES.'" title="'._MAILZ_YES.'" />';
        } else {
            return '<img src="images/icons/extrasmall/button_cancel" alt="'._MAILZ_NO.'" title="'._MAILZ_NO.'" />';
        }
    } else {
        if ($item == $yes) {
            return '<img src="images/icons/extrasmall/button_ok" alt="'._MAILZ_YES.'" title="'._MAILZ_YES.'" />';
        } else {
            return '<img src="images/icons/extrasmall/button_cancel" alt="'._MAILZ_NO.'" title="'._MAILZ_NO.'" />';
        }
    }
}