<?php

namespace Suin\Notification\Tests\Unit\Service;

use Mockery as m;
use XoopsUser;
use XoopsModule;
use Suin\Notification\Service\ReadingService;
use Suin\Notification\Repository\NotificationRepository;
use Suin\Notification\Entity\Notification;

class ReadingServiceTest extends \PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		// :(
		if ( class_exists('XoopsUser') === false ) {
			eval('class XoopsUser{}');
		}

		if ( class_exists('XoopsModule') === false ) {
			eval('class XoopsModule{}');
		}
	}

	private function getNotificationRepository()
	{
		$notificationRepository = m::mock('Suin\Notification\Repository\NotificationRepository');

		return $notificationRepository;
	}

	private function getUser()
	{
		$user = m::mock('XoopsUser');

		return $user;
	}

	private function getModule()
	{
		$module = m::mock('XoopsModule');

		return $module;
	}

	public function testRead()
	{
		// dependency injection
		$notificationRepository = $this->getNotificationRepository();

		$service = new ReadingService();
		$service->setNotificationRepository($notificationRepository);

		// create notification
		$from = $this->getUser();
		$to   = $this->getUser();
		$module = $this->getModule();
		$notification = new Notification();
		$notification
			->setId(1234567890)
			->setFrom($from)
			->setTo($to)
			->setUnread()
			->setMessage('Message')
			->setLink('/link/to/page')
			->setModule($module);

		// expectation of behavior
		$notificationRepository->shouldReceive('persist')->with($notification)->once();

		// execute test target method
		$updatedNotification = $service->read($notification);

		// expects that same entity will be returned
		$this->assertSame($notification, $updatedNotification);

		// read flag will be updated
		$this->assertTrue($notification->hasBeenRead());

		// following fields will not be changed
		$this->assertSame(1234567890, $updatedNotification->getId());
		$this->assertSame($from, $updatedNotification->getFrom());
		$this->assertSame($to, $updatedNotification->getTo());
		$this->assertSame('Message', $updatedNotification->getMessage());
		$this->assertSame('/link/to/page', $updatedNotification->getLink());
		$this->assertSame($module, $updatedNotification->getModule());
	}

	/**
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage Database error
	 */
	public function testReadThrowsException()
	{
		// dependency injection
		$notificationRepository = $this->getNotificationRepository();

		$service = new ReadingService();
		$service->setNotificationRepository($notificationRepository);

		// create notification
		$notification = new Notification();

		// expectation of behavior
		$notificationRepository->shouldReceive('persist')->andThrow(new \RuntimeException('Database error'))->once();

		// execute test target method
		$service->read($notification);
	}
}
