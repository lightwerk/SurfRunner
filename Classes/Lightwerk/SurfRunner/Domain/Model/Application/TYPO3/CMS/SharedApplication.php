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
class SharedApplication extends AbstractApplication {

	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = 'TYPO3 CMS Shared') {
		parent::__construct($name);
	}

}