<?php

/**
 * Organization Actions extends sfActions
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class organizationActions extends sfActions
{
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
    // Store organizations and their staff counts in an array where
    // $this->staffCountByOrg = array( organization id => staff count by organization ).
//    $this->organizations = agOrganization::organizationInArray();
//    $organizations = $this->organizations;
//    $this->staffCountByOrg = array();
//    foreach($organizations as $orgId => $orgName)
//    {
//      $this->staffCountByOrg[$orgId] = agStaff::staffCount(false, null, true, $orgId);
//    }
    $this->staffCountByOrg = agStaff::getUniqueStaffCount(3);

    $query = Doctrine_Query::create()
    ->select('o.*')
    ->from('agOrganization as o');

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
    $this->ag_organization = Doctrine_Core::getTable('agOrganization')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->ag_organization);

    // Get staff resource type in an array.
     $this->staffResourceTypes = agStaffResourceType::staffResourceTypeInArray();
//     $staffResourceTypesObj = Doctrine_Core::getTable('agStaffResourceType')->findAll();
//     $this->staffResourceTypes = array();
//     foreach ($staffResourceTypesObj as $stfResType)
//     {
//       $this->staffResourceTypes[$stfResType->getId()] = $stfResType->getStaffResourceType();
//     }

    // Get the organization's unique staff count by staff resource type.
    $this->uniqueStaffCount = agStaff::getUniqueStaffCount(2, array($this->ag_organization->getId()));

    // Get the organization's total unique staff count.
    $totalStaffCountByOrg = agStaff::getUniqueStaffCount(3, array($this->ag_organization->getId()));
    $this->totalStaffCount = $totalStaffCountByOrg[$this->ag_organization->getId()];

    // Get the staffs and their staff resource type associated to the organization.
    $this->organizationStaffResources = agOrganization::organizationStaffByResource(array($this->ag_organization->getId()));
//    print_r($this->organizationStaffResources);
    $this->personPrimaryNames = agPerson::getPersonPrimaryNames();
//    print_r($this->personPrimaryNames);
    $this->personFullName = agPerson::getPersonFullName();
    
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

    // Delete associating staff resource.
    $ag_organization->getAgStaffResourceOrganization()->delete();

    $ag_organization->delete();

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

      $this->redirect('organization/edit?id='.$ag_organization->getId());
    }
  }
}