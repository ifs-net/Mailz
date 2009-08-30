<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @uses PHPMailer http://phpmailer.sourceforge.net
 * @package Zikula_System_Modules
 * @subpackage Mailer
 */

/**
 * get available admin panel links
 *
 * @author Florian Schießl
 * @params  object  args['mail']        mail as object / array
 * @params  string  args['item']        item to extract
 * @return array array of admin links
 */
function smarty_function_templateusage($params, &$smarty)
{
	$mail = unserialize($params['mail']);
	if ((!$mail) | !isset($mail) || (!(count($mail) > 0))) {
        return 'param mail missing';
    }
	$notemplates = (int) $mail['notemplates'];
    if ($notemplates == 1) {
        return _NO;
    } else {
        return _YES;
    }

	return $mail[$item];
}