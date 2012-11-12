<?php

namespace Suin\Notification\Tests\Unit\Service;

use Suin\Notification\Service\SendingService;
use Suin\Notification\Repository\NotificationRepository;
use Suin\Notification\Entity\Notification;
use Mockery as m;

class SendingServiceTest extends \PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		// :(
		if ( class_exists('XoopsMemberHandler') === false ) {
			eval('class XoopsMemberHandler {}');
		}

		if ( class_exists('XoopsModuleHandler') === false ) {
			eval('class XoopsModuleHandler {}');
		}
	}

	private function getUserRepository()
	{
		$userRepository = m::mock('XoopsMemberHandler');

		return $userRepository;
	}

	private function getModuleRepository()
	{
		$moduleRepository = m::mock('XoopsModuleHandler');

		return $moduleRepository;
	}

	private function getNotificationRepository()
	{
		$notificationRepository = m::mock('Suin\Notification\Repository\NotificationRepository');

		return $notificationRepository;
	}

	public function testSend()
	{
		// dependency injection
		$userRepository = $this->getUserRepository();
		$moduleRepository = $this->getModuleRepository();
		$notificationRepository = $this->getNotificationRepository();

		$service = new SendingService();
		$service
			->setUserRepository($userRepository)
			->setModuleRepository($moduleRepository)
			->setNotificationRepository($notificationRepository);

		// parameters
		$fromUserId = 1234;
		$toUserId   = 9876;
		$message    = 'Message';
		$link       = 'link/to/page';
		$moduleId   = 11;

		// expected behavior
		$fromUser = m::mock('XoopsUser');
		$toUser   = m::mock('XoopsUser');
		$module   = m::mock('XoopsModule');

		$userRepository->shouldReceive('getUser')->with(1234)->andReturn($fromUser);
		$userRepository->shouldReceive('getUser')->with(9876)->andReturn($toUser);
		$moduleRepository->shouldReceive('get')->with(11)->andReturn($module);

		$fromUser->shouldReceive('get')->with('uid')->andReturn(1234);
		$toUser->shouldReceive('get')->with('uid')->andReturn(9876);
		$module->shouldReceive('get')->with('mid')->andReturn(11);

		$notificationRepository->shouldReceive('persist')->with('Suin\Notification\Entity\Notification')->once();

		// execute test target
		$notification = $service->send($fromUserId, $toUserId, $message, $link, $moduleId);

		// verification
		$this->assertTrue($notification instanceof Notification);
		$this->assertTrue($notification->getFrom() instanceof \XoopsUser);
		$this->assertSame(1234, $notification->getFrom()->get('uid'));
		$this->assertTrue($notification->getTo() instanceof \XoopsUser);
		$this->assertSame(9876, $notification->getTo()->get('uid'));
		$this->assertSame('Message', $notification->getMessage());
		$this->assertSame('link/to/page', $notification->getLink());
		$this->assertTrue($notification->getModule() instanceof \XoopsModule);
		$this->assertSame(11, $notification->getModule()->get('mid'));
	}
}
