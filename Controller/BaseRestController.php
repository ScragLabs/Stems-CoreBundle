<?php

namespace Stems\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Form;

/**
 * This is the base rest controller for all Stems-compatible modules. It's main function is to provide tidy and homogenised JSON responses.
 */
class BaseRestController extends Controller
{
	/**
	 * Collects data ready for when the JSON response is compiles, with sensible defaults
	 */
	protected $data = array(
		'status'     => 'success',
		'flash'	     => false,
		'meta' 	 	 => array(),
		'validation' => array(),
	);

	/**
	 * Sets the response status to success and includes and optional message
	 *
	 * @param  string 	$message 	Message to attach to the response 
	 * @param  boolean	$flash 		Whether the response needs to be handle as a flash message
	 * @return self
	 */
	public function success($message=null, $flash=false)
	{
		$this->data['status']  = 'success';
		$this->data['message'] = $message;
		$this->data['flash']   = $flash;

		return $this;
	}

	/**
	 * Sets the response status to error and includes and optional message
	 *
	 * @param  string 	$message 	Message to attach to the response 
	 * @param  boolean	$flash 		Whether the response needs to be handle as a flash message
	 * @return self
	 */
	public function error($message=null, $flash=false)
	{
		$this->data['status']  = 'error';
		$this->data['message'] = $message;
		$this->data['flash']   = $flash;

		return $this;
	}

	/**
	 * Add html to the response data
	 *
	 * @param  string 	$html 	The html to attach
	 * @return self
	 */
	public function addHtml($html='')
	{
		$this->data['html'] = $html;

		return $this;
	}

	/**
	 * Add meta data to the response data
	 *
	 * @param  array 	$meta 	The meta data to attach in and indexed array format
	 * @return self
	 */
	public function addMeta($meta=array())
	{
		// merge in any exisiting meta data to the new stuff
		$this->data['meta'] = array_merge($this->data['meta'], $meta);

		return $this;
	}

	/**
	 * Set the name of the callback to be handled by the response
	 *
	 * @param  string 	$callback 	The name of the callback
	 * @return self
	 */
	public function setCallback($callback=null)
	{
		$this->data['callback'] = $callback;

		return $this;
	}

	/**
	 * Instantiates a JSON Response object using our data and returns it
	 *
	 * @return JsonResponse		The compiled response object.
	 */
	public function sendResponse()
	{
		return new JsonResponse($this->data);
	}

	/**
	 * Takes a Form object and appends the errors to the response
	 *
	 * @param  Form 	$form 	The form to take the errors from
	 * @return self
	 */
	public function addValidationErrors(Form $form) {

		// Start the error list if it doesn't exist
	    if (!array_key_exists('errors', $this->data['validation'])) {
	    	$this->data['validation']['errors'] = array();
	    }

	    // Loop through the form children and the form itself to get all errors
	    if ($form->hasChildren()) {
	        foreach ($form->getChildren() as $child) {
	            if (!$child->isValid()) {
	                $this->data['validation']['errors'][$child->getName()] = $this->getErrorMessages($child);
	            }
	        }
	    } else {
	        foreach ($form->getErrors() as $key => $error) {
	            $this->data['validation']['errors'][] = $error->getMessage();
	        }   
	    }

	    return $this;
	}
}
