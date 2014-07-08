<?php
namespace Lightwerk\SurfRunner\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Service\DeploymentService;
use TYPO3\Flow\Annotations as Flow;

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
	 * @return void
	 */
	public function deployWaitingFromQueueCommand() {
		$deployment = $this->deploymentService->deployWaitingFromQueue();
		$this->response->setExitCode(
			$deployment->getStatus()
		);
	}

}