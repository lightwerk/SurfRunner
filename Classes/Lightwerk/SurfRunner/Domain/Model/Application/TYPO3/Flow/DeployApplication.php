<?php
namespace Lightwerk\SurfRunner\Domain\Model\Application\TYPO3\Flow;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Domain\Model\Application\AbstractApplication;
use TYPO3\Flow\Annotations as Flow;

/**
 * Deploy Application
 *
 * @package Lightwerk\SurfRunner
 */
class DeployApplication extends AbstractApplication
{
    /**
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
            'lightwerk.surftasks:assets:gulp',
        ],
        'transfer' => [
            'lightwerk.surftasks:git:stoponchanges',
            'lightwerk.surftasks:transfer:rsync',
        ],

        'migrate' => [
            'lightwerk.surftasks:typo3:flow:migrate',
            'lightwerk.surftasks:typo3:flow:flushcache',
            'lightwerk.surftasks:typo3:flow:warmupcache',
        ],
        'finalize' => [
            'lightwerk.surftasks:deploymentlog',
            'lightwerk.surftasks:git:removedeploybranch',
            'lightwerk.surftasks:git:tagnodedeployment',
        ],
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
        'lightwerk.surftasks:transfer:rsync' => [
            'options' => [
                'rsyncFlags' => [
                    'include' => ['Data/Persistence'],
                    'exclude' => ['Configuration/PackageStates.php', 'Data/*']
                ]
            ],
        ],
        'lightwerk.surftasks:git:clean' => [
            'options' => [
                'nodeName' => 'localhost',
            ],
        ],
        'lightwerk.surftasks:git:tagnodedeployment' => [
            'options' => [
                'nodeName' => 'localhost',
            ],
        ],
        'lightwerk.surftasks:assets:gulp' => [
            'options' => [
                'nodeName' => 'localhost',
            ],
        ],
        'lightwerk.surftasks:deploymentlog' => [
            'options' => [
                'deploymentLogTargetPath' => '..',
            ],
        ],
    ];
}
