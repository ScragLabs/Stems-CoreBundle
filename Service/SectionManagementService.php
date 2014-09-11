<?php

namespace Stems\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Monolog\Logger;

/**
 *	Handles the rendering, processing and other functionality of sections, commonly used in pages and blog posts
 */
class SectionManagementService
{
	/**
	 * The entity manager
	 */
	protected $em;

	/**
	 * The twig renderer
	 */
	protected $twig;

	/**
	 * The form builder
	 */
	protected $formFactory;

	/**
	 * Type configurations for individual bundles that use sections
	 */
	protected $types;

	/**
	 * The current bundle type to handle sections for
	 */
	protected $bundle = 'page';

	/**
	 * Any errors generated by the save methods
	 */
	protected $saveErrors = array();

	public function __construct(EntityManager $em, TwigEngine $twig, FormFactoryInterface $formFactory, $types)
	{
		// Load the dependency services
		$this->em          = $em;
		$this->twig        = $twig;
		$this->formFactory = $formFactory;

		// Load the configured types
		$this->types = $types;
	}

	/**
	 * Sets the bundle type we're handling the sections for
	 *
	 * @param  string  	$bundle 	The config key string for the bundle type to use
	 * @return this					For method chaining
	 */
	public function setBundle($bundle)
	{
		$this->bundle = $bundle;

		return $this;
	}

	/**
	 * Returns an array of admin edit form renders for the provided collection of section link entities
	 *
	 * @param  array  	$links 		The section links we need to generate forms for
	 * @return array 				An array of html strings for each rendered form
	 */
	public function getEditors($links=array())
	{
		$forms = array();

		// Ready the logger
		$logger = new Logger('main');

		// Get the section forms
		foreach ($links as $link) {

			// Find the specific section data and render the form view
			$section = $this->em->getRepository($this->types[$this->bundle][$link->getType()->getId()]['entity'])->find($link->getEntity());

			// Render the form view and store the html
			if ($section) {
				$forms[] = $section->editor($this, $link);	
			} else {
				$logger->error('The requested section (Entity ID: '.$link->getEntity().' - Section Type: '.$this->types[$this->bundle][$link->getType()->getId()]['entity'].') does not exist.');
			}
		}

		return $forms;
	}

	/**
	 * Builds and returns the specific form object for the requested section
	 *
	 * @param  array  	$link 		The link entity for the section
	 * @param  mixed 	$section 	The specific instance of the section type (eg. TextSection)
	 * @return mixed 				The specific instance of the section's form object
	 */
	public function createSectionForm($link, $section)
	{
		// Build the class name using the section type then create the form object
        $formClass = $this->types[$this->bundle][$link->getType()->getId()]['form'];
        $form      = $this->formFactory->create(new $formClass($link), $section);

        return $form;
	}

	/**
	 * Builds and returns the specific form object for the requested sub-section
	 *
	 * @param  mixed 	$section 	The specific instance of the sub-section type (eg. ImageGalleryImage)
	 * @return mixed 				The specific instance of the sub-section's form object
	 */
	public function createSubSectionForm($section)
	{
		// Build the class name using the section type then create the form object
		$formClass = str_replace('\\Entity\\', '\\Form\\', get_class($section)).'Type';
        $form 	   = $this->formFactory->create(new $formClass($section), $section);

        return $form;
	}

	/**
	 * Dynamic handler for posted form data when saving post sections
	 */
	public function saveSection($link, $parameters, $request)
	{
		try
		{
			// The specific section data and run the save
			$section = $this->em->getRepository($this->types[$this->bundle][$link->getType()->getId()]['entity'])->find($link->getEntity());
			$section->save($this, $parameters, $request, $link);
		}
		catch(\Exception $e)
        {
            // Add an error message if the was a problem saving the section data
            $this->saveErrors[] = 'There was a problem saving section ID '.$link->getID().': '.$e->getMessage();
        }
	}

	/**
	 * Renders the front end html for a section
	 */
	public function renderSection($link)
	{
		// Get the specific section instance data and run the renderer
		$section = $this->em->getRepository($this->types[$this->bundle][$link->getType()->getId()]['entity'])->find($link->getEntity());
		
		return $section->render($this, $link);
	}

	/**
	 * Get the entity manager
	 */
	public function getManager()
	{
		return $this->em;
	}

	/**
	 * Get twig
	 */
	public function getTwig()
	{
		return $this->twig;
	}

	/**
	 * Get any save errors
	 */
	public function getSaveErrors()
	{
		return $this->saveErrors;
	}
}
