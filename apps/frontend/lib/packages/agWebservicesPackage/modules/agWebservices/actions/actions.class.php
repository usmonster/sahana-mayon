<?php

/**
 * webservices actions.
 *
 * @package    AGASTI_CORE
 * @subpackage webservices
 * @author     Fabio Albuquerque <fabiocbalbuquerque@gmail.com>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agWebservicesActions extends agActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

  /**
   * List action - web interface which lists data from available datapoints.
   * Sets the template to render the listSuccess.php which decides
   * the subpage to be correctly rendered
   * @param sfWebRequest $request
   */
  public function executeList(sfWebRequest $request)
  {
    $this->type = $request->getParameter('datapoint');
    $method = 'get'.ucfirst($this->type);
    $this->results = agWebservicesHelper::$method();
  }

	/**
   * Get action - Grabs data from database if a valid token is passed.
   * It uses a parameter to specify which datapoint is 
   * going to be returned.
   * Route pattern: /webservices/get/:token/:datapoint.:format
   * - token: allows or blocks a request (checks valid web service client) 
   * - datapoint: which datapoint will be reached
   * - format - dictates whether a json or a xml will be created 
   * 
   * @param sfWebRequest $request
   */  
  public function executeGet(sfWebRequest $request)
  {
    $this->getRoute()->getObjects();
    $this->type = $request->getParameter('datapoint');
    $method = 'get'.ucfirst($this->type);
    $this->results = agWebservicesHelper::$method();
  }
  
  
    public function executeGetevent(sfWebRequest $request)
  {
    $this->getRoute()->getObjects();
    $this->type = $request->getParameter('datapoint');
    $method = 'get'.ucfirst($this->type);
    $this->results = agWebservicesHelper::$method($request->getParameter('event'));
  }

}