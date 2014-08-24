<?php
namespace Lightwerk\SurfRunner\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfCaptain\Utility\GeneralUtility;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Flow\Annotations as Flow;

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
			$workspacePath .= preg_replace(
				'/[^a-zA-Z0-9]/',
				'-',
				GeneralUtility::getUrlPartsFromRepositoryUrl($application->getOption('repositoryUrl'))['path']
			);
			$workspacePath .= '_' . substr(sha1($application->getOption('repositoryUrl')), 0, 5);
		} else {
			// Default
			$workspacePath .= $this->getName() . '/' . $application->getName();
		}
		return $workspacePath;
	}
}