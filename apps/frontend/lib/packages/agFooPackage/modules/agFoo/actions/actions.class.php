<?php

/**
 * foo actions.
 *
 * @package    AGASTI_CORE
 * @subpackage foo
 * @author     CUNY SPS
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agFooActions extends agActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $this->ag_foos = Doctrine_Core::getTable('agFoo')
            ->createQuery('a')
            ->execute();
  }

  public function executeList(sfWebRequest $request)
  {
    /**
     * Query the database for agFacility records joined with
     * agFacilityResource records
     */
    $query = agDoctrineQuery::create()
            ->select('f.*')
            ->from('agFoo f');

    /**
     * Create pager
     */
    $this->pager = new sfDoctrinePager('agFoo', 5);

    /**
     * Check if the client wants the results sorted, and set pager
     * query attributes accordingly
     */
    $this->sortColumn = $request->getParameter('sort') ? $request->getParameter('sort') : 'foo';
    $this->sortOrder = $request->getParameter('order') ? $request->getParameter('order') : 'ASC';

    if ($request->getParameter('sort')) {
      $query = $query->orderBy($request->getParameter('sort', 'foo') . ' ' . $request->getParameter('order', 'ASC'));
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

    $this->setTemplate(sfConfig::get('sf_app_template_dir') . DIRECTORY_SEPARATOR . 'listFoo');
  }

  public function executeShow(sfWebRequest $request)
  {
// <-------- CUT HERE -------->
$array = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14) ;
$fakeAddr = array(array(array('1'=>'monkeybanana3', '2'=>'raffle1', '5'=>'10031'), 1) ,
    array(array('1'=>'peachpitters', '5'=>'10035'), 1)) ;
$obj = agAddressHelper::init() ;
//$obj = agPersonNameHelper::init(4) ;
//$obj = agEntityAddressHelper::init() ;
//$obj->setAgAddressHelper() ;
//$obj->agAddressHelper->lineDelimiter = '<br />' ;
//$results = $obj->getEntityAddress($array, TRUE, FALSE, agAddressHelper::ADDR_GET_STRING) ;
//$results = $obj->defaultNameComponents ;
//$results = $obj->getPersonIds() ;
//$results = $obj->getPrimaryNameById() ;
//$results = $obj->getPrimaryNameByType() ;
//$results = $obj->getPrimaryNameAsString() ;
//$results = $obj->getAddressAsString() ;
//$results = $obj->getAddressStandardId() ;
//$results = agPersonNameHelper::init()->getPrimaryNameAsString(array(4)) ;
//$results = $obj->getAddressAllowedElements() ;
//$obj = Doctrine_Core::getTable('agPerson')->find(1) ;
//$results = $obj->updateAddressHashes($array) ;
//$addr = $obj->getAddressComponentsById($array) ;
//$results = $obj->setAddresses($addr) ;

$results = $obj->setAddresses($fakeAddr) ;
print_r($results) ;
// <-------- CUT HERE -------->

    $this->ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->ag_foo);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agFooForm();
    unset($this->form['created_at'], $this->form['updated_at']);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agFooForm();
    unset($this->form['created_at'], $this->form['updated_at']);
    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id'))), sprintf('Object ag_foo does not exist (%s).', $request->getParameter('id')));
    $this->form = new agFooForm($ag_foo);
    unset($this->form['created_at'], $this->form['updated_at']);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id'))), sprintf('Object ag_foo does not exist (%s).', $request->getParameter('id')));
    $this->form = new agFooForm($ag_foo);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id'))), sprintf('Object ag_foo does not exist (%s).', $request->getParameter('id')));
    $ag_foo->delete();

    $this->redirect('foo/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $ag_foo = $form->save();

      $this->redirect('foo/edit?id=' . $ag_foo->getId());
    }
  }

}
