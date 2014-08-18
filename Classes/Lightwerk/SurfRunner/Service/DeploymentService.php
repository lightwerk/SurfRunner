<?php
namespace Lightwerk\SurfRunner\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfCaptain\Domain\Repository\DeploymentRepository;
use Lightwerk\SurfRunner\Exception\NoAvailableDeploymentException;
use Lightwerk\SurfRunner\Factory\DeploymentFactory;
use Lightwerk\SurfRunner\Log\Backend\DatabaseBackend;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\LoggerInterface;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Surf\Domain\Model\Deployment;
use Lightwerk\SurfCaptain\Domain\Model\Deployment as SurfCaptainDeployment;

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
	 * @param LoggerInterface $logger
	 * @param boolean $dryRun
	 * @return Deployment
	 */
	public function deployWaitingFromQueue(LoggerInterface $logger, $dryRun) {
		/** @var SurfCaptainDeployment $surfCaptainDeployment */
		$surfCaptainDeployment = $this->deploymentRepository->findOneByStatus(SurfCaptainDeployment::STATUS_WAITING);
		if ($surfCaptainDeployment instanceof SurfCaptainDeployment === false) {
			throw new NoAvailableDeploymentException();
		}
		$this->setStatusBeforeDeployment($surfCaptainDeployment, $dryRun);

		$logger->addBackend(
			new DatabaseBackend(
				array('deployment' => $surfCaptainDeployment, 'severityThreshold' => LOG_DEBUG)
			)
		);

		$deployment = $this->deploymentFactory->getDeploymentByDeploymentRecord($surfCaptainDeployment, $logger);
		$deployment->initialize();
		if (!$dryRun) {
			$deployment->deploy();
		} else {
			$deployment->simulate();
		}

		$this->setStatusAfterDeployment($surfCaptainDeployment, $deployment->getStatus(), $dryRun);

		return $deployment;
	}

	/**
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @param bool $dryRun
	 * @return void
	 * @throws \TYPO3\Flow\Persistence\Exception\IllegalObjectTypeException
	 */
	protected function writeSurfCaptainDeployment(SurfCaptainDeployment $surfCaptainDeployment, $dryRun) {
		if (!$dryRun) {
			$this->deploymentRepository->update($surfCaptainDeployment);
			$this->persistenceManager->persistAll();
		}
	}

	/**
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @param boolean $dryRun
	 * @return void
	 */
	protected function setStatusBeforeDeployment(SurfCaptainDeployment $surfCaptainDeployment, $dryRun) {
		$surfCaptainDeployment->setStatus(SurfCaptainDeployment::STATUS_RUNNING);
		$this->writeSurfCaptainDeployment($surfCaptainDeployment, $dryRun);
	}

	/**
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @param integer $status
	 * @param boolean $dryRun
	 * @return void
	 */
	protected function setStatusAfterDeployment(SurfCaptainDeployment $surfCaptainDeployment, $status, $dryRun) {
		switch ($status) {
			case Deployment::STATUS_SUCCESS:
				$status = SurfCaptainDeployment::STATUS_SUCCESS;
				break;
			case Deployment::STATUS_UNKNOWN:
			case Deployment::STATUS_FAILED:
				$status = SurfCaptainDeployment::STATUS_FAILED;
				break;
			case Deployment::STATUS_CANCELLED:
				$status = SurfCaptainDeployment::STATUS_CANCELLED;
				break;
		}
		$surfCaptainDeployment->setStatus($status);
		$this->writeSurfCaptainDeployment($surfCaptainDeployment, $dryRun);
	}
}