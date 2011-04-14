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

//    $addressHelper = new agEntityAddressHelper();
////    $addressHelper= agEntityAddressHelper::init();
//    $addressByType = $addressHelper->getEntityAddressByType(array(1, 2, 3, 4, 5), FALSE, FALSE);
////    $addressByType = $addressHelper->getEntityAddressByType(array(1, 2, 3, 4, 5), TRUE, FALSE, agAddressHelper::ADDR_GET_STRING);
//    $addressAll = $addressHelper->getEntityAddress(array(1, 2, 3, 4, 5), TRUE, FALSE);
////    $addressAll = $addressHelper->getEntityAddress(array(1, 2, 3, 4, 5), TRUE, FALSE, agAddressHelper::ADDR_GET_STRING);
//    $addressByComponent = $addressHelper->getEntityAddressByType(array(1, 2, 3, 4, 5), TRUE, FALSE, agAddressHelper::ADDR_GET_TYPE);
////    unset($addressHelper);
//    echo 'Address By Type: <br />';
//    print_r($addressByType);
//    echo '<br /><br />Address all: <br />';
//    print_r($addressAll);
//    echo '<br /><br />Address By Component: <br />';
//    print_r($addressByComponent);
//    echo '<br /><br />';
//
////    $entityContacts = array(97 => array('home' => array( array( 1, array('city' => 'New York', 'country' => 'United States of America', 'line 1' => '88 East End Avenue', 'zip5' => '10028', 'zip+4' => '8024', 'state' => 'NY', 'latitude' => 40.77342100, 'longitude' => -73.94589400 )),
////                                                         array( 1, array('line 1' => '227 West 46 Street', 'city' => 'New York', 'zip5' => '10036', 'country' => 'United States of America', 'state' => 'NY', 'latitude' => 40.75929100, 'longitude' => -73.98668400 ))
////                                                       ),
////                                        'work' => array( array( 1, array('line 1' => '227 West 46 Street', 'city' => 'New York', 'zip5' => '10036', 'country' => 'United States of America', 'state' => 'NY', 'latitude' => 40.75929100, 'longitude' => -73.98668400 ))  )
////                                       )
////                           );
//    $entityContacts = array(97 => array('home' => array( 1, array( 'city' => 'New York',
//                                                                   'country' => 'United States of America',
//                                                                   'line 1' => '88 East End Avenue',
//                                                                   'zip5' => '10028',
//                                                                   'zip+4' => '8024',
//                                                                   'state' => 'NY',
//                                                                   'latitude' => 40.77342100,
//                                                                   'longitude' => -73.94589400 )
//                                                       ),
//                                        'work' => array( 1, array( 'line 1' => '227 West 46 Street',
//                                                                   'city' => 'New York',
//                                                                   'zip5' => '10036',
//                                                                   'country' => 'United States of America',
//                                                                   'state' => 'NY',
//                                                                   'latitude' => 40.75929100,
//                                                                   'longitude' => -73.98668400
//                                                                 )
//                                                       )
//                                       )
//                           );
//    $output = $addressHelper->setEntityAddress($entityContacts);
//    print_r($output);


    $emailHelper = new agEntityEmailHelper();
    $emailByType = $emailHelper->getEntityEmailByType(array(1, 2, 3, 4, 5), TRUE, FALSE, agEmailHelper::EML_GET_VALUE);
//    $emailAll = $emailHelper->getEntityEmail(array(1, 2, 3, 4, 5), TRUE, TRUE);
////    unset($emailHelper);
    echo '<br /><br />Email By Type: <br />';
    print_r($emailByType);
//    echo '<br /><br />Email All: <br />';
//    print_r($emailAll);
//    echo '<br /><br />';
//
//     $entityContacts = array(87 => array( array( 1, '    emailHelper1@localhost.com    '),
//                                          array( 3, '    email2@spssample.com   '),
//                                          array( 1, 'email1@spssample.com')
//                                        ),
//                            88 => array( array( 1, 'emailHelper2@localhost.com') ),
//                            89 => array( array( 1, 'email3@spssample.com') )
//                           );
//    $output = $emailHelper->setEntityEmail($entityContacts, FALSE);
//    print_r($output);



