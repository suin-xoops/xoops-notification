<?php

use Suin\Notification\DependencyInjectionContainer as Container;
use Suin\Notification\Entity\Notification;

require __DIR__.'/../../mainfile.php';

$notFound = function() {
	XCube_DelegateUtils::call('HTTP.NotFound');
	header('HTTP', true, 404);
	echo '404 Not Found';
	exit;
};

$id = XCube_Root::getSingleton()->mContext->mRequest->getRequest('id');

if ( ! preg_match('/^[0-9]+$/', $id) ) {
	$notFound();
}

$notification = Container::getNotificationRepository()->find($id);

if ( ( $notification instanceof Notification ) === false ) {
	$notFound();
}

/** @var $xoopsUser XoopsUser */
if ( $notification->isSentTo($xoopsUser) === false ) {
	$notFound();
}

$readingService = Container::getReadingService();
$readingService->read($notification);

XCube_Root::getSingleton()->mController->executeForward(XOOPS_URL.$notification->getLink());
