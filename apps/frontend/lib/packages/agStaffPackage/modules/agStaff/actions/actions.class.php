<?php

/**
 * Staff Actions extends agActions
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Nils Stolpe, CUNY SPS
 * @author     Ilya Gulko, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agStaffActions extends agActions
{

  /**
   * executeIndex is currently used to execute the index action
   *
   */
  public function executeIndex(sfWebRequest $request)
  {
    //do some index stuff here.
  }

  public function executeNewlist(sfWebRequest $request)
  {

    //the next few lines could be abstracted to agActions as they are request params that may be
    //used for any list
    if ($request->getGetParameter('filter'))
      $this->filter = $request->getGetParameter('filter');
    if ($request->getGetParameter('sort'))
      $this->sort = $request->getGetParameter('sort');
    if ($request->getGetParameter('order'))
      $this->order = $request->getGetParameter('order');
    $this->agPersonNameTypes = agDoctrineQuery::create()
            ->select('nt.id, nt.person_name_type')
            ->from('agPersonNameType nt')
            ->execute(array(), 'key_value_pair');
    /** @todo take into consideration app_display */
    $staffStatus = 'active';
    $staffStatusOptions = agDoctrineQuery::create()
            ->select('s.staff_status, s.staff_status')
            ->from('agStaffStatus s')
            ->execute(array(), 'key_value_pair');
    //the above query returns an array of keys matching their values.
    //ideally the above should exist in a global param,
    //so the database is not queried all the time
    $staffStatusOptions['all'] = 'all';
    if ($request->getParameter('status') && in_array($request->getParameter('status'), $staffStatusOptions)) {
      $staffStatus = $request->getParameter('status');
    }
    $this->statusFilterForm = new sfForm();
    $this->statusFilterForm->setWidgets(
        array(
          'status' => new sfWidgetFormChoice(
              array(
                'multiple' => false,
                'choices' => $staffStatusOptions,
                'label' => 'Staff Status'
              ),
              array('onchange' => 'submit();')
          ),
        // 'add_empty' => true))// ,'onClick' => 'submit()'))
        )
    );
    $this->statusFilterForm->setDefault('status', $staffStatus);

    $inlineDeco = new agWidgetFormSchemaFormatterInlineLeftLabel($this->statusFilterForm->getWidgetSchema());
    $this->statusFilterForm->getWidgetSchema()->addFormFormatter('inline', $inlineDeco);
    $this->statusFilterForm->getWidgetSchema()->setFormFormatterName('inline');

    $query = Doctrine::getTable('agStaff')
            ->createQuery('a')
            ->select(
                'p.*,
                  s.*,
                  namejoin.*,
                  name.*,
                  nametype.*,
                  stfrsco.*,
                  o.organization,
                  stfrsc.staff_resource_type_id,
                  ss.staff_status,
                  srt.staff_resource_type'
            )
            ->from(
                'agStaff s,
                  s.agPerson p,
                  p.agPersonMjAgPersonName namejoin,
                  namejoin.agPersonName name,
                  name.agPersonNameType nametype, s.agStaffResource stfrsc,
                  stfrsc.agStaffResourceOrganization stfrsco,
                  stfrsc.agStaffResourceType srt,
                  stfrsco.agOrganization o,
                  s.agStaffStatus ss'
    );
    if ($staffStatus != 'all') {
      $query->where('ss.staff_status=?', $staffStatus);
    }

    //we get our array of staff people above, now, populate it with data.
    //$this->staffArray = $staffArray;
    $this->pager = new agArrayPager(null, 10);


    $ag_staff = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    foreach ($ag_staff as $key => $value) {
      $person_array[] = $value['p_id'];

      //these two lines are only for development purposes,
      //they should be returned by their respective template helpers
      $person_phones[$value['p_id']] = 'phone';
      $person_emails[$value['p_id']] = 'email';
      //$remapped_array[$ag_event_staff['es_id']] = $
    }
    $names = new agPersonNameHelper($person_array);
    //^we need to get persons from the event staff ids that are returned here
    $person_names = $names->getPrimaryNameByType();
    //maybe we should set the below/above to a a static property for use through a session

    $emails = new agEntityEmailHelper($person_array);

    $email_array = $emails->getEntityEmailByType(null,false,true, agEmailHelper::EML_GET_VALUE);
    $person_emails = array();

    foreach($email_array as $person_id => $email_type)
    {
      foreach($email_type as $et => $etv){
        $person_emails[$person_id][$et] = $person_emails[$person_id][$et][0];
      }
    }

//    $phones = new agEntityPhoneHelper($person_array);
//    $phone_array = $phones->getEntityPhoneByType(null,true,true,true);
//    $person_phones = array();
//    foreach($phone_array as $person_id => $phone_type)
//    {
//      foreach($phone_type as $pt => $ptv){
//        $person_phones[$person_id][$pt] = $person_phones[$person_id][$pt][0];
//      }
//    }
    //$person_phones[$value['p_id']] = arraytostring($phone_array);
    //function arraytostring is a similar function to the draw address table function in agtemplatehelper

    foreach ($ag_staff as $staff => $value) {
      $staffArray[] = array(
        'fn' => $person_names[$value['p_id']]['given'],
        'ln' => $person_names[$value['p_id']]['family'],
        'agency' => $value['o_organization'],
        'staff_status' => $value['ss_staff_status'],
        'classification' => $value['srt_staff_resource_type'],
        'phones' => $person_phones[$value['p_id']], // only for testing, prefer the above
        'emails' => $person_emails[$value['p_id']],
          //'ess_staff_allocation_status_id' => $value['ess_staff_allocation_status_id']
          /** @todo benchmark scale */
      );
    }

//$this->ag_staff = $result_array;


    if ($request->getParameter('sort') && $request->getParameter('order')) {
      $sortColumns = array('group' => 'efg_event_facility_group',
        'name' => 'f_facility_name',
        'code' => 'f_facility_code',
        'status' => 'ras_facility_resource_allocation_status',
        'time' => 'efrat_activation_time',
        'type' => 'fgt_facility_group_type',
        'event' => 'e_event_name');
      $sort = $sortColumns[$request->getParameter('sort')];
      agArraySort::$sort = $sort;
      usort($facilityGroupArray, array('agArraySort', 'arraySort'));
      if ($request->getParameter('order') == 'DESC') {
        $staffArray = array_reverse($staffArray);
      }
    }
    $this->pager->setResultArray($staffArray);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();


    $this->ag_phone_contact_types = Doctrine::getTable('agPhoneContactType')
            ->createQuery('c')
            ->execute();
    $this->ag_email_contact_types = Doctrine::getTable('agEmailContactType')
            ->createQuery('d')
            ->execute();
    //p-code
    //$this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' ');
    //end p-code
  }

  /**
   * provides staff listing data to the staff list template
   * @param sfWebRequest $request
   */
  public function executeList(sfWebRequest $request)
  {
    $this->staffResourceStatus = 'active';
    $staffResourceStatusOptions = agDoctrineQuery::create()
            ->select('s.staff_resource_status, s.staff_resource_status')
            ->from('agStaffResourceStatus s')
            ->execute(array(), 'key_value_pair');
    //the above query returns an array of keys matching their values.
    //ideally the above should exist in a global param, so the database is not queried all the time
    $staffResourceStatusOptions['all'] = 'all';
    if ($request->getParameter('status') && in_array($request->getParameter('status'), $staffResourceStatusOptions)) {
      $this->staffResourceStatus = $request->getParameter('status');
    }
    $this->statusFilterForm = new sfForm();
    $this->statusFilterForm->setWidgets(
        array(
          'status' => new sfWidgetFormChoice(
              array(
                'multiple' => false,
                'choices' => $staffResourceStatusOptions,
                'label' => 'Staff Status'
              ),
              array('onchange' => 'submit();')
          ),
        //add_empty' => true))// ,'onClick' => 'submit()'))
        )
    );
    $this->statusFilterForm->setDefault('status', $this->staffResourceStatus);

    $inlineDeco = new agWidgetFormSchemaFormatterInlineLeftLabel($this->statusFilterForm->getWidgetSchema());
    $this->statusFilterForm->getWidgetSchema()->addFormFormatter('inline', $inlineDeco);
    $this->statusFilterForm->getWidgetSchema()->setFormFormatterName('inline');

    $query = Doctrine::getTable('agStaff')
            ->createQuery('a')
            ->select(
                'p.*,
                  s.*,
                  namejoin.*,
                  name.*,
                  nametype.*,
                  o.organization agency,
                  stfrsc.staff_resource_type_id,
                  e.id,
                  ememail1.id,
                  ec1.id,
                  ect1.email_contact_type work_email,
                  ememail2.id,
                  ec2.id,
                  ect2.email_contact_type home_email'
            )
            ->from(
                'agStaff s,
                  s.agPerson p,
                  p.agPersonMjAgPersonName namejoin,
                  namejoin.agPersonName name,
                  name.agPersonNameType nametype,
                  s.agStaffResource stfrsc,
                  stfrsc.agStaffResourceStatus srs,
                  stfrsc.agStaffResourceType,
                  stfrsc.agOrganization o,
                  p.agEntity e,
                  e.agEntityEmailContact ememail1,
                  ememail1.agEmailContact ec1,
                  ec1.agEmailContactType ect1,
                  e.agEntityEmailContact ememail2,
                  ememail2.agEmailContact ec2,
                  ec2.agEmailContactType ect2'
    );
    if ($this->staffResourceStatus != 'all') {
      $query->where('srs.staff_resource_status=?', $this->staffResourceStatus);
    }

    $this->pager = new sfDoctrinePager('agStaff', 20);
    /**
     * @todo include exception handling for a bad sort param
     */
    if ($request->getParameter('sort')) {
      if (substr($request->getParameter('sort'), 0, 11) == 'person_name') {
        $nameId = substr($request->getParameter('sort'), 12);
        $sortOrder = $request->getParameter('order', 'DESC');
        $this->pager->setQuery(
            $query->orderBy(
                'namejoin.person_name_type_id = ' . $nameId . ' ' . $sortOrder . ',
                  person_name ' . $sortOrder
            )
        );
      } else {
        $this->pager->setQuery($query->orderBy($request->getParameter('sort', 'person_name') . ' ' . $request->getParameter('order', 'DESC')));
      }
    } else {
      $this->pager->setQuery($query);
    }

    //$sqloutput = $query->getSqlQuery();
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
    $this->ag_phone_contact_types = Doctrine::getTable('agPhoneContactType')
            ->createQuery('c')
            ->execute();
    $this->ag_email_contact_types = Doctrine::getTable('agEmailContactType')
            ->createQuery('d')
            ->execute();
    //ideally this should only happen once
  }

  /**
   * executeShow displays an individual agPerson/staff-member entry. It uses an sfDoctrinePager
   * to navigate
   *
   * the list of all staff members and to display the current staff member.
   *
   * The use of Doctrine::getTable for various _types and _formats tables is used to generate
   * table labels for the staff module's showSuccess.php.
   *
   * executeShow has also been modified so it can handle requests called from indexSuccess or
   * searchSuccess. In the former case, pagination is done for the entire list of agPersons, and
   * users can browse through every one in the DB. In the later case, a check is made to see if
   * $request has a query (it will only have one if it has come from searchSuccess). If $request
   * has query, paginated results will only include those agPersons returned by the Lucene query.
   * If not, each agPerson object will be paginated.
   *
   * @param $request - a query.
   * @todo refactor the _types and _formats queries into one larger query.
   */
  public function executeShow(sfWebRequest $request)
  {
    $query = Doctrine::getTable('agStaff')
            ->createQuery('a')
            ->select(
                'p.*,
                  st.*,
                  ps.*,
                  s.*,
                  pn.*,
                  n.*,
                  e.*,
                  lang.*,
                  religion.*,
                  namejoin.*,
                  name.*,
                  nametype.*'
            )
            ->from(
                'agStaff st, 
                  st.agPerson p,
                  p.agPersonSex ps,
                  ps.agSex s,
                  p.agPersonMjAgNationality pn,
                  pn.agNationality n,
                  p.agEthnicity e,
                  p.agLanguage lang,
                  p.agReligion religion,
                  p.agPersonMjAgPersonName namejoin,
                  namejoin.agPersonName name,
                  name.agPersonNameType nametype'
    );

    $this->pager = new sfDoctrinePager('agStaff', 1);

    //if we have exceucted a search
    if ($request['query']) {
      $lqResults = Doctrine_core::getTable('agStaff')->getForLuceneQuery($request['query']);

      $i = 0;
      $lqIds = array();

      foreach ($lqResults as $lqResult) {
        $lqIds[$i] = $lqResult->getId();
        $i++;
      }

      if (count($lqIds) > 0) {
        $q = Doctrine::getTable('agStaff')
                ->createQuery('a')
                ->select('s.*')
                ->from('agStaff s')
                ->where('s.id IN (' . implode(',', $lqIds) . ')');
        $this->pager->setQuery($q);
        $this->pager->setPage($request->getParameter('page', 1));
        $this->pager->init();

        $this->query = $request['query'];
      }
    } else {
      if ($request->getParameter('sort')) {
        if (substr($request->getParameter('sort'), 0, 11) == 'person_name') {
          $nameId = substr($request->getParameter('sort'), 12);
          $sortOrder = $request->getParameter('order', 'DESC');
          $this->pager->setQuery($query->orderBy('namejoin.person_name_type_id = ' . $nameId . ' ' . $sortOrder . ', person_name ' . $sortOrder));
        } else {
          $this->pager->setQuery($query->orderBy($request->getParameter('sort', 'person_name') . ' ' . $request->getParameter('order', 'DESC')));
        }
        //$this->sortAppend = $sortOrder;
      } else {
        //$this->pager->setQuery(Doctrine::getTable('agPerson')->createQuery('a'));
        $this->pager->setQuery($query);
      }
      $this->pager->setPage($request->getParameter('page', 1));
      $this->pager->init();
    }

    $this->ag_person_name_types = Doctrine::getTable('agPersonNameType')
            ->createQuery('b')
            ->execute();
    $this->ag_phone_contact_types = Doctrine::getTable('agPhoneContactType')
            ->createQuery('c')
            ->execute();
    $this->ag_email_contact_types = Doctrine::getTable('agEmailContactType')
            ->createQuery('d')
            ->execute();
    $this->ag_language_formats = Doctrine::getTable('agLanguageFormat')
            ->createQuery('e')
            ->execute();
    $this->ag_address_contact_types = Doctrine::getTable('agAddressContactType')
            ->createQuery('f')
            ->execute();
    $this->agStaff = $this->pager->getResults()->getFirst();
    $agPerson = $this->agStaff->getAgPerson();
    $this->addressArray = $agPerson->getEntityAddressByType(
            true, null, agAddressHelper::ADDR_GET_NATIVE_STRING
    );

    //p-code
    $names_title = new agPersonNameHelper($agPerson->getId());
    $person_names_title = $names_title->getPrimaryNameByType();
    $this->getResponse()
        ->setTitle(
            'Sahana Agasti Staff - ' . (
            isset($person_names_title[$agPerson->getId()]['given']) ?
                $person_names_title[$agPerson->getId()]['given'] : ''
            )
            . ' ' . (
            isset($person_names_title[$agPerson->getId()]['family']) ?
                $person_names_title[$agPerson->getId()]['family'] : ''
            )
    );
    //end p-code
  }

  /**
   * Creates a blank agPerson form to create and save a new agPerson/staff-member
   *
   * @param sfWebRequest $request - A request object
   * */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new PluginagStaffPersonForm();

    if($request->isMethod(sfRequest::POST)) {
      $params = $request->getPostParameters();

      // Render the Forms
      //if($params['type'] == 'resourceStatus') {
        // This case is for loading the event-facility-resource-status form to replace the link that sumbitted
        // the request.
        //$facilityResourceStatus = new agEventFacilityResourceStatus();
        //$facilityResourceStatus['event_facility_resource_id'] = ltrim($params['id'], 'res_stat_id_');
        //$facilityResourceStatus['facility_resource_allocation_status_id'] =
//            agDoctrineQuery::create()
//              ->select('id')
//              ->from('agFacilityResourceAllocationStatus')
//              ->where('facility_resource_allocation_status = ?', $params['current'])
//              ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        $resourceForm = new PluginagEmbeddedAgStaffResourceForm();
        //$resourceForm->getWidget('facility_resource_allocation_status_id')->setAttribute('class', 'inputGray submitTextToForm set100');

        return $this->renderPartial('setterForm', array('form'     => $resourceForm,
                                                        'set'      => $params['type'],
                                                        'id'       => $params['id'],
                                                        'url'      => 'event/eventfacilityresource?eventFacilityResourceId=' .  ltrim($params['id'], 'res_stat_id_')
                                                  )
                                   );
      }
  }

  /**
   * Saves the new agPerson from data entered into the form generated by executeNew
   *
   * A new agEntity is also created and assigned to the agPerson being created. This
   * ensures that the new agPerson gets a new entity_id and that creation does not fail.
   *
   * @param sfWebRequest $request - A request object
   * */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new PluginagStaffPersonForm();
