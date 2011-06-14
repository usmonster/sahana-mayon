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
  protected $_searchedModels = array('agOrganization');

  public function executeSearch(sfWebRequest $request)
  {
    parent::doSearch($request->getParameter('query'));
    $this->target_module = 'organization';
    $this->setTemplate(sfConfig::get('sf_app_dir') . DIRECTORY_SEPARATOR . 'modules/search/templates/search');
  }

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
    // Query organizations and their staff counts.
    $query = agDoctrineQuery::create()
    ->select('o.*, count(distinct sr.staff_id) AS staffCount')
    ->from('agOrganization as o')
    ->leftJoin('o.agStaffResource sr')
    ->groupBy('o.organization');

     /**
     * Create pager
     */
    $this->pager = new sfDoctrinePager('agOrganization', 10);

    /**
     * Check if the client wants the results sorted, and set pager
     * query attributes accordingly
     */
    $this->sortColumn = $request->getParameter('sort') ? $request->getParameter('sort') : 'Organization';
    $this->sortOrder = $request->getParameter('order') ? $request->getParameter('order') : 'ASC';

    if ($request->getParameter('sort')) {
      $query = $query->orderBy($request->getParameter('sort', 'Organization') . ' ' . $request->getParameter('order', 'ASC'));
    }

    /**
     * Set pager's query to our final query including sort
     * parameters
     */
    $this->pager->setQuery($query);

    /**
     * Set the pager's page number, defaulting to page 1
     */
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
