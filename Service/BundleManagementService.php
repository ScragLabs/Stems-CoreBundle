<?php

namespace Stems\CoreBundle\Service;

/**
 * Service for managing the active stems bundles and getting/setting related config data
 *
 * @package    Stems - CoreBundle
 * @author     Stephen Wilkinson
 * @version    1.0
 */
class BundleManagementService
{
	/**
	 * All bundles registered in the kernel
	 */
	protected $bundles;

	/**
	 * Constructor
	 *
	 * @param  array 	$bundles 	%kernel.bundles% parameter  
	 */
	public function __construct($bundles) 
	{
		$this->bundles = $bundles;
	}	

	/**
	 * A list of all active Stems bundles, ignoring Core but any bespoke bundles as defined in the config
	 *
	 * @return array  		A list of bundles
	 */
	public function getActiveBundles()
	{
		// Get all loaded bundles
		$bundles = $this->bundles;

		// Remove the core bundle
		unset($bundles['StemsCoreBundle']);

		/**
		 * @todo - handle config control bespoke bundles, like thread and mirror
		 */

		// Filter the relevent Stems bundles
		foreach ($bundles as $key => $bundle) {
			if (!stristr($bundle, 'Stems')) {
				unset($bundles[$key]);
			}
		}

		return $bundles;
	}
}