//    $phoneHelper = new agEntityPhoneHelper();
//    $phoneByType = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5, 87), TRUE, FALSE, agPhoneHelper::PHN_GET_FORMATTED);
//    $phoneAll = $phoneHelper->getEntityPhone(array(1, 2, 3, 4, 5, 87), TRUE, FALSE, agPhoneHelper::PHN_GET_FORMATTED);
//    $phoneAll = $phoneHelper->getEntityPhone(array(1, 2, 3, 4, 5, 87), FALSE, FALSE);
//    $phoneAreaCode = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5, 87), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_AREA_CODE));
//    $phoneFirstThree = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5, 87), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_FIRST_THREE));
//    $phoneLastFour = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5, 87), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_LAST_FOUR));
//    $phoneExtension = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5, 87), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_EXTENSION));
//    $phoneDefault = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5, 87), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT, array(agPhoneHelper::PHN_DEFAULT));
//    $phoneByComponents = $phoneHelper->getEntityPhoneByType(array(1, 2, 3, 4, 5, 87), TRUE, TRUE, agPhoneHelper::PHN_GET_COMPONENT_SEGMENTS);
////    unset($phoneHelper);
//    echo '<br /><br />Phone By Type: <br />';
//    print_r($phoneByType);
//    echo '<br /><br />Phone All: <br />';
//    print_r($phoneAll);
//    echo '<br /><br />';
//    echo '<br /><br />Phone Area Code: <br />';
//    print_r($phoneAreaCode);
//    echo '<br /><br />Phone First Three: <br />';
//    print_r($phoneFirstThree);
//    echo '<br /><br />Phone Last Four: <br />';
//    print_r($phoneLastFour);
//    echo '<br /><br />Phone Extension: <br />';
//    print_r($phoneExtension);
//    echo '<br /><br />Phone Default: <br />';
//    print_r($phoneDefault);
//    echo '<br /><br />Phone By Component: <br />';
//    print_r($phoneByComponents);
//
//    $entityContacts = array( 87 => array( array( 1, array('1234567890', 2)),
//                                          array( 1, array('1234567890x123', 1)),
//                                          array( 1, array('55555'))
//                                        ),
//                             88 => array( array( 1, array('3762923490x333', 1) ) ),
//                             89 => array( array( 1, array('3456789012', 1 ) ) )
//                           );
//    $output = $phoneHelper->setEntityPhone($entityContacts, FALSE);
//    print_r($output);
//    echo "<br /><br />";
//
//    $entityContacts = array( 90 => array( array( 2, array('2222222222', 1)),
//                                          array( 1, array('8888888888x88', 2)),
//                                          array( 3, array('3333333333', 1))
//                                        ),
//                             91 => array( array( 4, array('1111111111x11111') ) ),
//                             92 => array( array( 2, array('788x45t' ) ) ),
//                             93 => array(array( 2, array('9999999999x99999') ) )
//                           );
//    $entityContacts = array( 95 => array( array( 4, array('3223456778x111') ) ) );
//    $entityContacts = array( 90 => array( array( 1, array('1212121212x12', 2)),
//                                          array( 1, array('8888888888x88', 2))
//                                        )
//                           );
//    $output = $phoneHelper->setEntityPhone($entityContacts, TRUE);
//    print_r($output);
//    echo "<br /><br />";
//
//    $entityContacts = array( 101 => array( array( 4, array('3434343434x34'))),
//                             102 => array( array( 3, array('23456')),
//                                           array( 3, array('2222222222')) ),
//                              88 => array( array( 4, array('55555')))
//                           );
//    $output = $phoneHelper->setEntityPhone($entityContacts, FALSE);
//    print_r($output);
//    echo "<br /><br />";
//    $entityContacts = array( 103 => array( array( 4, array('4445556666x777')),
//                                           array( 3, array('6667777888')),
//                                           array( 2, array('23456')),
//                                           array( 3, array( '1122334455', 1)),
//                                           array( 2, array('445566778899323', 2))
//                                         )
//                           );
////    $entityContacts = array( 104 => array( array( 3, array('73762'))));
//    $output = $phoneHelper->setEntityPhone($entityContacts, FALSE);
//    print_r($output);
//    echo "<br /><br />";
//    $entityContacts = array( 103 => array( array( 2, array('   445566778899323   ')),
//                                           array( 1, array('    8888889999   ', 1))
//                                         )
//                           );
//    $output = $phoneHelper->setEntityPhone($entityContacts, TRUE);
//    print_r($output);

