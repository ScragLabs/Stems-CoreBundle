<?

namespace Stems\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpKernel\Event\FilterControllerEvent,
    Doctrine\ORM\EntityManager;

use Stems\CoreBundle\Controller\BaseFrontController,
    Stems\PageBundle\Entity\Page,
    Stems\PageBundle\Entity\Layout;

class FrontListener
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

        // the controller should come in as an array, so get it out first
        if (is_array($controller)) {
            $controller = $controller[0];
        } else {
            return;
        }

        // only run if the controller loaded is an extension of the Stems base front controller
        if ($controller instanceof BaseFrontController) {

            // inject the entity manager into the controller
            $controller->em = $this->em;

            // get the path so we can attempt to predict the page that's needed
            $path = ltrim($event->getRequest()->getPathInfo(), '/');

            // attempt to load the page using an absolute path match first
            try
            {
                $controller->page = $this->em->getRepository('StemsPageBundle:Page')->load($path);
            }
            // if the absolute path doesn't work due to dynamic slug components attempt estimate the page
            catch (\Exception $e) 
            {
                $controller->page = $this->em->getRepository('StemsPageBundle:Page')->estimate($path);

                // if all else fails it probably doesn't exist, so create a generic page object that uses a default content layout
                if (!is_object($controller->page)) {
                    $controller->page = new Page();
                    $layout = new Layout();
                    $layout->setSlug('content');
                    $controller->page->setLayout($layout);
                }
            }
        }
    }
}