<?php

namespace Stems\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the base admin controller for all stems modules and provides fallback methods and handy cross-module functionally
 */
class BaseAdminController extends Controller
{
	/**
	 * Render the dialogue for the module's dashboard entry in the admin panel, defaulting to nothing if you don't want it in the dash.
	 */
	public function dashboardAction()
	{
		return new Response('');
	}

	/**
	 * Build the sitemap entries for the bundle, defaulting to nothing.
	 */
	public function sitemapAction()
	{
		return new Response('');
	}
}
