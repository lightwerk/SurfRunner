<?php
namespace Lightwerk\SurfRunner\Domain\Model\Application;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * SlaveDeployApplication
 *
 * @package Lightwerk\SurfRunner
 */
class SlaveDeployApplication extends AbstractApplication
{
    /**
     * 1. initialize: Initialize directories etc. (first time deploy)
     * 2. transfer: Transfer of application assets to the node
     * 3. finalize: Prepare final release (e.g. warmup)
     *
     * @var array
     */
    protected $tasks = [
        'initialize' => [
            'lightwerk.surftasks:transfer:assureconnection',
        ],
        'transfer' => [
            'lightwerk.surftasks:transfer:rsync',
        ],
        'finalize' => [
            'lightwerk.surftasks:deploymentlog',
        ]
    ];
}
