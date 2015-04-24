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