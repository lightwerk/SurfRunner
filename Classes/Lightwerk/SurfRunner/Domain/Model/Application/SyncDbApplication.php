<?php
namespace Lightwerk\SurfRunner\Domain\Model\Application;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * @package Lightwerk\SurfRunner
 * @author Achim Fritz <af@achimfritz.de>
 */
class SyncDbApplication extends AbstractApplication {

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
			'lightwerk.surftasks:database:transfer',
		),
		'migrate' => array(
			'lightwerk.surftasks:database:import',
		),
		'cleanup' => array(
			'lightwerk.surftasks:database:cleanup',
		),
	);

}
