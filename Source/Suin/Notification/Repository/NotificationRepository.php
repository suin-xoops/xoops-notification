<?php

namespace Suin\Notification\Repository;

use DateTime;
use XoopsMemberHandler as UserRepository;
use XoopsModuleHandler as ModuleRepository;
use Suin\Notification\Entity\Notification;
use Suin\Notification\Repository\Query;

class NotificationRepository extends RepositoryBase
{
	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * @var ModuleRepository
	 */
	private $moduleRepository;

	/**
	 * Set user repository
	 * @param UserRepository $userRepository
	 * @return NotificationRepository
	 */
	public function setUserRepository(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
		return $this;
	}

	/**
	 * Set module repository
	 * @param ModuleRepository $moduleRepository
	 * @return NotificationRepository
	 */
	public function setModuleRepository(ModuleRepository $moduleRepository)
	{
		$this->moduleRepository = $moduleRepository;
		return $this;
	}

	/**
	 * Persist notification object
	 * @param Notification $notification
	 * @throws \RuntimeException
	 */
	public function persist(Notification $notification)
	{
		if ( $notification->getId() === null ) {
			$statement  = "INSERT INTO %prefix%notification ";
			$statement .= "(id, from_user_id, to_user_id, message, link, `read`, module_id, created) ";
			$statement .= "VALUES (:id, :from_user_id, :to_user_id, :message, :link, :read, :module_id, :created)";
		} else {
			$statement  = 'UPDATE %prefix%notification ';
			$statement .= 'SET from_user_id = :from_user_id, to_user_id = :to_user_id, message = :message, ';
			$statement .= 'link = :link, `read` = :read, module_id = :module_id, created = :created ';
			$statement .= 'WHERE id = :id';
		}

		$query = new Query($statement, $this->prefix);
		$query->bind(array(
			':id'           => $notification->getId(),
			':from_user_id' => $notification->getFrom()->get('uid'),
			':to_user_id'   => $notification->getTo()->get('uid'),
			':message'      => $notification->getMessage(),
			':link'         => $notification->getLink(),
			':read'         => $notification->hasBeenRead(),
			':module_id'    => $notification->getModule()->get('mid'),
			':created'      => $notification->getCreated()->getTimestamp(),
		));

		if ( $this->database->queryF($query->toString()) === false ) {
			throw new \RuntimeException($this->database->error());
		}
	}

	/**
	 * Find by ID
	 * @param int $id
	 * @return Notification
	 */
	public function find($id)
	{
		$query = 'SELECT * FROM %prefix%notification WHERE id = :id';
		$query = new Query($query, $this->prefix);
		$query->bind(array(':id' => $id));
		$result = $this->database->query($query->toString());
		$notifications = $this->_constructNotificationsByQueryResult($result);
		return $notifications[0];
	}

	/**
	 * Find unread notifications by user
	 * @param int $userId
	 * @param int $limit
	 * @param int $start
	 * @return Notification[]
	 */
	public function findUnreadByUser($userId, $limit = 0, $start = 0)
	{
		$query = 'SELECT * FROM %prefix%notification WHERE to_user_id = :to_user_id AND `read` = 0 ORDER BY created DESC';
		$query = new Query($query, $this->prefix);
		$query->bind(array(':to_user_id' => $userId));
		$result = $this->database->query($query->toString(), $limit, $start);
		return $this->_constructNotificationsByQueryResult($result);
	}

	/**
	 * Find notifications by user
	 * @param int $userId
	 * @param int $limit
	 * @param int $start
	 * @return Notification[]
	 */
	public function findByUser($userId, $limit = 0, $start = 0)
	{
		$query = 'SELECT * FROM %prefix%notification WHERE to_user_id = :to_user_id ORDER BY created DESC';
		$query = new Query($query, $this->prefix);
		$query->bind(array(':to_user_id' => $userId));
		$result = $this->database->query($query->toString(), $limit, $start);
		return $this->_constructNotificationsByQueryResult($result);
	}

	/**
	 * Count notifications by user
	 * @param int $userId
	 * @return int
	 */
	public function countByUser($userId)
	{
		$query = 'SELECT COUNT(*) AS total FROM %prefix%notification WHERE to_user_id = :to_user_id ORDER BY created DESC';
		$query = new Query($query, $this->prefix);
		$query->bind(array(':to_user_id' => $userId));
		$result = $this->database->query($query->toString());
		$row    = $this->database->fetchArray($result);
		return intval($row['total']);
	}

	/**
	 * Construct notifications by query result
	 * @param resource $result
	 * @return Notification[]
	 */
	private function _constructNotificationsByQueryResult($result)
	{
		$notifications = array();

		while ( $row = $this->database->fetchArray($result) ) {
			$created = new DateTime();
			$created->setTimestamp($row['created']);
			$notification = new Notification();
			$notification
				->setId($row['id'])
				->setFrom($this->userRepository->getUser($row['from_user_id']))
				->setTo($this->userRepository->getUser($row['to_user_id']))
				->setMessage($row['message'])
				->setLink($row['link'])
				->setModule($this->moduleRepository->get($row['module_id']))
				->setCreated($created);

			if ( $row['read'] == 1 ) {
				$notification->setRead();
			} else {
				$notification->setUnread();
			}

			$notifications[] = $notification;
		}

		return $notifications;
	}
}
