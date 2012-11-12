<?php

namespace Suin\Notification\Repository;

class Query
{
	/**
	 * @var string
	 */
	private $query;

	/**
	 * @var string
	 */
	private $prefix;

	/**
	 * @var array
	 */
	private $values = array();

	/**
	 * Return new Query object
	 * @param string $query
	 * @param string $prefix
	 */
	public function __construct($query, $prefix = null)
	{
		$this->query = $query;
		$this->prefix = $prefix;
	}

	/**
	 * Bind values
	 * @param array $values
	 * @return Query
	 */
	public function bind(array $values)
	{
		$this->values = $values;
		return $this;
	}

	/**
	 * Return query as string
	 * @return string
	 */
	public function toString()
	{
		$query = str_replace('%prefix%', $this->prefix, $this->query);
		return $this->_replacePlaceholders($query, $this->values);
	}

	/**
	 * Replace placeholders
	 * @param string $query
	 * @param array $values
	 * @return string
	 */
	private function _replacePlaceholders($query, $values)
	{
		$values = $this->_escapeValues($values);
		return str_replace(array_keys($values), array_values($values), $query);
	}

	/**
	 * Escape values
	 * @param array $values
	 * @return array
	 */
	private function _escapeValues(array $values)
	{
		foreach ( $values as $key => $value ) {
			if ( is_string($value) ) {
				$values[$key] = '"'.mysql_real_escape_string($value).'"'; // TODO
			}

			if ( $value === null ) {
				$values[$key] = 'NULL';
			}

			if ( is_bool($value) ) {
				$values[$key] = ( $value ) ? 1 : 0;
			}
		}

		return $values;
	}
}

