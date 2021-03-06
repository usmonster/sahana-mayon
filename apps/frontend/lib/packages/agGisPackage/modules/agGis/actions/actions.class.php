<?php

/**
 * agEmbeddedShiftTemplateForm.class.php
 *
 * Provides embedded subform for editing facility resources
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
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
    $this->map_url = Doctrine::getTable('agGlobalParam') //all global parameters
            ->createQuery('a')
            ->select('a.*')
            ->from('agGlobalParam a')
            ->where('datapoint =?', 'gis_map_url')
            ->fetchOne();
  }

  /**
   * presents the user with a form for calculating distance between two records
   * for future it will calculate distances between all records.
   *
   * @param sfWebRequest $request web request
   */
  public function executeDistance(sfWebRequest $request)
  {
    $countArray = agGisQuery::searchUnrelatedGeo(TRUE, 'staff', 'facility');
    $this->combinationCount = $countArray[0]['rowCount'];

    $geoRelationSet = agGisQuery::searchUnrelatedGeo(FALSE, 'staff', 'facility');

    if ($request->isXmlHttpRequest()) {
      $this->results = agGisQuery::updateDistance($geoRelationSet, 1);//todo: $relationType);
      //return sfView::HEADER_ONLY;

      return sfView::$this->renderPartial('distancecalc', array('results' => $this->results));
    }
  }

  /**
   * presents the user with a list of staff members, their addresses, giving you the ability
   * to geocode an address via a hyperlink beside an address
   * 
   * @param sfWebRequest $request web request
   */
  public function executeListstaff(sfWebRequest $request)
  {
    require_once(sfConfig::get('sf_app_lib_dir') . '/packages/agGisPackage/lib/vendor/agasti/agGis.class.php');
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
    require_once(sfConfig::get('sf_app_lib_dir') . '/packages/agGisPackage/lib/vendor/agasti/agGis.class.php');
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
    require_once(sfConfig::get('sf_app_lib_dir') . '/packages/agGisPackage/lib/agGis.class.php');
    $this->uncodedStaffCount = count(agGisQuery::missingGis('staff'));
    $this->uncodedFacilityCount = count(agGisQuery::missingGis('facility'));
    $this->goodCount = 25;
    $this->zipCount = 15;
    $this->nonCount = 5;
    if ($request->getParameter('address_id')) {
      $this->form = new agAddressGeoForm();
    }
    if ($request->isMethod('post')) {//we're going to assume that posts here come from javascript
      $geoJson = new agGis();
      $toGeo = array('number' => $request->getParameter('number'), 'street' => $request->getParameter('street'), 'zip' => $request->getParameter('zip'));
      $toDB = $request->getParameter('address_id');
      $toScreen = $geoJson->getGeocode($toGeo);
      //CLEAN THIS UP!!!!!!!
      $this->toDB = $toDB;
      $this->address = $toGeo;
      $this->result = $toScreen;

      // at this point we're assuming the user thinks they have a better geo code, via whatever source.
      // either that, or it's a new entry.  let's see if there is an entry for this address.
      //$existingGeo1 = Doctrine_Core::getTable('agAddressGeo')->find($request->getParameter('address_id')); //is this going to find the address or join id?JOIN
      $existingGeo2 = Doctrine_Core::getTable('agAddressGeo')->findByDql('address_id = ?', $request->getParameter('address_id'));
      //the above works.which is faster?
      //OR we could do a findby
      $existingGeo3 = Doctrine_Core::getTable('agAddressGeo')->findBy('address_id', $request->getParameter('address_id'), Doctrine_CORE::HYDRATE_SINGLE_SCALAR);
      if ($toDB && !$existingGeo3) {
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
        $newGeoFeature->geo_coordinate_order = 1; //this should have a default value as we're mainly dealing with points
        $newGeoFeature->save();

        $addressGeo = new agAddressGeo();
        $addressGeo->address_id = $toDB;
        $addressGeo->geo_id = $newGeo->getId();
        $addressGeo->geo_match_score_id = 1;
        $addressGeo->save();
      } else {
        /* if there already is an entry for this... update if better, etc... HERE */
      }
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
    if ($form->isValid()) {
      if (isset($_REQUEST['geocode'])) {
        $geo = new agGeo();
        $geoFeature = $geo->setAgGeoFeature();
        $geoType = $geo->getAgGeoType();
//        $fixedaddressarray = $geo->
        $theGeo = new addrToGeo($address);

        $ag_address_geo = $form->save();
        $foo->setAgAddressGeo($ad_address_geo);
        $this->redirect('gis/edit?id=' . $ag_geo->getId());
      }
    }
  }

}
