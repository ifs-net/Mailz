<?php

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