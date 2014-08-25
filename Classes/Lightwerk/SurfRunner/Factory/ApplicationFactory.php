<?php
namespace Lightwerk\SurfRunner\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Domain\Model\Application\AbstractApplication;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Surf\Domain\Model\Node;

/**
 * Application Factory
 *
 * @Flow\Scope("singleton")
 * @package Lightwerk\SurfRunner
 */
class ApplicationFactory {

	/**
	 * @param array $configurations
	 * @return AbstractApplication[]
	 * @throws Exception
	 */
	public function getApplicationsByConfiguration(array $configurations) {
		$applications = array();

		$iteration = 0;
		foreach ($configurations as $configuration) {
			$iteration++;

			if (empty($configuration['type'])) {
				throw new Exception('No type is given in application configuration', 1408396056);
			}
			$applicationClass = '\\Lightwerk\\SurfRunner\\Domain\\Model\\Application\\' . $configuration['type'] . 'Application';
			if (!class_exists($applicationClass)) {
				break;
			}
			// Do not change the Application name!
			// @see \Lightwerk\SurfRunner\Application\AbstractApplication
			// ->getTaskNameForApplication()
			/** @var AbstractApplication $application */
			$application = new $applicationClass('#' . $iteration . ' ' . $configuration['type']);

			if (!empty($configuration['options']) && is_array($configuration['options'])) {
				$application->addOptions($configuration['options']);
			}
			if (!empty($configuration['tasks']) && is_array($configuration['tasks'])) {
				$application->addTasks($configuration['tasks']);
			}
			if (!empty($configuration['taskOptions']) && is_array($configuration['taskOptions'])) {
				$application->addTaskOptions($configuration['taskOptions']);
			}

			if (empty($configuration['nodes']) || !is_array($configuration['nodes'])) {
				throw new Exception('No nodes are given in application configuration', 1408396220);
			}
			foreach ($configuration['nodes'] as $nodeConfiguration) {
				$application->addNode($this->getNodeByConfiguration($nodeConfiguration));
			}

			$applications[] = $application;
		}
		return $applications;
	}

	/**
	 * @param array $configuration
	 * @return Node
	 * @throws Exception
	 */
	protected function getNodeByConfiguration(array $configuration) {
		if (empty($configuration['name'])) {
			throw new Exception('Name is not given for node', 1408396327);
		}
		if (empty($configuration['hostname'])) {
			throw new Exception('Hostname is not given for node', 1408396400);
		}
		$node = new Node($configuration['name']);
		foreach ($configuration as $key => $value) {
			if ($key === 'name') {
				continue;
			}
			$method = 'set' . ucfirst($key);
			if (method_exists($node, $method)) {
				$node->$method($value);
			} else {
				$node->setOption($key, $value);
			}
		}
		return $node;
	}
}