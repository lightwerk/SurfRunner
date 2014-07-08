<?php
namespace Lightwerk\SurfRunner\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfClasses\Domain\Repository\DeploymentRepository;
use Lightwerk\SurfRunner\Factory\DeploymentFactory;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Surf\Domain\Model\Deployment;

/**
 * @Flow\Scope("singleton")
 */
class DeploymentService {

	/**
	 * @FLOW\Inject
	 * @var DeploymentRepository
	 */
	protected $deploymentRepository;

	/**
	 * @FLOW\Inject
	 * @var DeploymentFactory
	 */
	protected $deploymentFactory;

	/**
	 * @FLOW\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @return Deployment
	 */
	public function deployWaitingFromQueue() {
		/** @var \Lightwerk\SurfClasses\Domain\Model\Deployment $deploymentRecord */
		$deploymentRecord = $this->deploymentRepository->findOneByStatus('waiting');
		$deploymentRecord->setStatus('running');
		$this->deploymentRepository->update($deploymentRecord);
		$this->persistenceManager->persistAll();

		$deployment = $this->deploymentFactory->getDeploymentByDeploymentRecord($deploymentRecord);
		$deployment->initialize();
		$deployment->deploy();

		switch ($deployment->getStatus()) {
			case Deployment::STATUS_SUCCESS:
				$status = 'success';
				break;
			case Deployment::STATUS_FAILED:
				$status = 'failed';
				break;
			case Deployment::STATUS_CANCELLED:
				$status = 'cancelled';
				break;
			case Deployment::STATUS_UNKNOWN:
				$status = 'unknown';
				break;
			default:
				$status = $deployment->getStatus();
		}
		$deploymentRecord->setStatus($status);
		$this->deploymentRepository->update($deploymentRecord);
		$this->persistenceManager->persistAll();

		return $deployment;
	}
}