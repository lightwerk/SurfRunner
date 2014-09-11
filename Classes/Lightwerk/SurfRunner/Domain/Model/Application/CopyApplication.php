<?php
namespace Lightwerk\SurfRunner\Domain\Model\Application;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Copy Application
 *
 * @package Lightwerk\SurfRunner
 */
class CopyApplication extends AbstractApplication {

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
			'lightwerk.surftasks:ssh:opentunnel',
			'lightwerk.surftasks:transfer:assureconnection',
		),
		'package' => array(
			'typo3.surf:package:git',
			'typo3.surf:composer:install',
		),
		'transfer' => array(
			'lightwerk.surftasks:transfer:rsync',
		),
		// 'update' => array(),
		// 'migrate' => array(),
		'finalize' => array(
			'lightwerk.surftasks:deploymentlog',
			'lightwerk.surftasks:git:removedeploybranch',
			'lightwerk.surftasks:git:tagnodedeployment',
			'lightwerk.surftasks:ssh:closetunnel',
		),
		// 'test' => array(),
		// 'switch' => array(),
		// 'cleanup' => array(),
	);

	/**
	 * @var array
	 */
	protected $taskOptions = array(
		'typo3.surf:package:git' => array(
			'options' => array(
				'fetchAllTags' => TRUE,
			),
		),
		'typo3.surf:composer:install' => array(
			'options' => array(
				'nodeName' => 'localhost',
				'composerCommandPath' => 'composer',
			),
		),
		'lightwerk.surftasks:git:tagnodedeployment' => array(
			'options' => array(
				'nodeName' => 'localhost',
			),
		),
	);
}