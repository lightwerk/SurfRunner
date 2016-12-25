<?php
namespace Lightwerk\SurfRunner\Domain\Model\Application\TYPO3\CMS;

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
class CopyApplication extends DeployApplication
{

    /**
     * @var array
     */
    protected $tasks = [
        'initialize' => [
            'lightwerk.surftasks:transfer:assureconnection',
        ],
        'package' => [
            'lightwerk.surftasks:database:dump',
            'typo3.surf:package:git',
            'typo3.surf:composer:install',
            'lightwerk.surftasks:git:clean',
            'lightwerk.surftasks:assets:gulp',
        ],
        'transfer' => [
            'lightwerk.surftasks:git:stoponchanges',
            'lightwerk.surftasks:typo3:cms:syncshared',
            'lightwerk.surftasks:database:transfer',
            'lightwerk.surftasks:lockfile:create',
            'lightwerk.surftasks:transfer:rsync',
        ],
        'migrate' => [
            'lightwerk.surftasks:database:import',
            'lightwerk.surftasks:clearphpcache',
            'lightwerk.surftasks:typo3:cms:clearcache',
            'lightwerk.surftasks:typo3:cms:createuploadfolders',
            'lightwerk.surftasks:typo3:cms:updatedatabase',
        ],
        'cleanup' => [
            'lightwerk.surftasks:database:cleanup',
        ],
        'finalize' => [
            'lightwerk.surftasks:lockfile:remove',
            'lightwerk.surftasks:deploymentlog',
            'lightwerk.surftasks:git:removedeploybranch',
            'lightwerk.surftasks:git:tagnodedeployment',
        ],
    ];
}
