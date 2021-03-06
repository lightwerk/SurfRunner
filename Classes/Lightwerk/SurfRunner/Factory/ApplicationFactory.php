<?php
namespace Lightwerk\SurfRunner\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Domain\Model\Application\AbstractApplication;
use TYPO3\Flow\Annotations as Flow;
use Lightwerk\SurfTasks\Factory\NodeFactory;
use TYPO3\Surf\Exception\InvalidConfigurationException;


/**
 * Application Factory
 *
 * @Flow\Scope("singleton")
 * @package Lightwerk\SurfRunner
 */
class ApplicationFactory
{

    /**
     * @Flow\Inject
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * @param array $configurations
     * @return AbstractApplication[]
     * @throws Exception
     */
    public function getApplicationsByConfiguration(array $configurations)
    {
        $applications = [];

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
                try {
                    $node = $this->nodeFactory->getNodeByArray($nodeConfiguration);
                    $application->addNode($node);
                } catch (InvalidConfigurationException $e) {
                    throw new Exception('cannot create node from configuration', 1437474964);
                }
            }

            $applications[] = $application;
        }
        return $applications;
    }
}
