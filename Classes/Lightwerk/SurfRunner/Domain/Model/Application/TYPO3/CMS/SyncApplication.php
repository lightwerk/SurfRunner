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
class SyncApplication extends AbstractApplication {

	/**
	 * @var array
	 */
	protected $tasks = array(
		'initialize' => array(
			'lightwerk.surftasks:transfer:assureconnection',
		),
		'package' => array(
			'lightwerk.surftasks:database:dump',
		),
		'transfer' => array(
			'lightwerk.surftasks:typo3:cms:syncshared',
			'lightwerk.surftasks:database:transfer',
		),
		'migrate' => array(
			'lightwerk.surftasks:database:import',
		),
		'cleanup' => array(
			'lightwerk.surftasks:database:cleanup',
		),
		'finalize' => array(
			'lightwerk.surftasks:deploymentlog',
		),
	);

}
