<?php

namespace Suin\Notification\Entity;

use DateTime;
use XoopsUser as User;
use XoopsModule as Module;

class Notification
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var User
	 */
	private $from;

	/**
	 * @var User
	 */
	private $to;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var string
	 */
	private $link;

	/**
	 * @var bool
	 */
	private $read;

	/**
	 * @var Module
	 */
	private $module;

	/**
	 * @var DateTime
	 */
	private $created;

	/**
	 * Return new notification object
	 */
	public function __construct()
	{
		$this->read = false;
		$this->created = new DateTime();
	}

	/**
	 * Set notification ID
	 * @param int $id
	 * @return Notification
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Set notification ID
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set from-user object
	 * @param User $user
	 * @return Notification
	 */
	public function setFrom(User $user)
	{
		$this->from = $user;
		return $this;
	}

	/**
	 * Return from-user object
	 * @return User
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * Set to-user object
	 * @param User $user
	 * @return Notification
	 */
	public function setTo(User $user)
	{
		$this->to = $user;
		return $this;
	}

	/**
	 * Return to-user object
	 * @return User
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 * Determine if this notification is sent to the user
	 * @param User $user
	 * @return bool
	 */
	public function isSentTo(User $user)
	{
		return ( $this->to === $user );
	}

	/**
	 * Set message
	 * @param string $message
	 * @return Notification
	 */
	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}

	/**
	 * Return message
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Set link
	 * @param string $link
	 * @return Notification
	 */
	public function setLink($link)
	{
		$this->link = $link;
		return $this;
	}

	/**
	 * Return link
	 * @return string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Set read
	 * @return Notification
	 */
	public function setRead()
	{
		$this->read = true;
		return $this;
	}

	/**
	 * Set unread
	 * @return Notification
	 */
	public function setUnread()
	{
		$this->read = false;
		return $this;
	}

	/**
	 * Determine if this notification has been read
	 * @return bool
	 */
	public function hasBeenRead()
	{
		return ( $this->read === true );
	}

	/**
	 * Set module
	 * @param Module $module
	 * @return Notification
	 */
	public function setModule(Module $module)
	{
		$this->module = $module;
		return $this;
	}

	/**
	 * Return module
	 * @return Module
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * Set created date time
	 * @param DateTime $created
	 * @return Notification
	 */
	public function setCreated(DateTime $created)
	{
		$this->created = $created;
		return $this;
	}

	/**
	 * Return created date time
	 * @return DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}
}
