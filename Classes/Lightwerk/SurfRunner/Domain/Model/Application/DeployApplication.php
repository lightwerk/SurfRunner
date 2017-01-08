<?php
namespace Lightwerk\SurfRunner\Domain\Model\Application;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Deploy Application
 *
 * @package Lightwerk\SurfRunner
 */
class DeployApplication extends AbstractApplication
{
    /**
     * 1. initialize: Initialize directories etc. (first time deploy)
     * 2. package: Local preparation of and packaging of application assets
     * 3. transfer: Transfer of application assets to the node
     * 4. update: Update the application assets on the node
     * 5. migrate: Migrate (Doctrine, custom)
     * 6. finalize: Prepare final release (e.g. warmup)
     * 7. test: Smoke test
     * 8. switch: Do symlink to current release
     * 9. cleanup: Delete temporary files or previous releases
     *
     * @var array
     */
    protected $tasks = [
        'initialize' => [
            'lightwerk.surftasks:transfer:assureconnection',
        ],
        'package' => [
            'typo3.surf:package:git',
            'typo3.surf:composer:install',
            'lightwerk.surftasks:git:clean',
            'lightwerk.surftasks:assets:npm',
            'lightwerk.surftasks:assets:bower',
            'lightwerk.surftasks:assets:grunt',
            'lightwerk.surftasks:assets:gulp',
        ],
        'transfer' => [
            'lightwerk.surftasks:transfer:rsync',
        ],
        // 'update' => array(),
        // 'migrate' => array(),
        'finalize' => [
            'lightwerk.surftasks:deploymentlog',
            'lightwerk.surftasks:git:removedeploybranch',
            'lightwerk.surftasks:git:tagnodedeployment',
        ],
        // 'test' => array(),
        // 'switch' => array(),
        // 'cleanup' => array(),
    ];

    /**
     * @var array
     */
    protected $taskOptions = [
        'typo3.surf:package:git' => [
            'options' => [
                'fetchAllTags' => true,
            ],
        ],
        'typo3.surf:composer:install' => [
            'options' => [
                'nodeName' => 'localhost',
                'composerCommandPath' => 'composer',
            ],
        ],
        'lightwerk.surftasks:git:tagnodedeployment' => [
            'options' => [
                'nodeName' => 'localhost',
            ],
        ],
        'lightwerk.surftasks:assets:npm' => [
            'options' => [
                'nodeName' => 'localhost',
                'useApplicationWorkspace' => true
            ],
        ],
        'lightwerk.surftasks:assets:bower' => [
            'options' => [
                'nodeName' => 'localhost',
                'useApplicationWorkspace' => true
            ],
        ],
        'lightwerk.surftasks:assets:grunt' => [
            'options' => [
                'nodeName' => 'localhost',
                'useApplicationWorkspace' => true
            ],
        ],
        'lightwerk.surftasks:assets:gulp' => [
            'options' => [
                'nodeName' => 'localhost',
                'useApplicationWorkspace' => true
            ],
        ],
        'lightwerk.surftasks:deploymentlog' => [
            'options' => [
                'deploymentLogTargetPath' => '..',
            ],
        ],
        'lightwerk.surftasks:git:clean' => [
            'options' => [
                'nodeName' => 'localhost',
            ],
        ],
    ];
}
