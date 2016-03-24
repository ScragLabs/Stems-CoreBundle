<?php

namespace Stems\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the base front controller for all Stems-compatible modules. Primarily for auto-loading functionality to integrate with CMS controlled paging.
 */
class BaseFrontController extends Controller
{
	/**
	 * A container for the page entity loaded from the CMS.
	 * The event handler will attempt a best guess at loading the page from the CMS, but will create a default page entity as a fallback.
	 */
	public $page;
}
