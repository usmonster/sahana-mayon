<?php

/**
 * Organization Actions extends sfActions
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class organizationActions extends agActions
{
  protected $agOrganizationHelper ;
  protected $_search = 'organization';


//  public function executeSearch(sfWebRequest $request)
//  {
//    $this->targetAction = 'search';
//    $string = $request->getParameter('query');
//    $pattern = "/\W/";
//    $replace = " ";
//    $this->params = '?query=' . urlencode(trim(preg_replace($pattern, $replace, $string), '+'));
////    $this->params = '?query=' . $request->getParameter('query');
//    $currentPage = ($request->hasParameter('page')) ? $request->getParameter('page') : 1;
//    parent::doSearch($request->getParameter('query'), $currentPage);
//    $this->setTemplate(sfConfig::get('sf_app_dir') . DIRECTORY_SEPARATOR . 'modules/search/templates/search');
//  }

  /**
   * Method to lazily load the agOrganizationHelper helper class.
   *
   * @return agOrganizationHelper an instance of agOrganizationHelper
   */
  protected function getOrganizationHelper()
  {
    // lazily load the sucker if she's not yet set
    if (! isset($this->agOrganizationHelper))
    {
      $this->agOrganizationHelper = agOrganizationHelper::init() ;
    }

    // return the recently instantiated (or pre-existing) object
    return $this->agOrganizationHelper ;
  }

  /**
   * executeIndex() is an empty method, a placeholder for indexing.
   *
   * @param $request sfWebRequest object for current page request
   */
  public function executeIndex(sfWebRequest $request)
  {
    //Empty placeholder.
  }

  /**
   * executeList() displays the list of organizations and the total distinct staff counts.
   *
   * @param sfWebRequest $request
   */
  public function executeList(sfWebRequest $request)
  {
    // we use the get parameters to manage most of this action's methods
    $this->listParams = $request->getGetParameters();

    // here are the post params we're looking for
    if ($request->getPostParameter('query')) {

      // if found, we trigger our redirect and add it to our listParams
      $param = str_replace('*', '%', strtolower(trim($request->getPostParameter('query'))));

      // merge the results together
      $this->listParams = array_merge($this->listParams, array($postParam => $param));

      // if a post was found we redirect and add everything via http_build_query
      $this->redirect(($this->moduleName . '/' . $this->actionName . '?' .
        http_build_query($this->listParams)));
    }

    // query organizations and their staff counts.
    $query = agDoctrineQuery::create()
    ->select('o.*, count(distinct sr.staff_id) AS staffCount')
    ->from('agOrganization as o')
    ->leftJoin('o.agStaffResource sr')
    ->groupBy('o.organization');

    // attach the wildcard query criterion
    if (isset($this->listParams['query'])) {
      $query->where('o.organization LIKE ?', array('%' . $this->listParams['query'] . '%'));
    }

    // establish our sort parameter mapping
    $sorts = array('organization' => 'o.organization',
        'description' => 'o.description',
        'staffCount' => 'count(distinct sr.staff_id)',
        );

    // map the variable to the sort param
    if (isset($this->listParams['sort']) && isset($sorts[$this->listParams['sort']]) &&
        isset($this->listParams['order'])) {
      $this->sortColumn = $this->listParams['sort'];
      $this->sortOrder = $this->listParams['order'];
    } else {
      $this->sortColumn = 'organization';
      $this->sortOrder = 'ASC';
    }

    // attach it to the query
    $query->orderBy($sorts[$this->sortColumn] . ' ' . $this->sortOrder);


    //Defines the columns of the organization display list page.
    $this->columnHeaders = array(
      'id' => array('title' => 'Id', 'sortable' => false),
      'organization' => array('title' => 'Organization','tooltip' => 'organization_name', 'sortable' => true),
      'description' => array('title' => 'Description', 'tooltip' => 'organization_description', 'sortable' => true),
      'staffCount' => array('title' => 'Staff Resources', 'tooltip' => 'organization_staff_count', 'sortable' => TRUE),
    );

    // set up our query and datasource
    $resultsPerPage = agGlobal::getParam('staff_list_results_per_page');
    $this->pager = new sfDoctrinePager('agOrganization', $resultsPerPage);
    $this->pager->setQuery($query);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();

  }

  /**
   * executeShow() displays detailed information on individual organization record.
   *
   * @param sfWebRequest $request
   */
  public function executeShow(sfWebRequest $request)
  {
    $this->orgId = $request->getParameter('id') ;

    $this->ag_organization = Doctrine_Core::getTable('agOrganization')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->ag_organization);

    // Get staff resource type in an array.
     $this->staffResourceTypes = agStaffResourceType::staffResourceTypeInArray();

    // Get the organization's unique staff count by staff resource type.
    $this->uniqueStaffCount = agStaff::getUniqueStaffCount(2, array($this->ag_organization->getId()));

    // Get the organization's total unique staff count.
    $totalStaffCountByOrg = agStaff::getUniqueStaffCount(3, array($this->ag_organization->getId()));
    $this->totalStaffCount = $totalStaffCountByOrg[$this->ag_organization->getId()];

     //p-code
      $this->getResponse()->setTitle('Sahana Agasti Organization - ' . $this->ag_organization->getOrganization());
     //end p-code
  }

  /**
   * exectueNew() calls a form page for new organization creation.
   *
   * @param sfWebRequest $request
   */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agOrganizationForm();
  }

  /**
   * executeCreate() creates new organization record.
   * Since organization is a subset of entity, it is required to create a
   * new entity in order to create a new organization.
   *
   * @param sfWebRequest $request
   */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agOrganizationForm();


    $entity = new agEntity();
    $foo = $entity->id;

    $entity->save();
    $this->form->getObject()->setAgEntity($entity);

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  /**
   * executeEdit() calls a form page for individual organization record editting.
   *
   * @param sfWebRequest $request
   */
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_organization = Doctrine_Core::getTable('agOrganization')->find(array($request->getParameter('id'))), sprintf('Object ag_organization does not exist (%s).', $request->getParameter('id')));
    $this->form = new agOrganizationForm($ag_organization);
    //p-code
    $this->getResponse()->setTitle('Sahana Agasti Organization Edit');
    //end p-code
  }

  /**
   * executeUpdate() updates an individual organization record.
   *
   * @param sfWebRequest $request
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_organization = Doctrine_Core::getTable('agOrganization')->find(array($request->getParameter('id'))), sprintf('Object ag_organization does not exist (%s).', $request->getParameter('id')));
    $this->form = new agOrganizationForm($ag_organization);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  /**
   * executeDelete() deletes individual organization record.
   *
   * @param sfWebRequest $request
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_organization = Doctrine_Core::getTable('agOrganization')->find(array($request->getParameter('id'))), sprintf('Object ag_organization does not exist (%s).', $request->getParameter('id')));

    // Reassign associating staff resource to default organization.
    $orgHelper = new agOrganizationHelper();
    $defaultOrganizationId = $orgHelper->getDefaultOrganizationId();
    
    $query = agDoctrineQuery::create()
             ->update('agStaffResource')
             ->set('organization_id', $defaultOrganizationId)
             ->where('organization_id = ?', $ag_organization->id);
    $updateCount = $query->execute();

    if ($agEntity = $ag_organization->getAgEntity()) {
      $agEntity->delete();
    } else {
      /**
       * If there was no related agEntity record found, something is
       * wrong.
       */
      throw new LogicException('agOrganization is expected to have an agEntity.');
    }

    $this->redirect('organization/list');
  }

  /**
   * processForm is a required method generated by Symfony/Doctrine to process organization related forms.
   *
   * @param sfWebRequest $request
   * @param sfForm $form
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $ag_organization = $form->save();

      if ($request->hasParameter("Another"))
      {
        $this->redirect('organization/new');
      }
      else
      {
        $this->redirect('organization/edit?id='.$ag_organization->getId());
      }
    }
  }
}
