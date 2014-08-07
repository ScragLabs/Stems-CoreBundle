<?php

namespace Stems\CoreBundle\Twig;

use Doctrine\ORM\EntityManager;

class CoreExtension extends \Twig_Extension
{
	/**
	 * The entity manager
	 */
	protected $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('username', array($this, 'usernameFilter')),
		);
	}

	public function getName()
	{
		return 'stems_core_extension';
	}

	/**
	 * Converts the given user id into it's associated name format, defaulting to the fullname
	 *
	 * @param  integer  $id         The user id
	 * @param  string   $format     The name format to use, either forname, surname, fullname or username
	 * @return string               The request name
	 */
	public function usernameFilter($id, $format='fullname')
	{
		// get the user entity
		$user = $this->em->getRepository('StemsUserBundle:User')->find($id);

		// skip if we can't find the user
		if (!is_object($user)) {
			return $id;
		}

		// build the get method and call it if it exists
		$method = 'get'.ucfirst($format);

		if (method_exists($user, $method)) {
			return $user->$method();
		} else {
			return $id;
		}
	}	
}