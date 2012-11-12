<?php

namespace Suin\Notification\Repository;

use XoopsDatabase as Database;

abstract class RepositoryBase
{
	/**
	 * @var \XoopsMySQLDatabase
	 */
	protected $database;

	/**
	 * @var string
	 */
	protected $prefix;

	/**
	 * @param \XoopsDatabase $database
	 * @return static
	 */
	public function setDatabase(Database $database)
	{
		$this->database = $database;
		return $this;
	}

	/**
	 * Set prefix
	 * @param string $prefix
	 * @return static
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
		return $this;
	}
}
