<?php
namespace Lightwerk\SurfRunner\Notification;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Notification\Driver\HipChatDriver;
use TYPO3\Flow\Annotations as Flow;
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
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings['notification']['hipChat'];
	}

	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->hipChatDriver->setSettings($this->settings);
	}

	/**
	 * @param Deployment $deployment
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 */
	public function deploymentStarted(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment) {
		if (empty($this->settings['deploymentStarted']['enabled'])) {
			return;
		}

		$view = new \TYPO3\Fluid\View\StandaloneView();
		$view->setTemplatePathAndFilename($this->settings['deploymentStarted']['templatePathAndFilename']);
		$view->assign('deployment', $deployment)
			 ->assign('surfCaptainDeployment', $surfCaptainDeployment)
			 ->assign('settings', array_merge($this->settings, $this->settings['deploymentStarted']));

		$this->hipChatDriver->sendMessage(
			$this->settings['deploymentStarted']['room'],
			$view->render(),
			HipChatDriver::MESSAGE_FORMAT_HTML
		);
	}

	/**
	 * @param Deployment $deployment
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 */
	public function deploymentFinished(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment) {
		if (empty($this->settings['deploymentFinished']['enabled'])) {
			return;
		}

		switch ($surfCaptainDeployment->getStatus()) {
			case SurfCaptainDeployment::STATUS_FAILED:
				$color = HipChatDriver::MESSAGE_COLOR_RED;
				break;
			default:
				$color = HipChatDriver::MESSAGE_COLOR_GREEN;
		}

		$view = new \TYPO3\Fluid\View\StandaloneView();
		$view->setTemplatePathAndFilename($this->settings['deploymentFinished']['templatePathAndFilename']);
		$view->assign('deployment', $deployment)
			 ->assign('surfCaptainDeployment', $surfCaptainDeployment)
			 ->assign('settings', array_merge($this->settings, $this->settings['deploymentFinished']));

		$this->hipChatDriver->sendMessage(
			$this->settings['deploymentFinished']['room'],
			$view->render(),
			HipChatDriver::MESSAGE_FORMAT_HTML,
			TRUE,
			$color
		);
	}
}