//
//    $stfResHelper = new agStaffResourceHelper();
//    $staffResources = $stfResHelper->getStaffResourceComponents(array(1, 2, 3, 4, 5), TRUE);
//    echo"Staff Resource Helper:<br />";
//    print_r($staffResources);
//    echo"<br /><br />";
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
$fakeAddr = array(array(array('1'=>'    monkeybanana3   ', '2'=>'raffle1', '3'=>'New York City', '4'=>'NY', '5'=>'10031', '7'=>'United State of America'), 1),
    array(array('1'=>'peachpitters', '5'=>'10035'), 1)) ;
//$fakeEntityAddr = array('1' => array(array(2, array(array('1'=>'peachpitters', '5'=>'10035'), 1)),
//                                     array(1, array(array('1'=>'    nickel    ','2'=>'bronze','3'=>'silver','4'=>'gold','5'=>'platinum','7'=>'    metals   ', '8'=>'    blah blah blah blah blah blah blah                          '), 1)),
//                                     array(3, array(array('1'=>'monkeybanana4', '2'=>'raffles', '3'=>'New York City', '4'=>'NY', '5'=>'10031', '7'=>'United State of America'), 1))),
//                          3 => array(array(1,array(array('1'=>'monkeybanana3', '2'=>'raffle1', '3'=>'New York City', '4'=>'NY', '5'=>'10031', '7'=>'United State of America'), 1)))) ;
$fakeEntityAddr = array('1' => array(array(1, array(array('1'=>'    nickel    ','2'=>'bronze','3'=>'silver','4'=>'gold','5'=>'platinum','7'=>'    metals   ', '8'=>'    blah blah blah blah blah blah blah                          '), 1)),
                                     array(3, array(array('1'=>'monkeybanana4', '2'=>'raffles', '3'=>'New York City', '4'=>'NY', '5'=>'10031', '7'=>'United State of America'), 1))),
                          3 => array(array(1,array(array('1'=>'monkeybanana3', '2'=>'raffle1', '3'=>'New York City', '4'=>'NY', '5'=>'10031', '7'=>'United State of America'), 1)))) ;
$addrContact = array( '1'=>array(array(3,4), array(2,1)),
    '10'=>array(array(3,10)),
    '87'=>array(array(1,9))) ;

$newNames = array(4 => Array ( 1 => Array ( 'Dummy'), 5 => Array ( 'Ali' ), 4 => Array ( 'Betty', 'Amusan' ), 3 => Array ( 'Ammonds' ), 2 => Array ( 'Amy' ) ),
    9 => Array (),
    8 => Array ( 3 => Array ( 'Aponte' ), 1 => Array ( 'Anna' ) ),
    10 => Array ( 3 => Array ( 'Butch' ), 1 => Array ( 'Sundance' ) )
) ;
//$obj = agAddressHelper::init() ;
//$obj = new agEntityEmailHelper ;
//$obj->setRecordIds($array) ;

//$obj = new agPersonNameHelper() ;
$obj = agEntityAddressHelper::init() ;
//$obj->setAgAddressHelper() ;
//$obj->agAddressHelper->lineDelimiter = '<br />' ;
//$addr = $obj->getEntityAddress($array, FALSE, FALSE) ;
//$results = $obj->defaultNameComponents ;
//$results = $obj->getPersonIds() ;
//$results = $obj->getPrimaryNameById() ;
//$results = $obj->setPersonNames($newNames, TRUE, TRUE) ;
//$results = $obj->getPrimaryNameAsString() ;
//$results = $obj->getNativeAddressAsString($array) ;
//$results = $obj->getAddressStandardId() ;
//$results = agPersonNameHelper::init()->getPrimaryNameAsString(array(4)) ;
//$results = $obj->getAddressAllowedElements() ;
//$obj = Doctrine_Core::getTable('agPerson')->find(1) ;
//$results = $obj->updateAddressHashes($array) ;
//$addrxnentsById($array) ;
//$results = $obj->setAddresses($fakeAddr, TRUE) ;
$results = $obj->setEntityAddress($fakeEntityAddr, array(), TRUE, TRUE, TRUE) ;
//$results = $obj->exceptionTest() ;
//$results = $obj->getEntityEmail(NULL, TRUE, FALSE) ;

//$results = $obj->purgePersonNames(array(3), TRUE, TRUE) ;

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
