<?php
namespace Lightwerk\SurfRunner\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfCaptain\Domain\Model\Deployment as SurfCaptainDeployment;
use Lightwerk\SurfCaptain\Domain\Repository\DeploymentRepository;
use Lightwerk\SurfRunner\Exception\NoAvailableDeploymentException;
use Lightwerk\SurfRunner\Factory\DeploymentFactory;
use Lightwerk\SurfRunner\Log\Backend\DatabaseBackend;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\LoggerInterface;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Surf\Domain\Model\Deployment;

/**
 * Deployment Service
 *
 * @Flow\Scope("singleton")
 * @package Lightwerk\SurfRunner
 */
class DeploymentService {

	/**
	 * @Flow\Inject
	 * @var DeploymentRepository
	 */
	protected $deploymentRepository;

	/**
	 * @Flow\Inject
	 * @var DeploymentFactory
	 */
	protected $deploymentFactory;

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject()
	 * @var \TYPO3\Flow\SignalSlot\Dispatcher
	 */
	protected $signalDispatcher;

	/**
	 * @param LoggerInterface $logger
	 * @param boolean $dryRun
	 * @return \Lightwerk\SurfRunner\Domain\Model\Deployment
	 * @throws \Lightwerk\SurfRunner\Exception\NoAvailableDeploymentException
	 * @throws \Lightwerk\SurfRunner\Factory\Exception
	 * @throws \TYPO3\Surf\Exception
	 */
	public function deployWaitingFromQueue(LoggerInterface $logger, $dryRun) {
		/** @var SurfCaptainDeployment $surfCaptainDeployment */
		$surfCaptainDeployment = $this->deploymentRepository->findOneByStatus(SurfCaptainDeployment::STATUS_WAITING);
		if (
			$surfCaptainDeployment instanceof SurfCaptainDeployment === FALSE ||
			$this->deploymentRepository->countByRepositoryUrlAndStatus($surfCaptainDeployment->getRepositoryUrl(), SurfCaptainDeployment::STATUS_RUNNING) > 0
		) {
			throw new NoAvailableDeploymentException();
		}

		if (!$dryRun) {
			$this->setStatusBeforeDeployment($surfCaptainDeployment);
		}

		if ($this->deploymentRepository->countByRepositoryUrlAndStatus($surfCaptainDeployment->getRepositoryUrl(), SurfCaptainDeployment::STATUS_RUNNING) > 1) {
			// Stop if two deployments of the same repositoryUrl are running
			$this->setStatusBackToWaiting($surfCaptainDeployment);
			throw new NoAvailableDeploymentException();
		}

		$logger->addBackend(
			new DatabaseBackend(
				array('deployment' => $surfCaptainDeployment, 'severityThreshold' => LOG_DEBUG)
			)
		);

		$deployment = $this->deploymentFactory->getDeploymentByDeploymentRecord($surfCaptainDeployment, $logger);
		$deployment->initialize();
		if (!$dryRun) {
			$this->emitDeploymentStarted($deployment, $surfCaptainDeployment);
			$deployment->deploy();
			$this->setStatusAfterDeployment($surfCaptainDeployment, $deployment->getStatus());
			$this->emitDeploymentFinished($deployment, $surfCaptainDeployment);
		} else {
			$deployment->simulate();
		}

		return $deployment;
	}

	/**
	 * Signalizes that a deployment started
	 *
	 * @Flow\Signal
	 * @param Deployment $deployment
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 * @throws \TYPO3\Flow\SignalSlot\Exception\InvalidSlotException
	 */
	protected function emitDeploymentStarted(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment) {}

	/**
	 * Signalizes that a deployment finished
	 *
	 * @Flow\Signal
	 * @param Deployment $deployment
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 * @throws \TYPO3\Flow\SignalSlot\Exception\InvalidSlotException
	 */
	protected function emitDeploymentFinished(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment) {}

	/**
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 * @throws \TYPO3\Flow\Persistence\Exception\IllegalObjectTypeException
	 */
	protected function writeSurfCaptainDeployment(SurfCaptainDeployment $surfCaptainDeployment) {
		$this->deploymentRepository->update($surfCaptainDeployment);
		$this->persistenceManager->persistAll();
	}

	/**
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 */
	protected function setStatusBeforeDeployment(SurfCaptainDeployment $surfCaptainDeployment) {
		$surfCaptainDeployment->setStatus(SurfCaptainDeployment::STATUS_RUNNING);
		$this->writeSurfCaptainDeployment($surfCaptainDeployment);
	}

	/**
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 */
	protected function setStatusBackToWaiting(SurfCaptainDeployment $surfCaptainDeployment) {
		$surfCaptainDeployment->setStatus(SurfCaptainDeployment::STATUS_WAITING);
		$this->writeSurfCaptainDeployment($surfCaptainDeployment);
	}

	/**
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @param integer $status
	 * @return void
	 */
	protected function setStatusAfterDeployment(SurfCaptainDeployment $surfCaptainDeployment, $status) {
		switch ($status) {
			case Deployment::STATUS_SUCCESS:
				$statusValue = SurfCaptainDeployment::STATUS_SUCCESS;
				break;
			case Deployment::STATUS_UNKNOWN:
				// Fall through
			case Deployment::STATUS_FAILED:
				$statusValue = SurfCaptainDeployment::STATUS_FAILED;
				break;
			case Deployment::STATUS_CANCELLED:
				$statusValue = SurfCaptainDeployment::STATUS_CANCELLED;
				break;
			default:
				$statusValue = $status;
		}
		$surfCaptainDeployment->setStatus($statusValue);
		$this->writeSurfCaptainDeployment($surfCaptainDeployment);
	}
}