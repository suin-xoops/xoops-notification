<?php

namespace Suin\Notification;

use XoopsMemberHandler as UserRepository;
use XoopsModuleHandler as ModuleRepository;
use XoopsMySQLDatabase as Database;
use Suin\Notification\Service\SendingService;
use Suin\Notification\Service\ReadingService;
use Suin\Notification\Repository\NotificationRepository;

class DependencyInjectionContainer
{
	/**
	 * Return sending service object
	 * @return SendingService
	 */
	public static function getSendingService()
	{
		$sendingService = new SendingService();
		$sendingService
			->setUserRepository(self::getUserRepository())
			->setModuleRepository(self::getModuleRepository())
			->setNotificationRepository(self::getNotificationRepository());

		return $sendingService;
	}

	/**
	 * Return reading service object
	 * @return ReadingService
	 */
	public static function getReadingService()
	{
		$readingService = new ReadingService();
		$readingService->setNotificationRepository(self::getNotificationRepository());
		return $readingService;
	}

	/**
	 * Return notification repository
	 * @return NotificationRepository
	 */
	public static function getNotificationRepository()
	{
		$notificationRepository = new NotificationRepository();
		$notificationRepository
			->setUserRepository(self::getUserRepository())
			->setModuleRepository(self::getModuleRepository())
			->setDatabase(self::getDatabase())
			->setPrefix(self::getDatabase()->prefix('notification').'_');
		return $notificationRepository;
	}

	/**
	 * Return database
	 * @return DataBase
	 */
	private static function getDatabase()
	{
		$root = \XCube_Root::getSingleton();
		return $root->mController->mDB;
	}

	/**
	 * Return user repository
	 * @return UserRepository
	 */
	private static function getUserRepository()
	{
		return xoops_gethandler('member');
	}

	/**
	 * Return module repository
	 * @return ModuleRepository
	 */
	private static function getModuleRepository()
	{
		return xoops_gethandler('module');
	}
}
