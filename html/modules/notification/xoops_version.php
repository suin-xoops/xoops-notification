<?php

defined( 'XOOPS_ROOT_PATH' ) or die;

$mydirname = basename(dirname(__FILE__));

$modversion['name'] = _MI_NOTIFICATION_NAME;
$modversion['version'] = 1.00;
$modversion['description'] = _MI_NOTIFICATION_DESC;
$modversion['credits'] = 'suin';
$modversion['author'] = 'suin https://github.com/suin/xoops-notification';
$modversion['license'] = 'GPL see LICENSE';
$modversion['image'] = 'notification_slogo.png';
$modversion['dirname'] = $mydirname;

$modversion['cube_style'] = true;

// Admin things
$modversion['hasAdmin'] = 0;
$modversion['adminindex'] = 'admin/index.php' ;
$modversion['adminmenu'] = 'admin/menu.php' ;

// Menu
$modversion['hasMain'] = 1;

// Templates
$modversion['templates'][] = array(
	'file' => 'notification_index.html',
);
