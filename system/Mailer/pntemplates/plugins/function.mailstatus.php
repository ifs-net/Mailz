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
 * get mail status
 *
 * @author Florian Schießl
 * @params  int args['status']        mail status
 * @return array array of admin links
 */
function smarty_function_mailstatus($params, &$smarty)
{
    // Get parameters
	$status = $params['status'];
	if (!isset($status) || (!($status >= 0))) {
        return "status error";
    }

    // Cast to int
    $status = (int) $status;

    // Return status code
    switch($status) {
        case 0:
            return _MAILER_STATUS_WAITING;
        case 1:
            return _MAILER_STATUS_RETRY;
        case 2:
            return _MAILER_STATUS_RETRYLAST;
        default:
            return 'unknown';
    }
}