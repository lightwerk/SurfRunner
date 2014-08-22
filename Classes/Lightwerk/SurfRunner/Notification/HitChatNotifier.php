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
	 * @param Deployment $deployment
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 */
	public function deploymentStarted(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment) {
		$settings = $this->getSettingsForFunction('deploymentStarted', $surfCaptainDeployment);

		if (empty($settings['enabled'])) {
			return;
		}

		$view = new \TYPO3\Fluid\View\StandaloneView();
		$view->setTemplatePathAndFilename($settings['templatePathAndFilename']);
		$view->assign('deployment', $deployment)
			 ->assign('surfCaptainDeployment', $surfCaptainDeployment)
			 ->assign('settings', $settings);

		$this->hipChatDriver->setSettings($settings)
							->sendMessage($settings['room'], $view->render(), HipChatDriver::MESSAGE_FORMAT_HTML);
	}

	/**
	 * @param Deployment $deployment
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return void
	 */
	public function deploymentFinished(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment) {
		$settings = $this->getSettingsForFunction('deploymentFinished', $surfCaptainDeployment);

		if (empty($settings['enabled'])) {
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
		$view->setTemplatePathAndFilename($settings['templatePathAndFilename']);
		$view->assign('deployment', $deployment)
			 ->assign('surfCaptainDeployment', $surfCaptainDeployment)
			 ->assign('settings', $settings);

		$this->hipChatDriver->setSettings($settings)
							->sendMessage($settings['room'], $view->render(), HipChatDriver::MESSAGE_FORMAT_HTML, TRUE, $color);
	}

	/**
	 * @param string $key
	 * @param SurfCaptainDeployment $surfCaptainDeployment
	 * @return array
	 */
	protected function getSettingsForFunction($key, SurfCaptainDeployment $surfCaptainDeployment) {
		$settings = array_merge($this->settings, $this->settings[$key]);

		$repositoryUrl = $surfCaptainDeployment->getRepositoryUrl();
		if (!empty($repositoryUrl) && !empty($settings[$repositoryUrl][$key])) {
			$settings = array_merge($settings, $settings[$repositoryUrl][$key]);
		}

		return $settings;
	}
}