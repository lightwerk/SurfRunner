<?php
namespace Lightwerk\SurfRunner\Notification;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfCaptain\Domain\Model\Deployment as SurfCaptainDeployment;
use Lightwerk\SurfRunner\Notification\Driver\HipChatDriver;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\View\StandaloneView;
use TYPO3\Surf\Domain\Model\Deployment;

/**
 * HitChat Notifier
 *
 * @Flow\Scope("singleton")
 * @package Lightwerk\SurfRunner
 */
class HitChatNotifier
{
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
    public function injectSettings(array $settings)
    {
        $this->settings = $settings['notification']['hipChat'];
    }

    /**
     * @param Deployment $deployment
     * @param SurfCaptainDeployment $surfCaptainDeployment
     * @return void
     */
    public function deploymentStarted(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment)
    {
        $this->sendHipChatMessage('deploymentStarted', $deployment, $surfCaptainDeployment);
    }

    /**
     * @param string $key
     * @param Deployment $deployment
     * @param SurfCaptainDeployment $surfCaptainDeployment
     * @return void
     */
    protected function sendHipChatMessage($key, Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment)
    {
        $settings = $this->getSettingsForFunction($key, $surfCaptainDeployment);
        $pathParts = pathinfo($settings['templatePathAndFilename']);

        $view = new StandaloneView();
        $view->setTemplatePathAndFilename($settings['templatePathAndFilename']);
        $view->setPartialRootPath($pathParts['dirname'] . '/Partials/');
        $view->setPartialRootPath($pathParts['dirname'] . '/Layouts/');
        $view->assign('deployment', $deployment)
            ->assign('surfCaptainDeployment', $surfCaptainDeployment)
            ->assign('settings', $settings);

        $enabled = trim($view->renderSection('Enabled'));
        if (empty($enabled)) {
            return;
        }

        switch (strtolower($pathParts['extension'])) {
            case 'txt':
                $format = HipChatDriver::MESSAGE_FORMAT_TEXT;
                break;
            default:
                $format = HipChatDriver::MESSAGE_FORMAT_HTML;
        }

        switch ($surfCaptainDeployment->getStatus()) {
            case SurfCaptainDeployment::STATUS_RUNNING:
                $color = HipChatDriver::MESSAGE_COLOR_YELLOW;
                break;
            case SurfCaptainDeployment::STATUS_FAILED:
                $color = HipChatDriver::MESSAGE_COLOR_RED;
                break;
            default:
                $color = HipChatDriver::MESSAGE_COLOR_GREEN;
        }

        $this->hipChatDriver
            ->setSettings($settings)
            ->sendMessage($settings['room'], $view->renderSection('Message'), $format, true, $color);
    }

    /**
     * @param string $key
     * @param SurfCaptainDeployment $surfCaptainDeployment
     * @return array
     */
    protected function getSettingsForFunction($key, SurfCaptainDeployment $surfCaptainDeployment)
    {
        $settings = array_merge($this->settings, $this->settings[$key]);

        $repositoryUrl = $surfCaptainDeployment->getRepositoryUrl();
        if (!empty($repositoryUrl) && !empty($settings[$repositoryUrl][$key])) {
            $settings = array_merge($settings, $settings[$repositoryUrl][$key]);
        }

        return $settings;
    }

    /**
     * @param Deployment $deployment
     * @param SurfCaptainDeployment $surfCaptainDeployment
     * @return void
     */
    public function deploymentFinished(Deployment $deployment, SurfCaptainDeployment $surfCaptainDeployment)
    {
        $this->sendHipChatMessage('deploymentFinished', $deployment, $surfCaptainDeployment);
    }
}
