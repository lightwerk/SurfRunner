<?php
namespace Lightwerk\SurfRunner\Notification;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Notification\Driver\HipChatDriver;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\I18n\Translator;
use TYPO3\Surf\Domain\Model\Deployment;
use Lightwerk\SurfCaptain\Domain\Model\Deployment as SurfCaptainDeployment;

/**
 * @Flow\Scope("singleton")
 */
class HitChatNotifier {
	/**
	 * @Flow\Inject
	 * @var HipChatDriver
	 */
	protected $hipChatDriver;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var Translator
	 */
	protected $translator;

	/**
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings['notification']['hipChat'];
	}

	public function initializeObject() {
		$this->hipChatDriver->setSettings($this->settings);
	}

	/**
	 * @param Deployment $deployment
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 */
	public function deploymentStarted(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment) {
		if (!empty($this->settings['deploymentStarted']['enabled'])) {
			$arguments = array(
				'name' => $deployment->getName(),
				'link' => str_replace('{{identifier}}', $deployment->getName(), $this->settings['frontendUrl']),
				'repositoryUrl' => $surfCaptainDeployment->getRepositoryUrl(),
				'referenceName' => $surfCaptainDeployment->getReferenceName(),
				'type' => $surfCaptainDeployment->getType(),
				'status' => $surfCaptainDeployment->getStatus(),
			);

			$this->hipChatDriver->sendMessage(
				$this->settings['deploymentStarted']['room'],
				$this->getTranslation('notification.deploymentStarted', $arguments)
			);
		}
	}

	/**
	 * @param Deployment $deployment
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 */
	public function deploymentFinished(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment) {
		if (!empty($this->settings['deploymentFinished']['enabled'])) {
			$arguments = array(
				'name' => $deployment->getName(),
				'link' => str_replace('{{identifier}}', $deployment->getName(), $this->settings['frontendUrl']),
				'repositoryUrl' => $surfCaptainDeployment->getRepositoryUrl(),
				'referenceName' => $surfCaptainDeployment->getReferenceName(),
				'type' => $surfCaptainDeployment->getType(),
				'status' => $surfCaptainDeployment->getStatus(),
			);

			if ($surfCaptainDeployment->getStatus() !== SurfCaptainDeployment::STATUS_FAILED) {
				$color = HipChatDriver::MESSAGE_COLOR_GREEN;
			} else {
				$color = HipChatDriver::MESSAGE_COLOR_RED;
			}

			$this->hipChatDriver->sendMessage(
				$this->settings['deploymentFinished']['room'],
				$this->getTranslation('notification.deploymentFinished', $arguments),
				HipChatDriver::MESSAGE_FORMAT_TEXT,
				TRUE,
				$color
			);
		}
	}

	/**
	 * @param string $id
	 * @param array $arguments
	 * @param integer $quantity
	 * @return string
	 */
	protected function getTranslation($id, array $arguments = array(), $quantity = NULL) {
		return $this->translator->translateById($id, $arguments, $quantity, NULL, 'Main', 'Lightwerk.SurfRunner');
	}
}