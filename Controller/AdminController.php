<?php

namespace Stems\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
	protected $developerHome = 'stems_admin_core_developer_overview';

	/**
	 * Filter out the active bundles relevant to Stems and Thread & Mirror
	 */
	protected function getActiveBundles()
	{
		// get all loaded bundles
		$bundles = $this->container->getParameter('kernel.bundles');

		// remove the core bundle
		unset($bundles['StemsCoreBundle']);

		// filter the relevent Stems and ThreadAndMirror bundles
		foreach ($bundles as $key => $bundle) {
			if (!stristr($bundle, 'Stems') && !stristr($bundle, 'ThreadAndMirror')) {
				unset($bundles[$key]);
			}
		}

		return $bundles;
	}

	/**
	 * The admin toolbar
	 */
	public function barAction()
	{
		return $this->render('StemsCoreBundle:Admin:bar.html.twig');
	}

	/**
	 * Compile the dashboard view using the dialogues for each module
	 */
	public function dashboardAction()
	{
		// get all loaded bundles
		$bundles = $this->getActiveBundles();

		return $this->render('StemsCoreBundle:Admin:dashboard.html.twig', array(
			'bundles'	=> $bundles,
		));
	}

	/**
	 * The developer dashboard
	 */
	public function developerDashboardAction()
	{
		// get all loaded bundles
		$bundles = $this->getActiveBundles();

		return $this->render('StemsCoreBundle:Admin:developerDashboard.html.twig', array(
			'bundles'	=> $bundles,
		));
	}

	/**
	 * Compile individual sitemap entries for each module and build the sitemap file
	 */
	public function sitemapAction(Request $request)
	{
		// get all loaded bundles
		$bundles = $this->getActiveBundles();

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

		$request->getSession()->setFlash('success', 'The sitemap has been updated.');
		return $this->redirect($this->generateUrl($this->developerHome));
	}
}

