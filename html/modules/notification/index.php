<?php

use Suin\Notification\DependencyInjectionContainer;

require __DIR__.'/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/core/XCube_PageNavigator.class.php';

/** @var $xoopsUser XoopsUser */

// guard
if ( ( $xoopsUser instanceof XoopsUser ) === false ) {
	redirect_header(XOOPS_URL, 3, _NOPERM);
}

// presentation logic
$notificationRepository = DependencyInjectionContainer::getNotificationRepository();
$total = $notificationRepository->countByUser($xoopsUser->get('uid'));

$pager = new XCube_PageNavigator('./index.php');
$pager->setPerpage(25);
$pager->setTotalItems($total);
$pager->fetch();

$notifications = $notificationRepository->findByUser($xoopsUser->get('uid'), $pager->getPerpage(), $pager->getStart());

$xoopsTpl->assign(array(
	'notifications' => $notifications,
	'pager'         => $pager,
));

require_once XOOPS_ROOT_PATH . '/header.php';
$xoopsOption['template_main'] = 'notification_index.html';
require_once XOOPS_ROOT_PATH . '/footer.php';
