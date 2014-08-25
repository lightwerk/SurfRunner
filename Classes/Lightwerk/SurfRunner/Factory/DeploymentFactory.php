<?php
namespace Lightwerk\SurfRunner\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfCaptain\Domain\Repository\DeploymentRepository;
use Lightwerk\SurfRunner\Domain\Model\Deployment;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\LoggerInterface;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;

/**
 * Deployment Factory
 *
 * @Flow\Scope("singleton")
 * @package Lightwerk\SurfRunner
 */
class DeploymentFactory {

	/**
	 * @Flow\Inject
	 * @var DeploymentRepository
	 */
	protected $deploymentRepository;

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject
	 * @var ApplicationFactory
	 */
	protected $applicationFactory;

	/**
	 * @param \Lightwerk\SurfCaptain\Domain\Model\Deployment $surfCaptainDeployment
	 * @param \TYPO3\Flow\Log\LoggerInterface $logger
	 * @return Deployment
	 * @throws Exception
	 */
	public function getDeploymentByDeploymentRecord(\Lightwerk\SurfCaptain\Domain\Model\Deployment $surfCaptainDeployment,
													LoggerInterface $logger) {
		$deployment = new Deployment(
			$this->persistenceManager->getIdentifierByObject($surfCaptainDeployment)
		);
		$deployment->setLogger($logger);

		$configuration = $surfCaptainDeployment->getConfiguration();
		if (empty($configuration['applications']) || !is_array($configuration['applications'])) {
			throw new Exception('No applications are given in deployment configuration', 1408397565);
		}
		$applications = $this->applicationFactory->getApplicationsByConfiguration($configuration['applications']);
		foreach ($applications as $application) {
			$deployment->addApplication($application);
		}

		return $deployment;
	}
}