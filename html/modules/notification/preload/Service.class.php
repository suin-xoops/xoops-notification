<?php
/**
 * Sample:
 *
 * ```
 * XCube_DelegateUtils::call('Notification.Send', array(
 *     'from_user_id' => 1,
 *     'to_user_id'   => 123,
 *     'message'      => 'Hello World!!',
 *     'link'         => '/modules/bulletin/story.php?id=123',
 *     'module_id'    => 11,
 * ));
 * ```
 */

use Suin\Notification\DependencyInjectionContainer as Container;

class Notification_Service extends XCube_ActionFilter
{
	public function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add('Notification.Send', array($this, 'send'));
	}

	/**
	 * @param array $arguments
	 * @throws Exception
	 * @return void
	 */
	public function send(array $arguments)
	{
		// ASSERTION FOR PRECONDITION
		$this->_assertArgument($arguments, 'to_user_id');
		$this->_assertArgument($arguments, 'from_user_id');
		$this->_assertArgument($arguments, 'message');
		$this->_assertArgument($arguments, 'link');
		$this->_assertArgument($arguments, 'module_id');

		try {
			$sendingService = Container::getSendingService();
			$sendingService->send(
				$arguments['to_user_id'],
				$arguments['from_user_id'],
				$arguments['message'],
				$arguments['link'],
				$arguments['module_id']);
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * @param array $arguments
	 * @param string $key
	 * @throws InvalidArgumentException
	 * @return void
	 */
	private function _assertArgument(array $arguments, $key)
	{
		if ( isset($arguments[$key]) === false ) {
			throw new InvalidArgumentException(sprintf('"%s" is missing for arguments', $key));
		}
	}
}