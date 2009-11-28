<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

function smarty_function_nlcontenttype($params, &$smarty) 
{
    $dom = ZLanguage::getModuleDomain('mailz');

    switch($params['newsletter']['contenttype']) {
        case 'h':
            return __('HTML (formated text)', $dom);
        case 't':
            return __('TEXT (plain text)', $dom);
        case 'c':
            return __('combined: text+html', $dom);
        default:
            return 'unknown nl content type';
    }
}