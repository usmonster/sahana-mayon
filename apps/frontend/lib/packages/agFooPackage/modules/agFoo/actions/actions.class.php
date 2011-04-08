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

    $addressHelper = new agEntityAddressHelper();
//    $addressHelper= agEntityAddressHelper::init();
    $addressByType = $addressHelper->getEntityAddressByType(array(1, 2, 3, 4, 5), TRUE, FALSE, agAddressHelper::ADDR_GET_STRING);
    $addressAll = $addressHelper->getEntityAddress(array(1, 2, 3, 4, 5), TRUE, FALSE, agAddressHelper::ADDR_GET_STRING);
    unset($addressHelper);
    echo 'Address By Type: <br />';
    print_r($addressByType);
    echo '<br /><br />Address all: <br />';
    print_r($addressAll);
    echo '<br /><br />';

//    $emailHelper = new agEntityEmailHelper();
//    $emailByType = $emailHelper->getEntityEmailByType(array(1, 2, 3, 4, 5), TRUE);
//    $emailAll = $emailHelper->getEntityEmail(array(1, 2, 3, 4, 5), TRUE);
//    unset($emailHelper);
//    echo '<br /><br />Email By Type: <br />';
//    print_r($emailByType);
//    echo '<br /><br />Email All: <br />';
//    print_r($emailAll);
//    echo '<br /><br />';

    $phoneHelper = new agEntityPhoneHelper();
//    $phoneByType = $phoneHelper->getEntityPhoneByType(array(3, 4, 5), TRUE);
//    $phoneAll = $phoneHelper->getEntityPhone(array(3, 4, 5), TRUE);
    $phoneByType = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5), TRUE, FALSE, agPhoneHelper::PHN_GET_FORMATTED);
    $phoneAll = $phoneHelper->getEntityPhone(array(1, 2, 3, 4, 5), TRUE, FALSE, agPhoneHelper::PHN_GET_FORMATTED);
    $phoneAreaCode = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_AREA_CODE));
    $phoneFirstThree = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_FIRST_THREE));
    $phoneLastFour = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_LAST_FOUR));
    $phoneExtension = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_EXTENSION));
    $phoneDefault = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_DEFAULT));
    $phoneByComponents = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT_SEGMENTS);
    unset($phoneHelper);
    echo '<br /><br />Phone By Type: <br />';
    print_r($phoneByType);
    echo '<br /><br />Phone All: <br />';
    print_r($phoneAll);
    echo '<br /><br />';
    echo '<br /><br />Phone Area Code: <br />';
    print_r($phoneAreaCode);
    echo '<br /><br />Phone First Three: <br />';
    print_r($phoneFirstThree);
    echo '<br /><br />Phone Last Four: <br />';
    print_r($phoneLastFour);
    echo '<br /><br />Phone Extension: <br />';
    print_r($phoneExtension);
    echo '<br /><br />Phone Default: <br />';
    print_r($phoneDefault);
    echo '<br /><br />Phone By Component: <br />';
    print_r($phoneByComponents);


//    $phoneAreaCode = $agPhoneHelper::getPhoneComponent(array(1,2, 3, 4, 5), agPhoneHelper::PHN_AREA_CODE);
//    echo '<br /><br />Phone Area Code: <br />';
//    print_r($phoneAreaCode);
//    $phoneFirstThree = $agPhoneHelper::getPhoneComponent(array(1,2, 3, 4, 5), agPhoneHelper::PHN_FIRST_THREE);
//    echo '<br /><br />Phone First Three: <br />';
//    print_r($phoneFirstThree);
//    $phoneLastFour = $phoneHelper->getPhoneComponent(array(1,2, 3, 4, 5), agPhoneHelper::PHN_LAST_FOUR);
//    echo '<br /><br />Phone Last Four: <br />';
//    print_r($phoneLastFour);
//    $phoneExtension = $phoneHelper->getPhoneComponent(array(1,2, 3, 4, 5), agPhoneHelper::PHN_EXTENSION);
//    echo '<br /><br />Phone Extension: <br />';
//    print_r($phoneExtension);
//    unset($phoneHelper);
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
$fakeAddr = array(array(array('1'=>'monkeybanana3', '2'=>'raffle1', '3'=>'New York City', '4'=>'NY', '5'=>'10031', '7'=>'United State of America'), 1),
    array(array('1'=>'peachpitters', '5'=>'10035'), 1)) ;
$fakeEntityAddr = array('1' => array(array(2, array(array('1'=>'peachpitters', '5'=>'10035'), 1)), array(3, array(array('1'=>'monkeybanana4', '2'=>'raffles', '3'=>'New York City', '4'=>'NY', '5'=>'10031', '7'=>'United State of America'), 1))), 3 => array(array(1,array(array('1'=>'monkeybanana3', '2'=>'raffle1', '3'=>'New York City', '4'=>'NY', '5'=>'10031', '7'=>'United State of America'), 1)))) ;
$addrContact = array( '1'=>array(array(3,4), array(2,1)),
    '10'=>array(array(3,10)),
    '87'=>array(array(1,9))) ;
//$obj = agAddressHelper::init() ;
//$obj = new agEntityEmailHelper ;
//$obj->setRecordIds($array) ;

$obj = new agPersonNameHelper() ;
//$obj = agEntityAddressHelper::init() ;
//$obj->setAgAddressHelper() ;
//$obj->agAddressHelper->lineDelimiter = '<br />' ;
//$addr = $obj->getEntityAddress($array, FALSE, FALSE) ;
//$results = $obj->defaultNameComponents ;
//$results = $obj->getPersonIds() ;
//$results = $obj->getPrimaryNameById() ;
//$results = $obj->getPrimaryNameByType() ;
//$results = $obj->getPrimaryNameAsString() ;
//$results = $obj->getNativeAddressAsString($array) ;
//$results = $obj->getAddressStandardId() ;
//$results = agPersonNameHelper::init()->getPrimaryNameAsString(array(4)) ;
//$results = $obj->getAddressAllowedElements() ;
//$obj = Doctrine_Core::getTable('agPerson')->find(1) ;
//$results = $obj->updateAddressHashes($array) ;
//$addr = $obj->getAddressComponentsById($array) ;
//$results = $obj->setAddresses($fakeAddr, TRUE) ;
//$results = $obj->setEntityAddress($fakeEntityAddr, array(), TRUE, FALSE, TRUE) ;
//$results = $obj->exceptionTest() ;
//$results = $obj->getEntityEmail(NULL, TRUE, FALSE) ;

$results = $obj->purgePersonNames(array(3), TRUE, TRUE) ;

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
