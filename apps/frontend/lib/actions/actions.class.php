<?php

/**
 * extends sfActions for common functionality
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agActions extends sfActions
{

  protected $_search;

  public function __construct($context, $moduleName, $actionName)
  {
    parent::__construct($context, $moduleName, $actionName);
    if (!isset($this->_search)) {
      $this->_search = 'staff';
    }
  }

  public function executeSearch(sfWebRequest $request)
  {
    // build our url
    $url = $this->_search . '/list';
    if ($request->getPostParameter('query')) {
      $url .= '?' . http_build_query(array_merge($request->getGetParameters(),
        array('query' => strtolower(trim($request->getPostParameter('query'))),)));
    }
    $this->redirect($url);
  }

  public function executeStatus(sfWebRequest $request)
  {
    ////TODO: module_ACTION_status instead? -UA
    //$statusId = implode('_', array($this->moduleName, 'status'));
    //$context = $this->getContext();
    ////$context = sfContext::getInstance();
    //$status = $context->has($statusId) ? $this->getContext()->get($statusId) : $statusId/*array(0, 0, 0)*/;

    //TODO: get import data directory root info from global param
    $importDataRoot = sfConfig::get('sf_upload_dir');
    $importDir = $importDataRoot . DIRECTORY_SEPARATOR . $this->moduleName;
    $statusFile = $importDir . DIRECTORY_SEPARATOR . 'status.yml';
    $status = is_readable($statusFile) ? sfYaml::load($statusFile) : array()/* array(0, 0, 0) */;
    $format = $request->getRequestFormat();
    if ('json' == $format) {
      $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
      $status = json_encode($status);
    }
    //TODO: else, use partial instead of returning?
    //TODO: the else block below is for testing -- remove when finished
    else {
      $this->getResponse()->setHttpHeader('Content-Type', 'text/plain; charset=utf-8');
      $status = json_encode($status);
    }

    return $this->renderText($status);
  }

}
