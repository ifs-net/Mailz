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