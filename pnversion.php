<?php
/**
 * @package      mailz
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

$modversion['name']           = 'mailz';
// the version string must not exceed 10 characters!
$modversion['version']        = '1.1';
$modversion['description']    = 'enhanced zk powered newsletter and mailing system';
$modversion['displayname']    = 'mailz';

// The following in formation is used by the credits module
// to display the correct credits
$modversion['changelog']      = 'docs/changelog.txt';
$modversion['credits']        = 'docs/credits.txt';
$modversion['help']           = 'docs/documentation.txt';
$modversion['license']        = 'docs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Florian Schiessl';
$modversion['contact']        = 'http://www.ifs-net.de/';

// The following information tells the Zikula core that this
// module has an admin option.
$modversion['admin']          = 1;

// This one adds the info to the DB, so that users can click on the 
// headings in the permission module
$modversion['securityschema'] = array('mailz::' => 'Action Name::Newsletter ID');
