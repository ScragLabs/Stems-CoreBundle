<?php

namespace Stems\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\ORM\EntityManager;
use Stems\CoreBundle\Controller\BaseFrontController;
use Stems\CoreBundle\Controller\BaseAdminController;
use Stems\PageBundle\Exception\PageNotFoundException;
use Stems\PageBundle\Entity\Page;
use Stems\PageBundle\Entity\Layout;

class BaseListener
{
	/**
	 * To hold the injected entity manager
	 */
	protected $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function onKernelController(FilterControllerEvent $event)
	{
		$controller = $event->getController();

		// The controller should come in as an array, so get it out first
		if (is_array($controller)) {
			$controller = $controller[0];
		} else {
			return;
		}

		// Trigger for all front controllers
		if ($controller instanceof BaseFrontController) {

			// Inject the entity manager into the controller
			$controller->em = $this->em;

			// Get the path so we can attempt to predict the page that's needed
			$path = ltrim($event->getRequest()->getPathInfo(), '/');

			// Attempt to load the page using an absolute path match first
			try
			{
				$controller->page = $this->em->getRepository('StemsPageBundle:Page')->load($path);
			}
			// If the absolute path doesn't work due to dynamic slug components attempt estimate the page
			catch (PageNotFoundException $e) 
			{
				$controller->page = $this->em->getRepository('StemsPageBundle:Page')->estimate($path);

				// If all else fails it probably doesn't exist, so create a generic page object that uses a default content layout
				if (!is_object($controller->page)) {
					$controller->page = new Page();
					$layout = new Layout();
					$layout->setSlug('content');
					$controller->page->setLayout($layout);
				}
			}
		}

        // Trigger for all admin controllers
        if ($controller instanceof BaseAdminController) {

            // Inject the entity manager into the controller
            $controller->em = $this->em;
        }
	}
}