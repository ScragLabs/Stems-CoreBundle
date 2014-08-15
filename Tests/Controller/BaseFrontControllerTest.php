<?php

namespace StemsCoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * This controller test case doesn't test the BaseFrontController itself, but provides generic testing methods to be inherited by all stems FrontController tests
 *
 * @package    StemsCoreBundle
 * @author     Stephen Wilkinson
 * @version    1.0
 */
class BaseFrontControllerTest extends WebTestCase
{
	protected $client;

	public function __construct() 
	{
        // always load a client as this controller is for frontend pages
		$this->client = static::createClient();
	}

    /**
     * Test the page is loadable and whether the Page object loaded has the expected slug
     *
     * @param  string   $uri    The request uri to test the page with
     * @param  string   $slug   The slug of the page object we expect to be loaded (can be left empty for generic pages)
     */
    public function assertCmsLoadable($uri, $slug='')
    {
    	// load the page
        $crawler = $this->client->request('GET', $uri);

        // check any content loaded
        $this->assertTrue($crawler->filter('.cms-page')->count() > 0);

        // check the slug of the page
        $this->assertTrue($crawler->filter('body')->attr('data-cms-slug') == $slug);
    }
}
