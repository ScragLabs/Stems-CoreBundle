<?php

namespace Stems\CoreBundle\Service;

// Universal pagination function that pulls "get" paramters to perform pagination, but can also be overridden with custom values by passing an array through as the 3rd parameter
//
// Example Usage:
//
// * With Custom Paramters *
// $data = $this->get(Stems.core.pagination)->paginate($entities, $request, array('maxPerPage' => 5));
//

class Pagination
{
	protected $records = array();

	protected $currentPage = 1;

	protected $maxPerPage;

	protected $maxPages = 1;

	protected $offset = 0;
	/**
	 * Take a page of records from an entire collection
	 */
	public function paginate($records, $request, $custom=array())
	{
		// get the maxPerPage value from the cookie if it exists
		$cookie = $request->cookies->get('maxPerPage');

		if (isset($cookie)) {
			 $custom['maxPerPage'] = $request->cookies->get('maxPerPage');
		}

		// get the current page from the request if a custom page number isn't passed
		if (isset($custom['currentPage'])) {
			$this->currentPage = ($request->get('currentPage') && is_numeric($request->get('currentPage')) && $request->get('currentPage') >= 1) ? $request->get('currentPage') : $custom['currentPage'];
		} else {
			$this->currentPage = ($request->get('currentPage') && is_numeric($request->get('currentPage')) && $request->get('currentPage') >= 1) ? $request->get('currentPage') : 1;
		}

		// check if a custom value is set for the max per page and use that as a default if nothing is set in the request
		if (isset($custom['maxPerPage'])) {
			$this->maxPerPage = ($request->get('maxPerPage') && is_numeric($request->get('maxPerPage')) && ($request->get('maxPerPage') >= 1)) ? $request->get('maxPerPage') : $custom['maxPerPage'];
		} else {
			$this->maxPerPage = ($request->get('maxPerPage') && is_numeric($request->get('maxPerPage')) && ($request->get('maxPerPage') >= 1)) ? $request->get('maxPerPage') : 10;
		}

		// break the records into chunks and get the relevant chunk for the current page
		if (count($records) > 0) {
			$pages = array_chunk($records, $this->maxPerPage);

			$this->maxPages = count($pages);
			if ($this->maxPages == 0) $this->maxPages = 1;

			if (isset($pages[$this->currentPage-1])) {
				$this->records = $pages[$this->currentPage-1];
			} else {
				$this->records = $pages[0];
				$this->currentPage = 1;
			}
		}
		return $this;
	}

	/**
	 * Calculate offset and limit data for use in a query
	 */
	public function offset($records, $request, $custom=array())
	{
		$this->records = $records;

		// get the maxPerPage value from the cookie if it exists
		$cookie = $request->cookies->get('maxPerPage');

		if (isset($cookie)) {
			 $custom['maxPerPage'] = $request->cookies->get('maxPerPage');
		}

		// get the current page from the request if a custom page number isn't passed
		if (isset($custom['currentPage'])) {
			$this->currentPage = ($request->get('currentPage') && is_numeric($request->get('currentPage')) && $request->get('currentPage') >= 1) ? $request->get('currentPage') : $custom['currentPage'];
		} else {
			$this->currentPage = ($request->get('currentPage') && is_numeric($request->get('currentPage')) && $request->get('currentPage') >= 1) ? $request->get('currentPage') : 1;
		}

		// check if a custom value is set for the max per page and use that as a default if nothing is set in the request
		if (isset($custom['maxPerPage'])) {
			$this->maxPerPage = ($request->get('maxPerPage') && is_numeric($request->get('maxPerPage')) && ($request->get('maxPerPage') >= 1)) ? $request->get('maxPerPage') : $custom['maxPerPage'];
		} else {
			$this->maxPerPage = ($request->get('maxPerPage') && is_numeric($request->get('maxPerPage')) && ($request->get('maxPerPage') >= 1)) ? $request->get('maxPerPage') : 10;
		}

		// use the amount of records to calcuate the rest of the parameters
		if ($records > 0) {

			// get the total amount of pages
			$this->maxPages = ($records % $this->maxPerPage) + 1;

			// fix any erroneous page numbers
			$this->maxPages < 1 and $this->maxPages = 1;

			// calculate the offset
			$this->offset = ($this->page - 1) * $this->maxPerPage;
		}
		
		return $this;
	}

