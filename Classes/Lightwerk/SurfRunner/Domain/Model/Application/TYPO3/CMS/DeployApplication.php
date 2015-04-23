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
class DeployApplication extends AbstractApplication {

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
			'typo3.surf:package:git',
			'typo3.surf:composer:install',
			'lightwerk.surftasks:git:clean',
			'lightwerk.surftasks:assets:gulp',
		),
		'transfer' => array(
			'lightwerk.surftasks:git:stoponchanges',
			'lightwerk.surftasks:lockfile:create',
			'lightwerk.surftasks:transfer:rsync',
		),
		// 'update' => array(),
		'migrate' => array(
			'lightwerk.surftasks:clearphpcache',
			'lightwerk.surftasks:typo3:cms:clearcache',
			'lightwerk.surftasks:typo3:cms:createuploadfolders',
			'lightwerk.surftasks:typo3:cms:updatedatabase',
		),
		'finalize' => array(
			'lightwerk.surftasks:lockfile:remove',
			'lightwerk.surftasks:deploymentlog',
			'lightwerk.surftasks:git:removedeploybranch',
			'lightwerk.surftasks:git:tagnodedeployment',
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
		'lightwerk.surftasks:git:clean' => array(
			'options' => array(
				'nodeName' => 'localhost',
			),
		),
		'lightwerk.surftasks:git:tagnodedeployment' => array(
			'options' => array(
				'nodeName' => 'localhost',
			),
		),
		'lightwerk.surftasks:assets:gulp' => array(
			'options' => array(
				'nodeName' => 'localhost',
			),
		),
		'lightwerk.surftasks:transfer:rsync' => array(
			'options' => array(
				'rsyncFlags' => array(
					'exclude' => array('typo3temp/*'),
					'include' => array('typo3temp/.gitdummy'),
				)
			),
		),
		'lightwerk.surftasks:deploymentlog' => array(
			'options' => array(
				'deploymentLogTargetPath' => '..',
			),
		),
	);
}
