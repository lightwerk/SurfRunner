<?php
namespace Lightwerk\SurfRunner\Application\TYPO3\CMS;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Lightwerk.SurfRunner".  *
 *                                                                        *
 *                                                                        */

use Lightwerk\SurfRunner\Application\AbstractApplication;
use TYPO3\Flow\Annotations as Flow;

class SharedApplication extends AbstractApplication {

	/**
	 * @var array
	 */
	protected $options = array(
		'useApplicationWorkspace' => TRUE,
	);

	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = 'TYPO3 CMS Shared') {
		parent::__construct($name);
	}

}