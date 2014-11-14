<?php

namespace Stems\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
	protected $home = 'stems_admin_core_developer_overview';

	/**
	 * The admin toolbar
	 * @todo - ordering
	 * @todo - menu objects from menu bundle
	 */
	public function barAction()
	{
		// Load the active Stems and bespoke bundles
		$bundles = $this->get('stems.core.bundles.management')->getActiveBundles();

		// Load the core menu items for each bundle
		$core = array();

		foreach ($bundles as $name => $bundle) {
			if ($this->container->hasParameter('stems.admin.menu_core_'.$name)) {
				$items = $this->container->getParameter('stems.admin.menu_core_'.$name);
				$core = array_merge($core, $items); 
			}
		}

		// Load the bespoke menu items
		$bespoke = array();

		foreach ($bundles as $name => $bundle) {
			if ($this->container->hasParameter('stems.admin.menu_bespoke_'.$name)) {
				$items = $this->container->getParameter('stems.admin.menu_bespoke_'.$name);
				$bespoke = array_merge($bespoke, $items); 
			}
		}

		// Load the config menu items
		$config = array();

		foreach ($bundles as $name => $bundle) {
			if ($this->container->hasParameter('stems.admin.menu_config_'.$name)) {
				$items = $this->container->getParameter('stems.admin.menu_config_'.$name);
				$config = array_merge($config, $items);
			}
		}

		return $this->render('StemsCoreBundle:Admin:bar.html.twig', array(
			'core'    => $core,
			'bespoke' => $bespoke,
			'config'  => $config,
		));
	}

	/**
	 * Compile the dashboard view using the dialogues for each module
	 */
	public function dashboardAction()
	{
		// Load the active Stems and bespoke bundles
		$bundles = $this->get('stems.core.bundles.management')->getActiveBundles();

		return $this->render('StemsCoreBundle:Admin:dashboard.html.twig', array(
			'bundles'	=> $bundles,
		));
	}

	/**
	 * The developer dashboard
	 */
	public function developerDashboardAction()
	{
		// Load the active Stems and bespoke bundles
		$bundles = $this->get('stems.core.bundles.management')->getActiveBundles();

		return $this->render('StemsCoreBundle:Admin:developerDashboard.html.twig', array(
			'bundles'	=> $bundles,
		));
	}

	/**
	 * Compile individual sitemap entries for each module and build the sitemap file
	 */
	public function sitemapAction(Request $request)
	{
		// Load the active Stems and bespoke bundles
		$bundles = $this->get('stems.core.bundles.management')->getActiveBundles();

		// render the xml sitemap
		$xml = $this->renderView('StemsCoreBundle:Admin:sitemap.html.twig', array(
			'bundles'	=> $bundles,
		));

		// create the xml file
		$filename = 'sitemap.xml';
		$filepath = $this->get('kernel')->getRootDir().'/../web/sitemaps/'.$filename;
		$handle = fopen($filepath, 'w+');
		fwrite($handle, ''.$xml.'');
		fclose($handle);

		$request->getSession()->getFlashBag()->set('success', 'The sitemap has been updated.');
		return $this->redirect($this->generateUrl($this->home));
	}
}

