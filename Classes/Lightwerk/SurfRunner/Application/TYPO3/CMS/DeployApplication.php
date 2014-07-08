<?php
namespace Lightwerk\SurfRunner\Application\TYPO3\CMS;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Application\AbstractApplication;
use TYPO3\Flow\Annotations as Flow;

class DeployApplication extends AbstractApplication {

	/**
	 * @var array
	 */
	protected $tasks = array(
//		'initialize' => array(),	// Initialize directories etc. (first time deploy)
		'package' => array(			// Local preparation of and packaging of application assets
			'typo3.surf:package:git',
			'lightwerk.surftasks:package:composerinstall',
		),
		'transfer' => array(		// Transfer of application assets to the node
			'typo3.surf:transfer:rsync',
		),
//		'update' => array(),		// Update the application assets on the node
		'migrate' => array(			// Migrate (Doctrine, custom)
			'lightwerk.surftasks:typo3:cms:createuploadfolders',
			'lightwerk.surftasks:typo3:cms:clearcache',
			'lightwerk.surftasks:typo3:cms:updatedb',
		),
		'finalize' => array(		// Prepare final release (e.g. warmup)
			'lightwerk.surftasks:deploymentlog',
			'lightwerk.surftasks:git:removebranch',
		),
//		'test' => array(),			// Smoke test
//		'switch' => array(),		// Do symlink to current release
//		'cleanup' => array(),		// Delete temporary files or previous releases
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
	);
}