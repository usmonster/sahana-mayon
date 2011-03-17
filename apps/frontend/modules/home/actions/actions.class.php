<?php

/** 
* home actions.
*
* PHP Version 5
*
* LICENSE: This source file is subject to LGPLv3.0 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Full Name, Organization
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/

/**
* @todo add description of class in header
*/

class homeActions extends agActions
{

  protected $searchedModels = array('agStaff');

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
#    $this->forward('default', 'module');
  }
  public function executeError(sfWebRequest $request)
  {
  $this->math = 2+2;
    $this->error_code = $this->response;
  }
   public function executePrepare(sfWebRequest $request)
  {
//    $this->forward('default', 'module');
  }

}
