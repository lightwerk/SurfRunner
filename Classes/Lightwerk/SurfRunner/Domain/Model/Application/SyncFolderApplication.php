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
class SyncFolderApplication extends AbstractApplication {

	/**
	 * @var array
	 */
	protected $tasks = array(
		'initialize' => array(
			'lightwerk.surftasks:transfer:assureconnection',
		),
		'transfer' => array(
			'lightwerk.surftasks:typo3:cms:syncshared',
		),
	);

}
