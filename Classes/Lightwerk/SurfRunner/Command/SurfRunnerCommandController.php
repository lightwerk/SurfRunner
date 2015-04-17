<?php
namespace Lightwerk\SurfRunner\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Exception\NoAvailableDeploymentException;
use Lightwerk\SurfRunner\Factory\Exception;
use Lightwerk\SurfRunner\Service\DeploymentService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;
use TYPO3\Flow\Log\Logger;
use TYPO3\Flow\Log\LoggerInterface;
use TYPO3\Surf\Log\Backend\AnsiConsoleBackend;

/**
 * SurfRunner Command Controller
 *
 * @Flow\Scope("singleton")
 * @package Lightwerk\SurfRunner
 */
class SurfRunnerCommandController extends CommandController {

	/**
	 * @FLOW\Inject
	 * @var DeploymentService
	 */
	protected $deploymentService;

	/**
	 * @param string $identifier
	 * @param boolean $dryRun
	 * @param boolean $disableAnsi
	 * @return void
	 */
	public function deployCommand($identifier, $dryRun = FALSE, $disableAnsi = FALSE) {
		$logger = $this->getLogger($disableAnsi);
		try {
			$deployment = $this->deploymentService->deployByIdentifier($identifier, $logger, $dryRun);
			$status = $deployment->getStatus();
		} catch (NoAvailableDeploymentException $e) {
			$logger->log($e->getMessage(), LOG_INFO);
			$status = 0;
		} catch (Exception $e) {
			$logger->log('Configuration error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')', LOG_ERR);
			$status = 1;
		} catch (\Exception $e) {
			$logger->log('Deployment error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')', LOG_ERR);
			$status = 1;
		}
		$this->response->setExitCode($status);
	}

	/**
	 * Deploy one from waiting queue
	 *
	 * @param boolean $dryRun
	 * @param boolean $disableAnsi
	 * @return void
	 */
	public function deployWaitingFromQueueCommand($dryRun = FALSE, $disableAnsi = FALSE) {
		$logger = $this->getLogger($disableAnsi);
		try {
			$deployment = $this->deploymentService->deployWaitingFromQueue($logger, $dryRun);
			$status = $deployment->getStatus();
		} catch (NoAvailableDeploymentException $e) {
			$logger->log($e->getMessage(), LOG_INFO);
			$status = 0;
		} catch (Exception $e) {
			$logger->log('Configuration error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')', LOG_ERR);
			$status = 1;
		} catch (\Exception $e) {
			$logger->log('Deployment error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')', LOG_ERR);
			$status = 1;
		}
		$this->response->setExitCode($status);
	}

	/**
	 * @param boolean $disableAnsi
	 * @return LoggerInterface
	 */
	protected function getLogger($disableAnsi = FALSE) {
		$logger = new Logger();
		$logger->addBackend(
			new AnsiConsoleBackend(array('severityThreshold' => LOG_DEBUG, 'disableAnsi' => $disableAnsi))
		);
		return $logger;
	}

}
