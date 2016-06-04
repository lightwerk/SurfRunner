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
class DeployApplication extends AbstractApplication {

	/**
	 * @var array
	 */
	protected $tasks = array(
		'initialize' => array(
			'lightwerk.surftasks:transfer:assureconnection',
		),
		'package' => array(
			'typo3.surf:package:git',
			'typo3.surf:composer:install',
			'typo3.surf:composer:dumpAutoload',
			'lightwerk.surftasks:git:clean',
			'lightwerk.surftasks:assets:gulp',
		),
		'transfer' => array(
			'lightwerk.surftasks:git:stoponchanges',
			'lightwerk.surftasks:transfer:rsync',
		),

		'migrate' => array(
			'lightwerk.surftasks:typo3:flow:migrate',
			'lightwerk.surftasks:typo3:flow:flushcache',
			'lightwerk.surftasks:typo3:flow:warmupcache',
		),
		'finalize' => array(
			'lightwerk.surftasks:deploymentlog',
			'lightwerk.surftasks:git:removedeploybranch',
			'lightwerk.surftasks:git:tagnodedeployment',
		),
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
		'typo3.surf:composer:dumpAutoload' => array(
			'options' => array(
				'nodeName' => 'localhost',
				'composerCommandPath' => 'composer',
				'composerArguments' => '--optimize',
			),
		),
		'lightwerk.surftasks:transfer:rsync' => array(
			'options' => array(
				'rsyncFlags' => array(
					'include' => array('Data/Persistence'),
					'exclude' => array('Configuration/PackageStates.php', 'Data/*')
				)
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
		'lightwerk.surftasks:deploymentlog' => array(
			'options' => array(
				'deploymentLogTargetPath' => '..',
			),
		),
	);
}
