<?php
namespace Lightwerk\SurfRunner\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Application\AbstractApplication;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Node;

/**
 * @Flow\Scope("singleton")
 */
class ApplicationFactory {

	/**
	 * @param array $configuration
	 * @return Application[]
	 */
	public function getApplicationsByConfiguration(array $applicationsConfiguration) {
		$applications = array();

		$solvedApplicationConfigurations = array();
		foreach ($applicationsConfiguration as $applicationConfiguration) {
			if (empty($applicationConfiguration['abstract'])) {
				$solvedApplicationConfigurations[] = $this->getApplicationConfigurations(
					$applicationConfiguration,
					$applicationsConfiguration
				);
			}
		}

		$iteration = 0;
		foreach ($solvedApplicationConfigurations as $solvedApplicationConfiguration) {
			$iteration++;
			$applicationClass = '\\Lightwerk\\SurfRunner\\Application\\' . $solvedApplicationConfiguration['type'] . 'Application';
			if (!class_exists($applicationClass)) {
				break;
			}
			// Do not change the Application name!
			// @see \Lightwerk\SurfRunner\Application\AbstractApplication->getTaskNameForApplication()
			/** @var AbstractApplication $application */
			$application = new $applicationClass('#' . $iteration . ' ' . $solvedApplicationConfiguration['type']);

			if (!empty($solvedApplicationConfiguration['tasks']) && is_array($solvedApplicationConfiguration['tasks'])) {
				$application->addTasks($solvedApplicationConfiguration['tasks']);
			}
			if (!empty($solvedApplicationConfiguration['taskOptions']) && is_array($solvedApplicationConfiguration['taskOptions'])) {
				$application->addStagesAndTasks($solvedApplicationConfiguration['taskOptions']);
			}

			if (!empty($solvedApplicationConfiguration['nodes']) && is_array($solvedApplicationConfiguration['nodes'])) {
				foreach ($solvedApplicationConfiguration['nodes'] as $nodeConfiguration) {
					$application->addNode($this->getNodeByConfiguration($nodeConfiguration));
				}
			}

			$applications[] = $application;
		}
		return $applications;
	}

	/**
	 * @param array $configuration
	 * @return Node
	 */
	protected function getNodeByConfiguration(array $configuration) {
		$node = new Node($configuration['name']);
		foreach ($configuration as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($node, $method)) {
				$node->$method($value);
			} else {
				$node->setOption($key, $value);
			}
		}
		return $node;
	}

	/**
	 * @param array $configuration
	 * @param array $fullConfiguration
	 * @return array
	 */
	protected function getApplicationConfigurations(array $configuration, array $fullConfiguration) {
		if (!empty($configuration['superTypes']) && is_array($configuration['superTypes'])) {
			foreach ($configuration['superTypes'] as $superType) {
				if (!empty($fullConfiguration[$superType]) && is_array($fullConfiguration[$superType])) {
					$tempConfiguration = $this->getApplicationConfigurations($fullConfiguration[$superType], $fullConfiguration);
					if (!empty($configuration['nodes']) && !empty($configuration['nodes'])) {
						unset($tempConfiguration['nodes']);
					}
					$configuration = array_merge_recursive($configuration, $tempConfiguration);
				}
			}
		}
		return $configuration;
	}
}