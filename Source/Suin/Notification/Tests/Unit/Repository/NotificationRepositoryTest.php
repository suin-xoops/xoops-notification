<?php

namespace Suin\Notification\Tests\Unit\Repository;

use Mockery as m;
use Suin\Notification\Repository\NotificationRepository;
use Suin\Notification\Entity\Notification;

class NotificationRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		// :(
		if ( class_exists('XoopsDatabase') === false ) {
			eval('class XoopsDatabase {}');
		}

		if ( class_exists('XoopsMemberHandler') === false ) {
			eval('class XoopsMemberHandler {}');
		}

		if ( class_exists('XoopsUser') === false ) {
			eval('class XoopsUser {}');
		}

		if ( class_exists('XoopsModuleHandler') === false ) {
			eval('class XoopsModuleHandler {}');
		}

		if ( class_exists('XoopsModule') === false ) {
			eval('class XoopsModule {}');
		}
	}

	private function getDatabase()
	{
		return m::mock('XoopsDatabase');
	}

	private function getUserRepository()
	{
		return m::mock('XoopsMemberHandler');
	}

	private function getUser()
	{
		return m::mock('XoopsUser');
	}

	private function getModuleRepository()
	{
		return m::mock('XoopsModuleHandler');
	}

	private function getModule()
	{
		return m::mock('XoopsModule');
	}

	public function testSetDatabase()
	{
		$notificationRepository = new NotificationRepository();
		$this->assertAttributeSame(null, 'database', $notificationRepository);
		$database = $this->getMock('XoopsDatabase');
		$notificationRepository->setDatabase($database);
		$this->assertAttributeSame($database, 'database', $notificationRepository);
	}

	public function testPersistNewNotification()
	{
		$from = m::mock('XoopsUser');
		$from->shouldReceive('get')->with('uid')->andReturn(1234);
		$to = m::mock('XoopsUser');
		$to->shouldReceive('get')->with('uid')->andReturn(9876);
		$module = m::mock('XoopsModule');
		$module->shouldReceive('get')->with('mid')->andReturn(11);
		$created = m::mock('DateTime', array('getTimestamp' => 13579));

		$notification = m::mock('Suin\Notification\Entity\Notification', array(
			'getId'       => null,
			'getFrom'     => $from,
			'getTo'       => $to,
			'getMessage'  => 'message',
			'getLink'     => '/link/to/page',
			'hasBeenRead' => true,
			'getModule'   => $module,
			'getCreated'  => $created,
		));

		$expectedQuery  = 'INSERT INTO xoops_notification_notification ';
		$expectedQuery .= '(id, from_user_id, to_user_id, message, link, `read`, module_id, created) ';
		$expectedQuery .= 'VALUES (NULL, 1234, 9876, "message", "/link/to/page", 1, 11, 13579)';

		$database = $this->getMock('XoopsDatabase', array('queryF'));
		$database
			->expects($this->once())
			->method('queryF')
			->with($expectedQuery);

		$notificationRepository = new NotificationRepository();
		$notificationRepository
			->setDatabase($database)
			->setPrefix('xoops_notification_')
			->persist($notification);
	}

	public function testPersistUpdatingNotification()
	{
		$from = m::mock('XoopsUser');
		$from->shouldReceive('get')->with('uid')->andReturn(1234);
		$to = m::mock('XoopsUser');
		$to->shouldReceive('get')->with('uid')->andReturn(9876);
		$module = m::mock('XoopsModule');
		$module->shouldReceive('get')->with('mid')->andReturn(11);
		$created = m::mock('DateTime', array('getTimestamp' => 13579));

		$notification = m::mock('Suin\Notification\Entity\Notification', array(
			'getId'       => 123,
			'getFrom'     => $from,
			'getTo'       => $to,
			'getMessage'  => 'message',
			'getLink'     => '/link/to/page',
			'hasBeenRead' => false,
			'getModule'   => $module,
			'getCreated'  => $created,
		));

		$expectedQuery  = 'UPDATE xoops_notification_notification ';
		$expectedQuery .= 'SET from_user_id = 1234, to_user_id = 9876, message = "message", ';
		$expectedQuery .= 'link = "/link/to/page", `read` = 0, module_id = 11, created = 13579 ';
		$expectedQuery .= 'WHERE id = 123';

		$database = $this->getMock('XoopsDatabase', array('queryF'));
		$database
			->expects($this->once())
			->method('queryF')
			->with($expectedQuery);

		$notificationRepository = new NotificationRepository();
		$notificationRepository
			->setDatabase($database)
			->setPrefix('xoops_notification_')
			->persist($notification);
	}

	public function testFindUnreadByUser()
	{
		// dependency injection
		$database = $this->getMock('XoopsDatabase', array('query', 'fetchArray'));
		$userRepository = $this->getUserRepository();
		$moduleRepository = $this->getModuleRepository();

		$repository = new NotificationRepository();
		$repository->setDatabase($database);
		$repository->setPrefix('xoops_notification_');
		$repository->setUserRepository($userRepository);
		$repository->setModuleRepository($moduleRepository);

		// data set
		$rows = array(
			array(
				'id'           => 100,
				'from_user_id' => 9876,
				'to_user_id'   => 1234,
				'message'      => 'Message 1',
				'link'         => '/link/to/page/1',
				'read'         => 0,
				'module_id'    => 10,
				'created'      => 1234567890,
			),
			array(
				'id'           => 101,
				'from_user_id' => 9876,
				'to_user_id'   => 1234,
				'message'      => 'Message 2',
				'link'         => '/link/to/page/2',
				'read'         => 1,
				'module_id'    => 10,
				'created'      => 1234567890,
			),
			false,
		);

		// behavior expectation
		$pointer = ':pointer';
		$query = 'SELECT * FROM xoops_notification_notification WHERE to_user_id = 1234 AND `read` = 0 ORDER BY created DESC';
		$database->expects($this->at(0))->method('query')->with($query)->will($this->returnValue($pointer));
		$database->expects($this->at(1))->method('fetchArray')->with($pointer)->will($this->returnValue($rows[0]));
		$database->expects($this->at(2))->method('fetchArray')->with($pointer)->will($this->returnValue($rows[1]));

		$from = $this->getUser();
		$to   = $this->getUser();
		$userRepository->shouldReceive('getUser')->with(9876)->andReturn($from);
		$userRepository->shouldReceive('getUser')->with(1234)->andReturn($to);

		$module = $this->getModule();
		$moduleRepository->shouldReceive('get')->with(10)->andReturn($module);

		// execute test target method
		$notifications = $repository->findUnreadByUser(1234);

		// verify result
		$this->assertInternalType('array', $notifications);
		$this->assertCount(count($rows) - 1, $notifications);

		/** @var $firstNotification Notification */
		$firstNotification = reset($notifications);
		$this->assertTrue($firstNotification instanceof Notification);
		$this->assertSame(100, $firstNotification->getId());
		$this->assertSame($from, $firstNotification->getFrom());
		$this->assertSame($to, $firstNotification->getTo());
		$this->assertSame('Message 1', $firstNotification->getMessage());
		$this->assertSame('/link/to/page/1', $firstNotification->getLink());
		$this->assertFalse($firstNotification->hasBeenRead());
		$this->assertSame($module, $firstNotification->getModule());
		$this->assertTrue($firstNotification->getCreated() instanceof \DateTime);
		$this->assertSame(1234567890, $firstNotification->getCreated()->getTimestamp());

		/** @var $secondNotification Notification */
		$secondNotification = next($notifications);
		$this->assertTrue($firstNotification instanceof Notification);
		$this->assertSame(101, $secondNotification->getId());
		$this->assertTrue($secondNotification->hasBeenRead());
	}

	public function testFindByUser()
	{
		// dependency injection
		$database = $this->getMock('XoopsDatabase', array('query', 'fetchArray'));
		$userRepository = $this->getUserRepository();
		$moduleRepository = $this->getModuleRepository();

		$repository = new NotificationRepository();
		$repository->setDatabase($database);
		$repository->setPrefix('xoops_notification_');
		$repository->setUserRepository($userRepository);
		$repository->setModuleRepository($moduleRepository);

		// behavior expectation
		$query = 'SELECT * FROM xoops_notification_notification WHERE to_user_id = 123 ORDER BY created DESC';
		$database->expects($this->at(0))->method('query')->with($query);

		$notifications = $repository->findByUser(123);
		$this->assertInternalType('array', $notifications);
	}

	public function testCountByUser()
	{
		// dependency injection
		$database = $this->getMock('XoopsDatabase', array('query', 'fetchArray'));
		$userRepository = $this->getUserRepository();
		$moduleRepository = $this->getModuleRepository();

		$repository = new NotificationRepository();
		$repository->setDatabase($database);
		$repository->setPrefix('xoops_notification_');
		$repository->setUserRepository($userRepository);
		$repository->setModuleRepository($moduleRepository);

		// behavior expectation
		$query = 'SELECT COUNT(*) AS total FROM xoops_notification_notification WHERE to_user_id = 123 ORDER BY created DESC';
		$database->expects($this->at(0))->method('query')->with($query);
		$database->expects($this->at(1))->method('fetchArray')->will($this->returnValue(array(
			'total' => 24,
		)));

		$total = $repository->countByUser(123);
		$this->assertSame(24, $total);
	}
}
