<?php

namespace Stems\CoreBundle\Service;

// curl in a webpage to use for scrape/deconstructing with the DOM crawler
//
// eg:
// $html = $this->get(Stems.core.scrape)->getHtml('www.yourlinkhere.com');

class Scrape
{
	// set variable to hold curl instance
	protected $crl;

	// this is where we dump the html we get
	protected $html = '';

	// set for binary type transfer
	protected $binary = 0;

	// this is the url we are going to do a pass on
	protected $url = '';

	// takes url passed to it and.. can you guess?
	public function getHtml($url)
	{
		// set the URL to scrape
		$this->url = $url;

		if (isset($this->url)) {

			// start cURL instance
			$this->ch = curl_init ();

			// this tells cUrl to return the data
			curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, 1);

			// set the url to download
			curl_setopt ($this->ch, CURLOPT_URL, $this->url);

			// follow redirects if any
			curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);

			// tell cURL if the data is binary data or not
			curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, $this->binary);

			// grabs the webpage from the internets
			$this->html = curl_exec($this->ch);

			// closes the connection
			curl_close ($this->ch);
		}

		return $this->html;
	}

	// function takes html, puts the data requested into an array
	public function parse_array($beg_tag, $close_tag)
	{
		// match data between specificed tags
		preg_match_all("($beg_tag.*$close_tag)siU", $this->html, $matching_data);

		// return data in array
		return $matching_data[0];
	}
}