	// SESSION VIEW SORTING //////////////////////////////////////////////////////////////////////////////////////////////////////

	// Universal sorting function that pulls "get" parameters to perform sort based on the session fields that are requested. Also allows for default values to be set.
	//
	// Example Usage:
	//
	// * The sessions prefix will be included in view clickies (eg. table headers in the back end) and prevents overwriting from multiple stored sortng sessions for different tables. *
	// <a href="?vehicles-sortby=name">Name</a>
	//
	// * Handle the sorting in the controller before pulling the entities and doing any pagination. *    
	// $sorting['valid_fields'] = array('name', 'id', 'price', 'mileage', 'year');
	// $sorting['default_field'] = 'id';
	// $sorting['default_direction'] = 'ASC';
	// $sorting['session_prefix'] = 'vehicles';
	//
	// $sorting =  $this->get(Stems.core.pagination)->sorting($sorting, $request);
	// $vehicles = $em->getRepository('ExampleBundle:Entity')->findBy(array('deleted' => false), array($sorting->getField() => $sorting->getDirection()));

	public function sorting($sorting, $request)
	{
		// set default search terms to allow universal functionality, otherwise use any defaults passed through
		if(!isset($sorting['default_field'])) {
				 $sorting['field'] = 'id';
		} else {
				 $sorting['field'] = $sorting['default_field'];
		}
		if(!isset($sorting['default_direction'])) {
				 $sorting['direction'] = 'ASC';
		} else {
				 $sorting['direction'] = $sorting['default_direction'];
		}

		// check the sessions for pre-saved search terms
		if($request->getSession()->get($sorting['session_prefix'].'-sortdirection')) {
				 $sorting['direction'] = $request->getSession()->get($sorting['session_prefix'].'-sortdirection');
		}
		if(!$request->getSession()->get($sorting['session_prefix'].'-sortdirection')) {
				 $request->getSession()->set($sorting['session_prefix'].'-sortdirection', $sorting['direction']);
		}
		if($request->getSession()->get($sorting['session_prefix'].'-sortby')) {
				 $sorting['field'] = $request->getSession()->get($sorting['session_prefix'].'-sortby');
		}

		// determine whether to use stored terms or any new requested values
		if($request->query->get($sorting['session_prefix'].'-sortby') && in_array($request->query->get($sorting['session_prefix'].'-sortby'), $sorting['valid_fields'])) {
				$sorting['field_requested'] = $request->query->get($sorting['session_prefix'].'-sortby');
				$request->getSession()->set($sorting['session_prefix'].'-sortby', $sorting['field_requested']);

				// handles sorting - automitcally toggle  direction unless a value is forced
				if($request->query->get($sorting['session_prefix'].'-sortdirection')) {
					$sorting['direction'] = $request->query->get($sorting['session_prefix'].'-sortdirection');
				} else {
					if($sorting['field_requested'] == $sorting['field']) {
						if($sorting['direction'] == 'ASC') {
							$sorting['direction'] = 'DESC';
								$request->getSession()->set($sorting['session_prefix'].'-sortdirection', $sorting['direction']);
							} else {
								$sorting['direction'] = 'ASC';
								$request->getSession()->set($sorting['session_prefix'].'-sortdirection', $sorting['direction']);
							}
						}
					}
					$sorting['field'] = $sorting['field_requested'];
				} elseif($request->getSession()->get($sorting['session_prefix'].'-sortby')) {
					$sorting['field'] = $request->getSession()->get($sorting['session_prefix'].'-sortby');
				}

		return $sorting;
	}

	public function getRecords()
	{
		return $this->records;
	}

	public function getCurrentPage()
	{
		return $this->currentPage;
	}

	public function getMaxPerPage()
	{
		return $this->maxPerPage;
	}

	public function getMaxPages()
	{
		return $this->maxPages;
	}
}