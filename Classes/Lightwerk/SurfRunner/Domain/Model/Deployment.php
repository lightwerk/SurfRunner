<?php
namespace Lightwerk\SurfRunner\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfCaptain\Utility\GeneralUtility;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Node;

/**
 * Deployment
 *
 * @package Lightwerk\SurfRunner
 */
class Deployment extends \TYPO3\Surf\Domain\Model\Deployment {

	/**
	 * @return string The release identifier
	 */
	public function getReleaseIdentifier() {
		return 'htdocs';
	}

	/**
	 * @param Application $application
	 * @return string
	 */
	public function getWorkspacePath(Application $application) {
		$workspacePath = FLOW_PATH_DATA . 'Surf/';
		if ($application->hasOption('repositoryUrl')) {
			$urlParts = GeneralUtility::getUrlPartsFromRepositoryUrl($application->getOption('repositoryUrl'));
			$workspacePath .= preg_replace(
				'/[^a-zA-Z0-9]/',
				'-',
				$urlParts['path']
			);
			$workspacePath .= '_' . substr(sha1($application->getOption('repositoryUrl')), 0, 5);
		} else {
			// Default
			$workspacePath .= $this->getName() . '/' . $application->getName();
		}
		return $workspacePath;
	}

	/**
	 * @return Node|NULL
	 */
	public function getFirstNode() {
		$nodes = $this->getNodes();
		foreach ($nodes as $node) {
			return $node;
		}
		return NULL;
	}
}