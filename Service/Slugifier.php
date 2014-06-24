<?php

namespace Stems\CoreBundle\Service;

class Slugifier
{
	/**
	 * Generate the slug for the passed string
	 */
	public function slugify($string)
	{
		// replace non letter or digits by -
		$slug = preg_replace('~[^\\pL\d]+~u', '-', $string);

		// trim
		$slug = trim($slug, '-');

		// transliterate
		$slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);

		// lowercase
		$slug = strtolower($slug);

		// remove unwanted characters
		$slug = preg_replace('~[^-\w]+~', '', $slug);

		return $slug;
	}
}
