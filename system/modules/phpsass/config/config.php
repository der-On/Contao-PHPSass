<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

$GLOBALS['BE_MOD']['design']['phpsass'] = array
(
    'tables' => array('tl_phpsass'),
    'icon'  => 'system/modules/phpsass/html/icon.gif',
);

$GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array('PHPSass', 'parseFrontendTemplate');