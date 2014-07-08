<?php
namespace Lightwerk\SurfRunner\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfClasses\Domain\Model\Deployment;
use Lightwerk\SurfRunner\Log\Backend\DatabaseBackend;
use TYPO3\Flow\Log\Backend\FileBackend;
use TYPO3\Surf\Log\Backend\AnsiConsoleBackend;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class LoggerFactory {

	/**
	 * Create a default logger
	 *
	 * @param Deployment $deploymentRecord
	 * @param string $deploymentName
	 * @param integer $severityThreshold
	 * @param boolean $disableAnsi
	 * @param boolean $addFileBackend
	 * @param boolean $addConsoleBackend
	 * @return \TYPO3\Flow\Log\Logger
	 */
	public function getDefaultLogger(Deployment $deploymentRecord, $deploymentName, $severityThreshold = LOG_INFO, $disableAnsi = FALSE, $addFileBackend = FALSE, $addConsoleBackend = TRUE) {
		$logger = new \TYPO3\Flow\Log\Logger();

		$database = new DatabaseBackend(array(
			'severityThreshold' => $severityThreshold,
		));
		$database->setDeployment($deploymentRecord);
		$logger->addBackend($database);

		if ($addConsoleBackend) {
			$console = new AnsiConsoleBackend(array(
				'severityThreshold' => $severityThreshold,
				'disableAnsi' => $disableAnsi
			));
			$logger->addBackend($console);
		}

		if ($addFileBackend) {
			$file = new FileBackend(array(
				'logFileURL' => FLOW_PATH_DATA . 'Logs/Surf-' . $deploymentName . '.log',
				'createParentDirectories' => TRUE,
				'severityThreshold' => LOG_DEBUG,
				'logMessageOrigin' => FALSE
			));
			$logger->addBackend($file);
		}

		return $logger;
	}
}