//    $ent = new agEntity();
//    $ent->save();
//    $this->form->getObject()->setAgEntity($ent);
//    $this->processForm($request, $this->form);
//
//    $this->setTemplate('new');
    $ent = new agEntity();
    $ent->save();

    $this->form->getObject()->setAgEntity($ent);
    //$this->form should now have in it an entity and a staff person related.
    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  /**
   * Creates a form prepopulated with the current information of the staff member to be edited.
   *
   * @param sfWebRequest $request - This function expects an ag_person.id as pass-in parameter.
   *
   * */
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
        $ag_staff = Doctrine::getTable('AgStaff')->find($request->getParameter('id')),
        sprintf('Object ag_staff does not exist (%s).', $request->getParameter('id'))
    );

    $ag_person = $ag_staff->getAgPerson();
    $this->form = new PluginagStaffPersonForm($ag_person);

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti Staff Edit');
    //end p-code
  }

  /**
   * Processes the form created by executeEdit to update the agPerson/staff-member being edited.
   * @param sfWebRequest $request - This function expects an ag_person.id as a pass-in parameter.
   *
   * */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless(
        $request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT)
    );
    $this->forward404Unless(
        $ag_staff = Doctrine::getTable('AgStaff')->find($request->getParameter('id')),
        sprintf('Object ag_staff does not exist (%s).', $request->getParameter('id'))
    );

    $ag_person = $ag_staff->getAgPerson();
    $this->form = new PluginagStaffPersonForm($ag_person);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  /**
   * Deletes the selected agPerson/staff-member.
   * @param sfWebRequest $request - This function expects an ag_person.id as a pass-in parameter.
   * @todo implement soft delete
   *
   *
   * The getting and deleting of various $ag_person properties is necessary to ensure that
   * the agPerson's relations are all properly deleted as well.
   *
   * As values for agPersonLanguageCompetency are not directly availabe to an agPerson,
   * it is necessary to iterate through the agPerson's agPersonMjAgPersonAgLanguage values
   * and delete their related agPersonLanguageCompetency values before deleting the actual
   * agPersonMjAgPerosnLanguage values.
   *
   * Last to be deleted is the actual agPerson.
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless(
        $ag_staff = Doctrine::getTable('AgStaff')->find($request->getParameter('id')),
        sprintf('Object ag_staff does not exist (%s).', $request->getParameter('id'))
    );

    $ag_person = $ag_staff->getAgPerson();

    foreach ($ag_staff->getAgStaffResource() as $ag_staff_resource_pre_org) {
      foreach ($ag_staff_resource_pre_org->getAgStaffResourceOrganization() as $ag_staff_resource_org) {
        $ag_staff_resource_org->delete();
      }
    }

    foreach ($ag_staff->getAgStaffResource() as $ag_staff_resource) {
      $ag_staff_resource->delete();
    }

    $ag_staff->delete();
    $ag_person->getAgPersonMjAgNationality()->delete();
    $ag_person->getAgPersonDateOfBirth()->delete();
    $ag_person->getAgPersonEthnicity()->delete();
    $ag_person->getAgPersonMaritalStatus()->delete();
    foreach ($ag_person->getAgPersonMjAgLanguage() as $lang) {
      $lang->getAgPersonLanguageCompetency()->delete();
    }
    foreach ($ag_person->getAgEntity()->getAgEntityEmailContact() as $email) {
      $email->delete();
    }
    foreach ($ag_person->getAgEntity()->getAgEntityPhoneContact() as $phone) {
      $phone->delete();
    }
    $ag_person->getAgEntity()->getAgEntityEmailContact()->delete();
    $ag_person->getAgPersonMjAgLanguage()->delete();
    $ag_person->getAgPersonMjAgPersonName()->delete();
    //$ag_person->getAgPersonMjAgPhoneContact()->delete();
    //^This and email below are disabled until we update the deletes to go through entity.
    $ag_person->getAgPersonMjAgProfession()->delete();
    $ag_person->getAgPersonResidentialStatus()->delete();
    $ag_person->getAgPersonSex()->delete();
    //$ag_person->getAgScenarioStaff()->delete();
    //$ag_person->getAgPersonMjAgEmailContact()->delete();
    $ag_person->getAgPersonMjAgReligion()->delete();
    //$ag_person->getAgPersonMjAgSiteContact()->delete();
    foreach ($ag_person->getAgEntity()->getAgEntityAddressContact() as $contact) {
//      foreach($contact->getAgAddress()->getAgAddressMjAgAddressValue() as $addressJoin)
//      {
//        $addressJoin->delete();
//      }
      //$address = $contact->getAgAddress();
      $contact->delete();
      //$address->delete();
    }
    $ent = $ag_person->getAgEntity();
    $ag_person->delete();
    $ent->delete();

    $this->redirect('staff/list');
  }

  /**
   * Processes one of the forms generated in the above actions.
   *
   * @param sfWebRequest $request - A request object
   * @param sfForm $form - A form object
   *
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      //are our values bound at this point?
      // The two lines below are needed to prevent an attempted delete of the agStaff object
      // attached to this person. Symfony/Doctrine seems to see agStaff as a join object in
      // this case, since it holds keys from person and staff status.
      // doSave() has also been overridden in agPersonForm.class.php so that the
      // saveStaffStatusList function is not called. Calling that refreshes the relation
      // and will cause failure on an update of an existing staff.
      //$form->getObject()->clearRelated('agStaffStatus');
      $form->getObject()->clearRelated('agStaff');

      $ag_staff = $form->save();
      $refAgStaff = $ag_staff->getAgStaff();
      $staffObj = agDoctrineQuery::create()
              ->from('agStaff s')
              ->where('s.person_id=?', $ag_staff->id)
              ->fetchOne();
      LuceneRecord::updateLuceneRecord($staffObj);

      //$staff_id = $ag_staff->getAgStaff()->getFirst()->getId();
//not an object? first it's a collection, now not an object if i just get one

      $staff_id = Doctrine::getTable('agStaff')->createQuery('a')
              ->select('a.id')
              ->from('agStaff a')
              ->where('a.person_id = ?', $ag_staff->id)
              ->fetchOne();

      //get id of STAFF person from the saved, extended agpersonform.

      // Check if the Save and Create Another button was used to submit. If it was, redirect to staff/new.
      if($request->getParameter('CreateAnother')) {
        $this->redirect('agStaff/new');
      }
      $this->redirect('agStaff/list');
    }
  }

  /**
   * Export the entire staff list of an Agasti installation as an XLS and offer the option to open the file.
   *
   * @todo: allow export of group selections or search results, implement export for other modules and models.
   * @todo: add some more formatting to the file that is output to make reading easier.
   * */
  public function executeExport()
  {
    $staffMembers = Doctrine::getTable('agPerson')
            ->createQuery('a')
            ->execute();
    $nameTypes = Doctrine::getTable('agPersonNameType')
            ->createQuery('a')
            ->execute();
    $phoneTypes = Doctrine::getTable('agPhoneContactType')
            ->createQuery('a')
            ->execute();
    $emailTypes = Doctrine::getTable('agEmailContactType')
            ->createQuery('a')
            ->execute();
    $addressTypes = Doctrine::getTable('agAddressContactType')
            ->createQuery('a')
            ->execute();
    $languageFormats = Doctrine::getTable('agLanguageFormat')
            ->createQuery('a')
            ->execute();
    require_once 'PHPExcel/Cell/AdvancedValueBinder.php';
    PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());
    $objPHPExcel = new sfPhpExcel();
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    // Set properties
    $objPHPExcel->getProperties()->setCreator("Agasti 2.0");
    $objPHPExcel->getProperties()->setLastModifiedBy("Agasti 2.0");
    $objPHPExcel->getProperties()->setTitle("Staff List");
    // Set default font
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times');
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(12);

    $row = 2;
    foreach ($staffMembers as $staffMember) {
      $headings = array('ID');
      $content = array('ID' => $staffMember->id);

      foreach ($nameTypes as $nameType) {
        $headings[] = ucwords($nameType->person_name_type) . ' Name';
        $j = Doctrine::getTable('AgPersonMjAgPersonName')->findByDql('person_id = ? AND person_name_type_id = ?', array($staffMember->id, $nameType->id))->getFirst();
        $content[ucwords($nameType->person_name_type) . ' Name']
            = ($j ? $j->getAgPersonName()->person_name : '');
      }
      $h = array('Sex', 'Date of Birth', 'Profession', 'Nationality',
        'Ethnicity', 'Religion', 'Marital Status');
      $headings = array_merge($headings, $h);
      foreach ($h as $head) {
        //if($head == 'Date of Birth') $head = 'Person Date Of Birth';
        $table = ($head == 'Date of Birth') ?
            'agPersonDateOfBirth' : 'ag' . str_replace(' ', '', $head);
        //$table = 'ag' . str_replace(' ', '', $head);
        $fetcher = 'get' . ucwords($table);
        $column = sfInflector::underscore(str_replace(' ', '', ucwords($head)));
        $objects = $staffMember->$fetcher();
        $i = 1;
        $c = count($objects);
        $results = null;
        if ($objects instanceof Doctrine_Collection) {
          foreach ($objects as $object) {
            if ($i <> $c) {
              $results = $results . $object->$column . "\n";
            } else {
              $results = $results . $object->$column;
            }
            $i++;
          }
        } else {
          $results = $results . $objects->$column;
        }
        $values[$head] = ucwords($results);
      }
      $content = array_merge($content, $values);
      foreach ($phoneTypes as $phoneType) {
        $headings[] = ucwords($phoneType->phone_contact_type) . ' Phone';
        $j = Doctrine::getTable('AgEntityPhoneContact')
                ->findByDql(
                    'entity_id = ? AND phone_contact_type_id = ?',
                    array($staffMember->entity_id, $phoneType->id)
                )->getFirst();
        $content[ucwords($phoneType->phone_contact_type) . ' Phone'] =
            (
            $j ? preg_replace(
                        $j
                        ->getAgPhoneContact()
                        ->getAgPhoneFormat()
                        ->getAgPhoneFormatType()->match_pattern,
                        $j
                        ->getAgPhoneContact()
                        ->getAgPhoneFormat()
                        ->getAgPhoneFormatType()->replacement_pattern,
                        $j
                        ->getAgPhoneContact()->phone_contact) :
                ''
            );
      }

      foreach ($emailTypes as $emailType) {
        $headings[] = ucwords($emailType->email_contact_type) . ' Email';
        $j = Doctrine::getTable('AgEntityEmailContact')
                ->findByDql(
                    'entity_id = ? AND email_contact_type_id = ?',
                    array($staffMember->entity_id, $emailType->id)
                )
                ->getFirst();
        $content[ucwords($emailType->email_contact_type) . ' Email'] =
            ($j ? $j->getAgEmailContact()->email_contact : '');
      }

      foreach ($addressTypes as $addressType) {
        $headings[] = ucwords($addressType->address_contact_type) . ' Address';
        $j = Doctrine::getTable('AgEntityAddressContact')
                ->findByDql(
                    'entity_id = ? AND address_contact_type_id = ?',
                    array($staffMember->entity_id, $addressType->id)
                )
                ->getFirst();
        $tempContainer = array();
        if ($j) {
          foreach ($j->getAgAddress()->getAgAddressStandard()->getAgAddressFormat()
          as $addressFormat) {
            foreach ($j->getAgAddress()->getAgAddressMjAgAddressValue() as $mj) {
              if ($addressFormat->getAgAddressElement()->id ==
                  $mj->getAgAddressValue()->address_element_id) {
                $i = 1;
                $c = count($j);
                if (isset($tempContainer['Line ' . $addressFormat->line_sequence])) {
                  $tempContainer['Line ' . $addressFormat->line_sequence] =
                      $tempContainer['Line ' . $addressFormat->line_sequence] .
                      $addressFormat->pre_delimiter . $mj->getAgAddressValue()->value .
                      $addressFormat->post_delimiter;
                } else {
                  $tempContainer['Line ' . $addressFormat->line_sequence] =
                      $addressFormat->pre_delimiter .
                      $mj->getAgAddressValue()->value .
                      $addressFormat->post_delimiter;
                }
              }
            }
          }
        } else {
          $tempContainer[ucwords($addressType->address_contact_type) . ' Address'] = null;
        }
        $content[ucwords($addressType->address_contact_type) . ' Address'] =
            implode("\n", $tempContainer);
      }
      $headings[] = 'Language';

      foreach ($languageFormats as $languageFormat) {
        $headings[] = ucwords($languageFormat->language_format);
        $competencies = null;
        $languages = null;
        $c = count($staffMember->getAgPersonMjAgLanguage());
        $i = 1;
        foreach ($staffMember->getAgPersonMjAgLanguage() as $languageJoin) {
          $compQuery = Doctrine::getTable('agPersonLanguageCompetency')->createQuery('a')
                  ->select('a.*')
                  ->from('agPersonLanguageCompetency a')
                  ->where('a.person_language_id = ?', $languageJoin->id)
                  ->andWhere('a.language_format_id = ?', $languageFormat->id);
          if ($i <> $c) {
            $languages = $languages . $languageJoin->getAgLanguage()->language . "\n";
          } else {
            $languages = $languages . $languageJoin->getAgLanguage()->language;
          }
          if ($compEx = $compQuery->fetchOne()) {
            if ($i <> $c) {
              $competencies =
                  $competencies . $compEx->getAgLanguageCompetency()->language_competency . "\n";
            } else {
              $competencies =
                  $competencies . $compEx->getAgLanguageCompetency()->language_competency;
            }
          } else {
            $competencies = $competencies . "\n";
          }
          $i++;
        }
        $content['Language'] = $languages;
        $content[ucwords($languageFormat->language_format)] = $competencies;
      }

      $content['Created'] = $staffMember->created_at;
      $content['Updated'] = $staffMember->updated_at;
      array_push($headings, 'Created', 'Updated');
      foreach ($headings as $hKey => $heading) {
        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hKey, 1)->setValue($heading);
        foreach ($content as $eKey => $entry) {
          if ($eKey == $heading) {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hKey, $row)->setValue($entry);
          }
        }
      }
      $row++;
    }
    //This should be assigning an auto-width to each column that will fit the largest data in it. For some reason, it's not working.
    $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

    for ($i = $highestColumnIndex; $i >= 0; $i--) {
      $objPHPExcel
          ->getActiveSheet()
          ->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))
          ->setAutoSize(true);
    }
    // Save Excel 2007 file

    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
    $todaydate = date("d-m-y");
    $todaydate = $todaydate . '-' . date("H-i-s");
    $filename = 'Staff';
    $filename = $filename . '-' . $todaydate;
    $filename = $filename . '.xls';
    $filePath = realpath(sys_get_temp_dir()) . '/' . $filename;
    $objWriter->save($filePath);
    $foo = new finfo();
    $bar = $foo->file($filePath, FILEINFO_MIME_TYPE);

    $this->getResponse()->setHttpHeader('Content-Type', 'application/vnd.ms-excel');
    $this->getResponse()->setHttpHeader(
        'Content-Disposition', 'attachment;filename="' . $filename . '"'
    );

    $exportFile = file_get_contents($filePath);

    //$this->getResponse()->setContent($objWriter);
    //$this->getResponse()->setContent($exportFile);

    $this->getResponse()->setHeaderOnly(true);
    $this->getResponse()->send();
    $objWriter->save('php://output');
    unlink($filePath);
  }

  /**
   * Imports staff records from a properly formatted XLS file.
   *
   * @todo: define a standard import format and document it for the end user.
   * @todo: make this more robust and create meaningful error messages for failed import fiels and records.
   * */
  public function executeImport2()
  {
    $staffMembers = Doctrine::getTable('agPerson')
            ->createQuery('a')
            ->execute();
    $nameTypes = Doctrine::getTable('agPersonNameType')
            ->createQuery('a')
            ->execute();
    $phoneTypes = Doctrine::getTable('agPhoneContactType')
            ->createQuery('a')
            ->execute();
    $emailTypes = Doctrine::getTable('agEmailContactType')
            ->createQuery('a')
            ->execute();
    $addressTypes = Doctrine::getTable('agAddressContactType')
            ->createQuery('a')
            ->execute();
    $languageFormats = Doctrine::getTable('agLanguageFormat')
            ->createQuery('a')
            ->execute();
    // Right now this is not as robust as possible, only XLS files will be handled. Functionality can be added later for CSV and a few other formats
    // that are supported by PHPExcel. PHPExcel_IOFactory::createReaderForFile is not the issue, it will set the right reader for whatever filetype is imported,
    // it's just set in the if statement below.
    // Set some properties to the imported file's path and file.
    $this->importFile = $_FILES['import']['name'];
    $this->importPath = sfConfig::get('sf_upload_dir') . '/' . $this->importFile;
    $filePath = pathinfo($this->importFile);
    //require_once sfConfig::get('sf_app_dir') . '/lib/util/agStaffImportXls.class.php';
    require_once sfConfig::get('sf_app_lib_dir') . '/util/agStaffImport.class.php';

    $passPath = $_FILES['import']['tmp_name'];
    $extension = strtolower($filePath['extension']);


    if ($extension <> 'xls' && $extension <> 'csv') {
      $this->uploadHeading = 'Import Failure';
      $this->uploadMessage = $this->importFile . ' is not an XLS file and could not be read. No data was imported to Agasti.';
    } else {
//      $returned = shell_exec('php -r "include (\'../apps/frontend/lib/util/agStaffImport.class.php\'); echo staffImport::processStaffImport(\'' . htmlspecialchars($passPath) . '\');"');
      $importObj = new agStaffImport(); //new agStaffImportXls();
      $importObj->fileName = $_FILES['import']['name'];
      $importObj->rowSetIterator = 0;
      $importObj->stop = false;
      $xlsPath = /* 'xlsfile:///' . */ $passPath;
      $returned = $importObj->processStaffImport($xlsPath);
//      $returned = shell_exec('php -r "include (\'../apps/frontend/lib/util/agStaffImport.class.php\'); echo staffImport::processStaffImport(\'' . htmlspecialchars($passPath) . '\', \'' . htmlspecialchars($tyr) . '\');"');
//      $toImport = unserialize($toImport);
      //$returned = unserialize($returned);
      while ($importObj->stop <> true) {
        $returned = $importObj->processStaffImport($xlsPath);
      }
//      while ($returned['Current Iteration' ] < $returned['Max Iteration']) {
//        $this->message = $returned;
//        //$this->redirect('staff/import');
//        //$returned = shell_exec('php -r "include (\'../apps/frontend/lib/util/agStaffImport.class.php\'); echo staffImport::buildAndSave(\'' . addslashes(serialize($returned['Staff'])) . '\', \'' . ($returned['Current Iteration'] + 1) . '\');"');
//        $returned = $importObj->saveImportedStaff($returned['Staff'],$returned['Current Iteration'] + 1);
//
////        $returned = staffImport::buildAndSave(serialize($returned['Staff']),$returned['Current Iteration'] + 1);
////        $returned = unserialize($returned);
//      }
      $this->message = $returned;
    }
  }

  public function executeImport(sfWebRequest $request)
  {
    //$this->forward404Unless($scenarioId = $request->getParameter('scenario_id'));

    $uploadedFile = $_FILES["import"];

    $uploadDir = sfConfig::get('sf_upload_dir') . '/';
    if(!move_uploaded_file($uploadedFile["tmp_name"], $uploadDir . $uploadedFile["name"])) return sfView::ERROR;
    $this->importPath = $uploadDir . $uploadedFile["name"];

    // fires event so listener will process the file (see ProjectConfiguration.class.php)
    $this->dispatcher->notify(new sfEvent($this, 'import.facility_file_ready'));
    // TODO: eventually use this ^^^ to replace this vvv.

    $import = new AgStaffImportXLS();
//    $returned = $import->createTempTable();

    $processedToTemp = $import->processImport($this->importPath);
    $this->numRecordsImported = $import->numRecordsImported;
    $this->events = $import->events;

    // Normalize imported temp data only if import is successful.
    if ($processedToTemp) {
      // Grab table name from AgImportXLS class.
      $sourceTable = $import->tempTable;
      $dataNorm = new agStaffImportNormalization($scenarioId, $sourceTable, 'staff');

      $format = "%d/%m/%Y %H:%M:%S";
//      echo strftime($format);

      $dataNorm->normalizeImport();

      $this->summary = $dataNorm->summary;
//      echo strftime($format);
    }


    //this below block is a bit hard coded and experimental, it should be changed to use gparams

    $agLuceneIndex = new agLuceneIndex('agFacility');
    $indexResult = $agLuceneIndex->indexAll();

//      chdir(sfConfig::get('sf_root_dir')); // Trick plugin into thinking you are in a project directory
//      $dispatcher = sfContext::getInstance()->getEventDispatcher();
//      $task = new luceneReindexTask($dispatcher, new sfFormatter()); //this->dispatcher
//      $task->run(array('model' => 'agFacility'), array('env' => 'all', 'connection' => 'doctrine', 'application' => 'frontend'));
  }

}
