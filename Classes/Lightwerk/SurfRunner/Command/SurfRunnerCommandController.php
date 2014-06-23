<?php
namespace Lightwerk\SurfRunner\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfCaptain\Domain\Repository\DeploymentRepository;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class SurfRunnerCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @FLOW\Inject
	 * @var DeploymentRepository
	 */
	protected $deploymentRepository;

	/**
	 * Deploy all waiting deployments
	 *
	 * @return void
	 */
	public function deployCommand() {

		$this->outputLine('You called the example command and passed "%s" as the first argument.');
	}

}