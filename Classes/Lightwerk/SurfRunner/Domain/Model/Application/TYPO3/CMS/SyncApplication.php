<?php
namespace Lightwerk\SurfRunner\Domain\Model\Application\TYPO3\CMS;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Domain\Model\Application\AbstractApplication;
use TYPO3\Flow\Annotations as Flow;

/**
 * Shared Application
 *
 * @package Lightwerk\SurfRunner
 */
class SyncApplication extends AbstractApplication
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
        ],
        'transfer' => [
            'lightwerk.surftasks:typo3:cms:syncshared',
            'lightwerk.surftasks:database:transfer',
        ],
        'migrate' => [
            'lightwerk.surftasks:database:import',
        ],
        'cleanup' => [
            'lightwerk.surftasks:database:cleanup',
        ],
        'finalize' => [
            'lightwerk.surftasks:deploymentlog',
        ],
    ];
}
