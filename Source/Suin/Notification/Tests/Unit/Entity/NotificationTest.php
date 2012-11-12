<?php

namespace Suin\Notification\Tests\Unit\Entity;

use Suin\Notification\Entity\Notification;
use Mockery as m;

class NotificationTest extends \PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		// :(
		if ( class_exists('XoopsUser') === false ) {
			eval('class XoopsUser {}');
		}

		if ( class_exists('XoopsModule') === false ) {
			eval('class XoopsModule{}');
		}
	}

	public function testSetId()
	{
		$notification = new Notification;
		$this->assertSame(null, $notification->getId());
		$notification->setId(1);
		$this->assertSame(1, $notification->getId());
	}

	public function testSetFrom()
	{
		$notification = new Notification;
		$this->assertSame(null, $notification->getFrom());
		$user = $this->getMock('XoopsUser');
		$notification->setFrom($user);
		$this->assertSame($user, $notification->getFrom());
	}

	public function testSetTo()
	{
		$notification = new Notification;
		$this->assertSame(null, $notification->getTo());
		$user = $this->getMock('XoopsUser');
		$notification->setTo($user);
		$this->assertSame($user, $notification->getTo());
	}

	public function testSetMessage()
	{
		$notification = new Notification;
		$this->assertSame(null, $notification->getMessage());
		$notification->setMessage('message');
		$this->assertSame('message', $notification->getMessage());
	}

	public function testSetLink()
	{
		$notification = new Notification;
		$this->assertSame(null, $notification->getLink());
		$notification->setLink('/modules/bulletin/article.php?id=1');
		$this->assertSame('/modules/bulletin/article.php?id=1', $notification->getLink());
	}

	public function testRead()
	{
		$notification = new Notification;
		$this->assertAttributeSame(false, 'read', $notification);

		$notification->setRead();
		$this->assertTrue($notification->hasBeenRead());

		$notification->setUnread();
		$this->assertFalse($notification->hasBeenRead());
	}

	public function testModule()
	{
		$notification = new Notification;
		$this->assertSame(null, $notification->getModule());
		$module = $this->getMock('XoopsModule');
		$notification->setModule($module);
		$this->assertSame($module, $notification->getModule());
	}

	public function testCreatedIsSpecifiedOnConstruction()
	{
		$notification = new Notification;
		$this->assertEquals(new \DateTime('now'), $notification->getCreated());
	}

	public function testCreated()
	{
		$notification = new Notification;
		$datetime = new \DateTime();
		$notification->setCreated($datetime);
		$this->assertSame($datetime, $notification->getCreated());
	}
}
