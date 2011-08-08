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
  //$this->forward('default', 'module');
    $this->organization_name = agGlobal::getParam('organization_name');
    $this->vesuvius_address = agGlobal::getParam('vesuvius_address');
  }
  public function executeError(sfWebRequest $request)
  {
    $this->error_code = $this->response;
  }
   public function executePrepare(sfWebRequest $request)
  {
  //$this->forward('default', 'module');
  }
  public function executeRespond(sfWebRequest $request)
  {
  //$this->forward('default', 'module');
  $this->ag_events = agDoctrineQuery::create()
 ->select('e.id, e.event_name, e.zero_hour, est.event_status_type')
 ->addSelect('es.id, est.id')
 ->from('agEvent e')
 ->innerJoin('e.agEventStatus es')
 ->innerJoin('es.agEventStatusType est')
 ->where('est.active = ?', TRUE)
 ->andWhere('EXISTS (SELECT ses.id
                        FROM agEventStatus ses
                        WHERE ses.event_id = es.event_id
                        HAVING MAX(ses.time_stamp) = es.time_stamp)')
->execute();
//print_r($this->ag_events);


  $this->scenarioForm = new sfForm();
    $this->scenarioForm->setWidgets(
        array(
          'ag_scenario_list' => new sfWidgetFormDoctrineChoice(
              array('multiple' => false, 'model' => 'agScenario')
          )
        )
    );


    $this->scenarioForm->getWidgetSchema()->setLabel('ag_scenario_list', false);
//    $this->ag_events = agDoctrineQuery::create()
//        ->select('a.*')
//        ->from('agEvent a')
//        ->execute();


  }

}
