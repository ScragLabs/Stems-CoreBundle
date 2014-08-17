<?php

namespace StemsCoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * This controller test case doesn't test the BaseFrontController itself, but provides generic testing methods to be inherited by all stems FrontController tests
 *
 * @package    Stems - CoreBundle
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
     * @return Crawler          The crawler object for the page request
     */
    public function assertCmsLoadable($uri, $slug=null)
    {
    	// load the page
        $crawler = $this->client->request('GET', $uri);

        // check any content loaded
        $this->assertTrue($crawler->filter('.cms-page')->count() > 0);

        // @todo (crawler isn't returning any attr) - check the slug of the page, which also checks if the template html has loaded
        //$this->assertTrue($crawler->filter('body')->attr('data-cms-slug') == $slug);

        return $crawler;
    }

    /**
     * Because php unit doesn't like a test case with no tests, we can easily override this in child FrontControllerTests
     */
    public function testIndex() 
    {
        $this->assertTrue(true);
    }
}
