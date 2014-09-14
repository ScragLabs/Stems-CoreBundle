<?php

namespace Stems\CoreBundle\Twig;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CoreExtension extends \Twig_Extension
{
	/**
	 * The service container
	 */
	protected $container;

	/**
	 * The entity manager
	 */
	protected $em;

	public function __construct(ContainerInterface $container, EntityManager $em)
	{
		$this->container = $container;
		$this->em 		 = $em;
	}

	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('username', array($this, 'usernameFilter')),
			new \Twig_SimpleFilter('parameter', array($this, 'parameterFilter')),
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
		// Get the user entity
		$user = $this->em->getRepository('StemsUserBundle:User')->find($id);

		// Skip if we can't find the user
		if (!is_object($user)) {
			return $id;
		}

		// Build the get method and call it if it exists
		$method = 'get'.ucfirst($format);

		if (method_exists($user, $method)) {
			return $user->$method();
		} else {
			return $id;
		}
	}

	/**
	 * Turns a parameter string into it's relevant value, saves injecting loads of globals into twig
	 *
	 * @param  string   $format     The name of the required parameter
	 * @return mixed                The value of the parameter
	 */
	public function parameterFilter($parameter)
	{
		// Attempt to get the parameter value
		$value = $this->container->getParameter($parameter, null);

		return $value;
	}
}