<?php
namespace Lightwerk\SurfRunner\Notification\Driver;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * HipChat Driver
 *
 * @Flow\Scope("singleton")
 * @package Lightwerk\SurfRunner
 */
class HipChatDriver
{

    const URL = 'https://api.hipchat.com/v2/';

    const MESSAGE_FORMAT_HTML = 'html';
    const MESSAGE_FORMAT_TEXT = 'text';

    const MESSAGE_COLOR_YELLOW = 'yellow';
    const MESSAGE_COLOR_RED = 'red';
    const MESSAGE_COLOR_GREEN = 'green';
    const MESSAGE_COLOR_PURPLE = 'purple';
    const MESSAGE_COLOR_GRAY = 'gray';
    const MESSAGE_COLOR_RANDOM = 'random';

    /**
     * @var array
     */
    protected $server = [
        'HTTP_CONTENT_TYPE' => 'application/json',
        'Accept' => 'application/json',
    ];

    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Http\Client\Browser
     */
    protected $browser;

    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Http\Client\CurlEngine
     */
    protected $browserRequestEngine;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return HipChatDriver
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return void
     */
    public function initializeObject()
    {
        $this->browserRequestEngine->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $this->browserRequestEngine->setOption(CURLOPT_SSL_VERIFYHOST, false);
        $this->browser->setRequestEngine($this->browserRequestEngine);
    }

    /**
     * @param integer $room
     * @param string $message
     * @param string $messageFormat
     * @param bool $notify
     * @param string $color
     * @return void
     * @throws Exception
     */
    public function sendMessage(
        $room,
        $message,
        $messageFormat = self::MESSAGE_FORMAT_TEXT,
        $notify = true,
        $color = self::MESSAGE_COLOR_YELLOW
    ) {
        $this->getApiResponse(
            'room/' . $room . '/notification',
            'POST',
            [
                'message' => $message,
                'message_format' => $messageFormat,
                'notify' => $notify,
                'color' => $color,
            ]
        );
    }

    /**
     * @param string $command
     * @param string $method
     * @param array $content
     * @return string
     * @throws Exception
     * @throws \TYPO3\Flow\Http\Client\InfiniteRedirectionException
     */
    protected function getApiResponse($command, $method = 'GET', array $content)
    {
        $url = self::URL . trim($command, '/') . '?' . http_build_query(['auth_token' => $this->settings['authToken']]);

        $response = $this->browser->request($url, $method, [], [], $this->server, json_encode($content));

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 400) {
            throw new Exception(
                'HipChat request was not successful. Response was: ' . $response->getStatus() . '. Content: ' . $response->getContent(),
                1408549039
            );
        }

        return $response->getContent();
    }
}
