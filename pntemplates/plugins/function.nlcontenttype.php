<?php

function smarty_function_nlcontenttype($params, &$smarty) 
{
    switch($params['newsletter']['contenttype']) {
        case 'h':
            return _MAILZ_HTML;
        case 't':
            return _MAILZ_TEXT;
        case 'c':
            return _MAILZ_COMBINED;
        default:
            return 'unknown nl content type';
    }
}