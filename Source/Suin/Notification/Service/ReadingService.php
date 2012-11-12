<?php

namespace Suin\Notification\Service;

use Exception;
use Suin\Notification\Repository\NotificationRepository;
use Suin\Notification\Entity\Notification;

/**
 * 既読サービス
 */
class ReadingService
{
	/**
	 * @var NotificationRepository
	 */
	private $notificationRepository;

	/**
	 * Set notification repository
	 * @param NotificationRepository $notificationRepository
	 * @return ReadingService
	 */
	public function setNotificationRepository(NotificationRepository $notificationRepository)
	{
		$this->notificationRepository = $notificationRepository;
		return $this;
	}

	/**
	 * Set read to the notification
	 * @param Notification $notification
	 * @throws Exception
	 * @return Notification
	 */
	public function read(Notification $notification)
	{
		try {
			$notification->setRead();

			// TODO >> implement UnitOfWork
			$this->notificationRepository->persist($notification);
		} catch ( Exception $e ) {
			throw $e;
		}

		return $notification;
	}
}
