<?php

/** 
* home actions.
*
* PHP Version 5.3
*
* LICENSE: This source file is subject to LGPLv2.1 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/licenses/lgpl-2.1.html
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
<<<<<<< TREE
=======
  
>>>>>>> MERGE-SOURCE
    $this->error_code = $this->response;
  }
   public function executePrepare(sfWebRequest $request)
  {
//    $this->forward('default', 'module');

  }

}
