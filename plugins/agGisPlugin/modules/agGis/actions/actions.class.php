<?php

/**
* agEmbeddedShiftTemplateForm.class.php
*
* Provides embedded subform for editing facility resources
*
* PHP Version 5
*
* LICENSE: This source file is subject to LGPLv3.0 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Charles Wisniewski, CUNY SPS
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/

class agGisActions extends sfActions
{
   /**
   * presents the user with the default indexSuccess page
   * 
   * @param sfWebRequest $request web request
   */
  public function executeIndex(sfWebRequest $request)
  {
//    $bar = 'bar';
//    $foo = new agGis($bar);
  }
    /**
   * presents the user with a form for gis configuration options
   *
   * @param sfWebRequest $request web request
   */
  public function executeConfig(sfWebRequest $request)
  {
    //configuration options
  }
  /**
   * presents the user with a form for calculating distance between two records
   * for future it will calculate distances between all records.
   *
   * @param sfWebRequest $request web request
   */
  public function executeDistance(sfWebRequest $request)
  {
    $this->ag_facility_geos = Doctrine::getTable('agFacility') //should be agStaff.
                                      ->createQuery('a')
                                      ->select('f.*,s.*, e.*, pa.*, aa.*, aag.*')
                                      ->from('agFacility f, f.agSite s, s.agEntity e, e.agEntityAddressContact pa,
                                        pa.agAddress aa, aa.agAddressGeo aag, 
                                        ')
                                      ->execute();
    $this->ag_staff_geos = Doctrine::getTable('agPerson') //we want to get all persons that don't have
                                      ->createQuery('a')
                                      ->select('f.*,s.*, e.*, pa.*, aa.*, aas.*')
                                      ->from('agFacility f, f.agSite s, s.agEntity e, e.agEntityAddressContact pa,
                                        pa.agAddress aa, aa.agAddressStandard aas, aas.agAddressFormat aaf
                                        ')//, agAddressFormat,agAddressElement - including element quadrupled this!!!
                                      ->execute();

    $this->form = new agGeoRelationshipForm();
    
    //show two listboxes, one of staff,
    //one of facilities
    //since we currently only have data for staff members, we will be doing staff to staff calculation
    //will only output to screen

  }
    /**
   * presents the user with a list of staff members, their addresses, giving you the ability
   * to geocode an address via a hyperlink beside an address
   * 
   * @param sfWebRequest $request web request
   */
  public function executeListstaff(sfWebRequest $request)
  {
   require_once(sfConfig::get('sf_plugins_dir') . '/agGisPlugin/lib/vendor/agasti/agGis.class.php');
    $this->ag_staff_geos = Doctrine::getTable('agPerson')
                                      ->createQuery('a')
                                      ->select('p.*, e.*, pa.*, aa.*, aas.*, namejoin.*, name.*, nametype.*')
                                      ->from('agPerson p, p.agEntity e, e.agEntityAddressContact pa,
                                        p.agPersonMjAgPersonName namejoin, namejoin.agPersonName name,
                                        name.agPersonNameType nametype,
                                        pa.agAddress aa, aa.agAddressStandard aas, aas.agAddressFormat aaf
                                        ')//, agAddressFormat,agAddressElement - including element quadrupled this!!!
                                      ->execute();
      //as of 12.11.2010 there is no concept of a 'global priority' or 'preference' for a person's
      //address, thjough  individual address types have priority

    $this->ag_address_contact_types = Doctrine::getTable('agAddressContactType')
        ->createQuery('f')
        ->execute();
    $this->ag_person_name_types = Doctrine::getTable('agPersonNameType')
            ->createQuery('b')
            ->execute();

  }
  public function executeListfacility(sfWebRequest $request)
  {
    require_once(sfConfig::get('sf_plugins_dir') . '/agGisPlugin/lib/vendor/agasti/agGis.class.php');
    $this->ag_facility_geos = Doctrine::getTable('agFacility')
                                      ->createQuery('a')
                                      ->select('f.*,s.*, e.*, pa.*, aa.*, aas.*')
                                      ->from('agFacility f, f.agSite s, s.agEntity e, e.agEntityAddressContact pa,
                                        pa.agAddress aa, aa.agAddressStandard aas, aas.agAddressFormat aaf
                                        ')//, agAddressFormat,agAddressElement - including element quadrupled this!!!
                                      ->execute();
      $this->ag_address_contact_types = Doctrine::getTable('agAddressContactType')
        ->createQuery('f')
        ->execute();
  }
    /**
   * presents the user with a new address to geo form
   * to geocode an address, this should not be used by the end user
   *
   * @param sfWebRequest $request web request
   */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agAddressGeoForm();
  }
   /**
   * executes the create function from the above action
   *
   * @param sfWebRequest $request web request
   */

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agAddressGeoForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }
   /**
   * presents the user with a form to edit address to geo information
   * this should not be used by the end user
   *
   * @param sfWebRequest $request web request
   */
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_geo = Doctrine_Core::getTable('agGeo')->find(array($request->getParameter('id'))), sprintf('Object ag_geo does not exist (%s).', $request->getParameter('id')));
    $this->form = new agGeoForm($ag_geo);
  }
   /**
   * executes the edit function from the above action, processes the form
   *
   * @param sfWebRequest $request web request
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_geo = Doctrine_Core::getTable('agGeo')->find(array($request->getParameter('id'))), sprintf('Object ag_geo does not exist (%s).', $request->getParameter('id')));
    $this->form = new agGeoForm($ag_geo);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }
   /**
   * geocodes an address provided in the web request, adds entries to the database
   * to support the geocode
   *
   * @param sfWebRequest $request web request
   */
  public function executeGeocode(sfWebRequest $request)
  {
    require_once(sfConfig::get('sf_plugins_dir') . '/agGisPlugin/lib/vendor/agasti/agGis.class.php');

    if($request->getParameter('address_id'))
    {
      $this->form = new agAddressGeoForm();
    }
    $geoJson = new agGis();
    $toGeo = array('number' => $request->getParameter('number'), 'street' => $request->getParameter('street'),'zip' => $request->getParameter('zip'));
    $toDB = $request->getParameter('address_id');
    $toScreen = $geoJson->getGeocode($toGeo);
    //CLEAN THIS UP!!!!!!!
    $this->toDB = $toDB;
    $this->address = $toGeo;
    $this->result = $toScreen;

    if ($toDB)
    {
      /* if this is a new geocode, create an ag_geo
       *
       */
      $newGeo = new agGeo();
      $newGeo->setGeoSourceId(1); //comes from our default geoserver
      $newGeo->setGeoTypeId(1); //point
      $newGeo->save();

      $newCoord = new agGeoCoordinate();
      $newCoord->setLongitude($toScreen[0]); //create a new entry in our coordinate table
      $newCoord->setLatitude($toScreen[1]);
      $newCoord->save();

      $newGeoFeature = new agGeoFeature();
      $newGeoFeature->geo_coordinate_id = $newCoord->getId();
      $newGeoFeature->geo_id = $newGeo->getId();
      
      $addressGeo = new agAddressGeo();
      $addressGeo->address_id = $toDB;
      $addressGeo->geo_id = $newGeo->getId();
      $addressGeo->geo_match_score_id = 1;
      $addressGeo->save();

    }
    else
    {
      /* if there already is an entry for this... update if better, etc... HERE */
    }
  }
   /**
   * executes the delete function for a geo item
   *
   * @param sfWebRequest $request web request
   */

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_geo = Doctrine_Core::getTable('agGeo')->find(array($request->getParameter('id'))), sprintf('Object ag_geo does not exist (%s).', $request->getParameter('id')));
    $ag_geo->delete();

    $this->redirect('gis/index');
  }
   /**
   * processes the form in question, this handles various forms
   * though currently only handles the geocode form
   *
   * @param sfWebRequest $request web request
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      if (isset($_REQUEST['geocode']))
      {
        $geo = new agGeo();
        $geoFeature = $geo->setAgGeoFeature();
        $geoType = $geo->getAgGeoType();
//        $fixedaddressarray = $geo->
        $theGeo = new addrToGeo($address);

        $ag_address_geo = $form->save();
        $foo->setAgAddressGeo($ad_address_geo);
        $this->redirect('gis/edit?id='.$ag_geo->getId());
      }
    }
  }
}
