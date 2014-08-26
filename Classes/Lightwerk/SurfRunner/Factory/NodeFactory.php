<?php
namespace Lightwerk\SurfRunner\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Surf\Domain\Model\Node;

/**
 * Node Factory
 *
 * @Flow\Scope("singleton")
 * @package Lightwerk\SurfRunner
 */
class NodeFactory {

	/**
	 * @param array $configuration
	 * @return Node
	 * @throws Exception
	 */
	public function getNodeByArray(array $configuration) {
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