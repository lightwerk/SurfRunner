<?php
namespace Lightwerk\SurfRunner\Log\Backend;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfClasses\Domain\Model\Deployment;
use Lightwerk\SurfClasses\Domain\Model\Log;
use Lightwerk\SurfClasses\Domain\Repository\LogRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;

class DatabaseBackend extends \TYPO3\Flow\Log\Backend\AbstractBackend {

	/**
	 * @FLOW\Inject
	 * @var LogRepository
	 */
	protected $logRepository;

	/**
	 * @FLOW\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @var Deployment
	 */
	protected $deployment;

	/**
	 * @var integer
	 */
	protected $number = 0;

	/**
	 * @param Deployment $deployment
	 * @return DatabaseBackend
	 */
	public function setDeployment(Deployment $deployment) {
		$this->deployment = $deployment;
		return $this;
	}

	public function open() {}

	/**
	 * Appends the given message along with the additional information into the log.
	 *
	 * @param string $message The message to log
	 * @param integer $severity One of the LOG_* constants
	 * @param mixed $additionalData A variable containing more information about the event to be logged
	 * @param string $packageKey Key of the package triggering the log (determined automatically if not specified)
	 * @param string $className Name of the class triggering the log (determined automatically if not specified)
	 * @param string $methodName Name of the method triggering the log (determined automatically if not specified)
	 * @return void
	 * @api
	 */
	public function append($message, $severity = LOG_INFO, $additionalData = NULL, $packageKey = NULL, $className = NULL, $methodName = NULL) {
		$log = new Log();
		$log->setDeployment($this->deployment)
			->setDate(new \DateTime())
			->setNumber(++$this->number)
			->setMessage($message)
			->setSeverity($severity);
		$this->logRepository->add($log);
		$this->persistenceManager->persistAll();
	}

	public function close() {}
}