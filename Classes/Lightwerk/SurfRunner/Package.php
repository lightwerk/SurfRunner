<?php
namespace Lightwerk\SurfRunner;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Package\Package as BasePackage;

/**
 * The Surf Runner Package of Lightwerk
 */
class Package extends BasePackage {

	/**
	 * Invokes custom PHP code directly after the package manager has been initialized.
	 *
	 * @param Bootstrap $bootstrap The current bootstrap
	 * @return void
	 */
	public function boot(Bootstrap $bootstrap) {
		$dispatcher = $bootstrap->getSignalSlotDispatcher();

		if (!$bootstrap->getContext()->isProduction()) {
			$dispatcher->connect('Lightwerk\SurfRunner\Service\DeploymentService', 'deploymentStarted', 'Lightwerk\SurfRunner\Notification\HitChatNotifier', 'deploymentStarted');
			$dispatcher->connect('Lightwerk\SurfRunner\Service\DeploymentService', 'deploymentFinished', 'Lightwerk\SurfRunner\Notification\HitChatNotifier', 'deploymentFinished');
		}
	}
}
