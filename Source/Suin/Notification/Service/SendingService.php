<?php

namespace Suin\Notification\Service;

use Exception;
use XoopsUser as User;
use XoopsModule as Module;
use XoopsMemberHandler as UserRepository;
use XoopsModuleHandler as ModuleRepository;
use Suin\Notification\Repository\NotificationRepository;
use Suin\Notification\Entity\Notification;

class SendingService
{
	/**
	 * @var NotificationRepository
	 */
	private $notificationRepository;

	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * @var ModuleRepository
	 */
	private $moduleRepository;

	/**
	 * Set notification repository
	 * @param NotificationRepository $notificationRepository
	 * @return SendingService
	 */
	public function setNotificationRepository(NotificationRepository $notificationRepository)
	{
		$this->notificationRepository = $notificationRepository;
		return $this;
	}

	/**
	 * Set user repository
	 * @param UserRepository $userRepository
	 * @return SendingService
	 */
	public function setUserRepository(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
		return $this;
	}

	/**
	 * Set module repository
	 * @param ModuleRepository $moduleRepository
	 * @return SendingService
	 */
	public function setModuleRepository(ModuleRepository $moduleRepository)
	{
		$this->moduleRepository = $moduleRepository;
		return $this;
	}

	/**
	 * Send a notification
	 * @param int $fromUserId
	 * @param int $toUserId
	 * @param string $message
	 * @param string $link
	 * @param int $moduleId
	 * @throws Exception
	 * @return Notification
	 */
	public function send($fromUserId, $toUserId, $message, $link, $moduleId)
	{
		try {
			/** @var $fromUser User */
			$fromUser = $this->userRepository->getUser($fromUserId);
			/** @var $toUser User */
			$toUser = $this->userRepository->getUser($toUserId);
			/** @var $module Module */
			$module = $this->moduleRepository->get($moduleId);

			// TODO >> verify user object and module object

			$notification = new Notification();
			$notification
				->setFrom($fromUser)
				->setTo($toUser)
				->setMessage($message)
				->setLink($link)
				->setModule($module);

			// TODO >> implement UnitOfWork
			$this->notificationRepository->persist($notification);

		} catch ( Exception $e ) {
			throw $e;
		}

		return $notification;
	}
}
