<?php

namespace Stems\CoreBundle\Definition;

interface SectionInstanceInterface
{
	/**
	 * Renders the front end html of the section
	 */
	public function render($services, $link);

	/**
	 * Renders the admin form for the section
	 */
	public function editor($services, $link);

	/**
	 * Handles the posted edit data for the section
	 */
	public function save($services, $parameters, $request, $link);
}