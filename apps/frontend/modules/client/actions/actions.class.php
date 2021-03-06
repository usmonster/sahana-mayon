<?php

/**
 * Client Actions extends sfActions
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
class clientActions extends sfActions
{

  /**
   * @param sfWebRequest $request - The function expects a query as pass-in parameter.  It may also
   * accepts one or multiple arrays of ag_nationality.id, ag_ethnicity.id, and/or ag_religion.id.
   *
   * executeSearch processes a search string and returns its results for the staff module.
   * sfDoctrinePager is used to return paginated results.
   * It checks if the query exists, then queries against entries in the agPerson table.
   *
   * $lqResults is processed to get the agPerson IDs from the Lucene Query, and then these IDs
   * are used to construct a Doctrine query that will be used to construct the pagination for
   * showSuccess.php.
   *
   * The final line of this function ($this->query = $query;) sets a query attribute
   * that will be used by _results.php to ensure that pagination links work properly.
   */
  public function executeSearch(sfWebRequest $request)
  {
    ProjectConfiguration::registerZend();
    $query = $request->getParameter('query');

    $originalQuery = $query;
    if (isset($request['nationality']) ||
        isset($request['ethnicity']) ||
        isset($request['religion'])) {
      $query = Zend_Search_Lucene_Search_QueryParser::parse($query);

      if ($query instanceof Zend_Search_Lucene_Search_Query_Insignificant) {
        $query = new Zend_Search_Lucene_Search_Query_Boolean;
      }
      if (isset($request['nationality'])) {
        foreach ($request['nationality'] as $val) {
          $nationalities = Doctrine::getTable('agNationality')
                  ->createQuery('a')
                  ->select('p.nationality')
                  ->from('agNationality p')
                  ->where('p.id = ?', $val)
                  ->execute();
          foreach ($nationalities as $ret) {
            $natSub = new Zend_Search_Lucene_Search_Query_Preprocessing_Term(
                    $ret->nationality, null, 'nationality'
            );
            $query->addSubquery($natSub, true);
          }
        }
      }
      if (isset($request['ethnicity'])) {
        foreach ($request['ethnicity'] as $val) {
          $ethnicities = Doctrine::getTable('agEthnicity')
                  ->createQuery('a')
                  ->select('p.ethnicity')
                  ->from('agEthnicity p')
                  ->where('p.id = ?', $val)
                  ->execute();
          foreach ($ethnicities as $ret) {
            $ethSub = new Zend_Search_Lucene_Search_Query_Preprocessing_Term(
                    $ret->ethnicity, null, 'ethnicity'
            );
            $query->addSubquery($ethSub, true);
          }
        }
      }
      if (isset($request['religion'])) {
        foreach ($request['religion'] as $val) {
          $religions = Doctrine::getTable('agReligion')
                  ->createQuery('a')
                  ->select('p.religion')
                  ->from('agReligion p')
                  ->where('p.id = ?', $val)
                  ->execute();
          foreach ($religions as $ret) {
            $relSub = new Zend_Search_Lucene_Search_Query_Preprocessing_Term(
                    $ret->religion, null, 'religion'
            );
            $query->addSubquery($relSub, true);
          }
        }
      }
    }
    $lqResults = Doctrine_core::getTable('agPerson')->getForLuceneQuery($query);
    $this->pager = new sfDoctrinePager('agPerson', 20);
    foreach ($lqResults as $lqResult) {
      $lqIds[] = $lqResult->getId();
    }
    if (isset($lqIds) && count($lqIds) > 0) {
      $q = Doctrine::getTable('agPerson')
              ->createQuery('a')
              ->select('p.*, ps.*, s.*, pn.*, n.*, e.*, lang.*, religion.*, namejoin.*, name.*,
                nametype.*')
              ->from(
                  'agPerson p, p.agPersonSex ps, ps.agSex s, p.agPersonMjAgNationality pn,
                pn.agNationality n, p.agEthnicity e, p.agLanguage lang, p.agReligion religion,
                p.agPersonMjAgPersonName namejoin, namejoin.agPersonName name,
                name.agPersonNameType nametype'
              )
              ->where('p.id IN (' . implode(',', $lqIds) . ')');

      if ($request->getParameter('sort')) {
        /**
         * @todo include exception handling for a bad sort param
         */
        $this->pager->setQuery(
            $q->orderBy(
                $request->getParameter('sort', 'person_name')
                . ' ' . $request->getParameter('order', 'DESC')));
      } else {
        $this->pager->setQuery($q);
      }
      $this->pager->setPage($request->getParameter('page', 1));
      $this->pager->init();
    }
    $this->query = $originalQuery;
  }

  /**
   * executeIndex is currently used to execute the index action for the staff
   * person module, it incorporates our join query to pull all staff information
   * and passes a 'pager' object to the next step in execution: sfView
   *
   * @param $request the field name for sorting and the sorting order as pass-in parameters.
   *
   */
  public function executeIndex(sfWebRequest $request)
  {
    //do some index stuff here.
  }

  /**
   * @todo detail this function
   * @param sfWebRequest $request
   */
  public function executeList(sfWebRequest $request)
  {
    $query = Doctrine::getTable('agPerson')
            ->createQuery('a')
            ->select('p.*, ps.*, s.*, pn.*, n.*, e.*, lang.*, religion.*, namejoin.*, name.*,
              nametype.*')
            ->from(
                'agPerson p, p.agPersonSex ps, ps.agSex s, p.agPersonMjAgNationality pn,
              pn.agNationality n, p.agEthnicity e, p.agLanguage lang, p.agReligion religion,
              p.agPersonMjAgPersonName namejoin, namejoin.agPersonName name,
              name.agPersonNameType nametype'
    );

    $this->pager = new sfDoctrinePager('agPerson', 20);
    /**
     * @todo include exception handling for a bad sort param
     */
    if ($request->getParameter('sort')) {
      if (substr($request->getParameter('sort'), 0, 11) == 'person_name') {
        $nameId = substr($request->getParameter('sort'), 12);
        $sortOrder = $request->getParameter('order', 'DESC');
        $this->pager->setQuery(
            $query->orderBy('namejoin.person_name_type_id = ' . $nameId .
                ' ' . $sortOrder . ', person_name ' . $sortOrder));
      } else {
        $this->pager->setQuery(
            $query->orderBy($request->getParameter('sort', 'person_name') .
                ' ' . $request->getParameter('order', 'DESC')));
      }
    } else {
      $this->pager->setQuery($query);
    }

    //$sqloutput = $query->getSqlQuery();
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
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
    $query = Doctrine::getTable('agPerson')
            ->createQuery('a')
            ->select(
                'p.*, ps.*, s.*, pn.*, n.*, e.*, lang.*, religion.*, namejoin.*, name.*, nametype.*'
            )
            ->from(
                'agPerson p, p.agPersonSex ps, ps.agSex s, p.agPersonMjAgNationality pn,
                  pn.agNationality n, p.agEthnicity e, p.agLanguage lang, p.agReligion religion,
                  p.agPersonMjAgPersonName namejoin, namejoin.agPersonName name,
                  name.agPersonNameType nametype'
    );

    $this->pager = new sfDoctrinePager('agPerson', 1);

    if ($request['query']) {
      $lqResults = Doctrine_core::getTable('agPerson')->getForLuceneQuery($request['query']);

      $i = 0;
      $lqIds = array();

      foreach ($lqResults as $lqResult) {
        $lqIds[$i] = $lqResult->getId();
        $i++;
      }

      if (count($lqIds) > 0) {
        $q = Doctrine::getTable('agPerson')
                ->createQuery('a')
                ->select('p.*')
                ->from('agPerson p')
                ->where('p.id IN (' . implode(',', $lqIds) . ')');
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
          $this->pager->setQuery(
              $query->orderBy('namejoin.person_name_type_id = ' . $nameId .
                  ' ' . $sortOrder . ', person_name ' . $sortOrder));
        } else {
          $this->pager->setQuery(
              $query->orderBy($request->getParameter('sort', 'person_name') .
                  ' ' . $request->getParameter('order', 'DESC')));
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
  }

  /**
   * Creates a blank agPerson form to create and save a new agPerson/staff-member
   *
   * @param sfWebRequest $request - A request object
   * */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new AgPersonForm();
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

    $this->form = new AgPersonForm();
    $ent = new agEntity();
    $ent->save();
    $this->form->getObject()->setAgEntity($ent);
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
        $ag_person = Doctrine::getTable('AgPerson')->find(array($request->getParameter('id'))),
        sprintf('Object ag_person does not exist (%s).', $request->getParameter('id')));
    $this->form = new AgPersonForm($ag_person);
  }

  /**
   * Processes the form created by executeEdit to update the agPerson/staff-member being edited.
   * @param sfWebRequest $request - This function expects an ag_person.id as a pass-in parameter.
   *
   * */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless(
        $request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless(
        $ag_person = Doctrine::getTable('AgPerson')->find(array($request->getParameter('id'))),
        sprintf('Object ag_person does not exist (%s).', $request->getParameter('id')));
    $this->form = new AgPersonForm($ag_person);

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
        $ag_person = Doctrine::getTable('AgPerson')->find(array($request->getParameter('id'))),
        sprintf('Object ag_person does not exist (%s).', $request->getParameter('id')));
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
    //$ag_person->getAgPersonMjAgPhoneContact()->delete(); This and email below are disabled until we update the deletes to go through entity.
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

    $this->redirect('client/list');
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
    $c = $form->getName();
    $d = $request->getParameter($form->getName());
    $e = $request->getFiles($form->getName());
    if ($form->isValid()) {
      $ag_person = $form->save();

      $this->redirect('client/edit?id=' . $ag_person->getId());
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
    $clientMembers = Doctrine::getTable('agPerson')
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
        $j = Doctrine::getTable(
                'AgPersonMjAgPersonName')->findByDql('person_id = ? AND person_name_type_id = ?',
                array($staffMember->id, $nameType->id))->getFirst();
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
            ($j ?
                preg_replace(
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
  public function executeImport()
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

    $passPath = $_FILES['import']['tmp_name'];
    $extension = strtolower($filePath['extension']);
    if ($extension <> 'xls') {
      $this->uploadHeading = 'Import Failure';
      $this->uploadMessage = $this->importFile . ' is not an XLS file and could not be read. No data was imported to Agasti.';
    } else {
      $returned = shell_exec('php -r "include (\'../apps/frontend/lib/util/import.php\'); echo processStaffImport(\'' . htmlspecialchars($passPath) . '\');"');
//      $returned = shell_exec('php -r "include (\'../apps/frontend/lib/util/import.php\'); echo processStaffImport(\'' . htmlspecialchars($passPath) . '\', \'' . htmlspecialchars($tyr) . '\');"');
//      $toImport = unserialize($toImport);
      //include '../apps/frontend/lib/util/import.php';
      $returned = unserialize($returned);
      while ($returned['Current Iteration' ] < $returned['Max Iteration']) {
        $this->message = $returned;
        //$this->redirect('staff/import');
        $returned = shell_exec('php -r "include (\'../apps/frontend/lib/util/import.php\'); echo buildAndSave(\'' . addslashes(serialize($returned['Staff'])) . '\', \'' . ($returned['Current Iteration'] + 1) . '\');"');
        //$returned = buildAndSave($returned['Staff'],$returned['Current Iteration'] + 1);
        $returned = unserialize($returned);
      }
      $this->message = $returned;
    }
  }

}
