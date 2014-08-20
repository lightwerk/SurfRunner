<?php
namespace Lightwerk\SurfRunner\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Service\DeploymentService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\LoggerInterface;
use TYPO3\Surf\Log\Backend\AnsiConsoleBackend;

/**
 * @Flow\Scope("singleton")
 */
class SurfRunnerCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @FLOW\Inject
	 * @var DeploymentService
	 */
	protected $deploymentService;

	/**
	 * Deploy one from waiting queue
	 *
	 * @param boolean $dryRun
	 * @return void
	 */
	public function deployWaitingFromQueueCommand($dryRun = FALSE) {
		$logger = $this->getLogger();
		try {
			$deployment = $this->deploymentService->deployWaitingFromQueue($logger, $dryRun);
			$status = $deployment->getStatus();
		} catch (\Lightwerk\SurfRunner\Exception\NoAvailableDeploymentException $e) {
			$logger->log('No waiting deployments', LOG_INFO);
			$status = 0;
		} catch (\Lightwerk\SurfRunner\Factory\Exception $e) {
			$logger->log('Configuration error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')', LOG_ERR);
			$status = 1;
		} catch (\Exception $e) {
			$logger->log('Deployment error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')', LOG_ERR);
			$status = 1;
		}
		$this->response->setExitCode($status);
	}

	/**
	 * @return LoggerInterface
	 */
	protected function getLogger() {
		$logger = new \TYPO3\Flow\Log\Logger();
		$logger->addBackend(
			new AnsiConsoleBackend(array('severityThreshold' => LOG_DEBUG))
		);
		return $logger;
	}

}