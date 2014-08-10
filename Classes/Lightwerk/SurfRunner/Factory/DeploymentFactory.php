<?php
namespace Lightwerk\SurfRunner\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfCaptain\Domain\Repository\DeploymentRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Surf\Domain\Model\Deployment;

/**
 * @Flow\Scope("singleton")
 */
class DeploymentFactory {

	/**
	 * @FLOW\Inject
	 * @var DeploymentRepository
	 */
	protected $deploymentRepository;

	/**
	 * @FLOW\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @FLOW\Inject
	 * @var ApplicationFactory
	 */
	protected $applicationFactory;

	/**
	 * @FLOW\Inject
	 * @var LoggerFactory
	 */
	protected $loggerFactory;

	/**
	 * @return Deployment
	 */
	public function getDeploymentByDeploymentRecord(\Lightwerk\SurfCaptain\Domain\Model\Deployment $deploymentRecord) {
		$deploymentName = $this->persistenceManager->getIdentifierByObject($deploymentRecord);

		$deployment = new Deployment($deploymentName);

		$deployment->setLogger($this->loggerFactory->getDefaultLogger($deploymentRecord, $deploymentName));

		$configuration = $deploymentRecord->getConfiguration();
		if (!empty($configuration['applications']) && is_array($configuration['applications'])) {
			$applications = $this->applicationFactory->getApplicationsByConfiguration($configuration['applications']);
			foreach ($applications as $application) {
				$deployment->addApplication($application);
			}
		}

		return $deployment;
	}
}