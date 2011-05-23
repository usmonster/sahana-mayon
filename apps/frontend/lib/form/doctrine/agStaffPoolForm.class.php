<?php

/**
 * Agasti ag Staff Pool Form Class - A class to generate either a new staff pool form or an
 * edit staff pool form
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 * */
class agStaffPoolForm extends sfForm
{

  public $search_id;
  public $scenario_id;
  public $staff_gen_id;
  public $sg_values;  //staff generator form posted values
  public $s_values;  //search form posted values

  /**
   *
   * @param integer $staff_gen_id an incoming staff generator id to construct the form
   * @param array $values is used in the event this form is being constructed after a post
   *        for a preview, to set the defaults of the internal forms
   */

  public function __construct($search_id = null, $values = null)
  {
    if ($values != null) {
      $this->sg_values = $values['sg_values'];
      $this->s_values = $values['s_values'];
    } else {
      $this->search_id = $search_id;
    }
    parent::__construct(array(), array(), array());
  }

  /**
   * Configures the form and starts off the embedding chain.
   * */
  public function embedAgSearchForms()
  {
    sfProjectConfiguration::getActive()->loadHelpers(array ('Helper','Url', 'Asset', 'Tag'));
    $this->wikiUrl = url_for('@wiki');
    $this->embedSearchForm();
    $this->embedStaffGeneratorForm();
    $this->getWidgetSchema()->setLabel('staff_generator', false);
    $this->getWidgetSchema()->setLabel('search', false);

    $staffpoolDeco = new agWidgetFormSchemaFormatterInlineTopLabel($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('row', $staffpoolDeco);
    $this->getWidgetSchema()->setFormFormatterName('row');
  }

  /**
   * Embeds the Staff Generator Form
   * */
  public function embedStaffGeneratorForm()
  {
    if (isset($this->search_id)) {
      $staffGenObject = agDoctrineQuery::create()
              ->from('agScenarioStaffGenerator a')
              ->where('a.search_id = ?', $this->search_id) //and scenario id?
              ->execute()->getFirst();
      //if($this->staff_gen_id = $staffGenObject->getId();
    }
    $staffGenForm = new agScenarioStaffGeneratorForm(isset($staffGenObject) ? $staffGenObject : null);

    $staffGenDeco = new agWidgetFormSchemaFormatterRow($staffGenForm->getWidgetSchema());
    $staffGenForm->getWidgetSchema()->addFormFormatter('row', $staffGenDeco);
    $staffGenForm->getWidgetSchema()->setFormFormatterName('row');

    $staffGenForm->setWidget('search_id', new sfWidgetFormInputHidden());
    $staffGenForm->setWidget('scenario_id', new sfWidgetFormInputHidden());
    $staffGenForm->setWidget('search_weight', new sfWidgetFormInputText(array(),array('class' => 'inputGray')));//'Choice(array('choices' => range(0, 10))));

    $staffGenForm->setValidator('search_id', new sfValidatorPass(array('required' => false)));
//    $staffGenForm->setValidator('search_weight', new sfValidatorPass(array('required' => false)));
    $staffGenForm->setValidator('scenario_id', new sfValidatorPass());

    unset($staffGenForm['created_at'], $staffGenForm['updated_at']);

    if (is_array($this->sg_values)) {
      $staffGenForm->setDefault('search_weight', $this->sg_values['search_weight']);
    } elseif(!$this->staff_gen_id) {
      $staffGenForm->setDefault('search_weight', 50);
    }

    /**
     * Set labels on a few fields
     */

    $staffGenForm->getWidgetSchema()->setLabel(
        'search_weight',
        'Search Weight <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:search_weight&do=export_xhtmlbody" class="tooltipTrigger" title="Search Weight">?</a>');

    $this->embedForm('staff_generator', $staffGenForm);
  }

  /**
   * Embeds the Lucene Form
   * */
  public function embedSearchForm()
  {
    if (isset($this->search_id)) {
      $searchObject = agDoctrineQuery::create()
              ->from('agSearch a')
              ->where('a.id = ?', $this->search_id)
              ->execute()->getFirst();
    }
    $searchForm = new agSearchForm(isset($searchObject) ? $searchObject : null);

    //although this is the third time we've made this object, what it's constructed from each time is different
    $searchDeco = new agWidgetFormSchemaFormatterRow($searchForm->getWidgetSchema());
    $searchForm->getWidgetSchema()->addFormFormatter('row', $searchDeco);
    $searchForm->getWidgetSchema()->setFormFormatterName('row');
    $searchForm->setWidget('search_condition', new sfWidgetFormInputHidden());
    $searchForm->getWidgetSchema()->setLabel('search_name', 'Name');
    //$searchForm->getWidgetSchema()->setLabel('search_type_id', 'Search Type');
    $searchForm->setWidget('search_type_id', new sfWidgetFormInputHidden());
    $searchForm->setWidget('search_hash', new sfWidgetFormInputHidden());
    $searchForm->getWidget('search_name')->setAttribute('class', 'inputGray');
    $searchForm->getWidgetSchema()->setLabel(
    'search_name',
    'Name <a href="' . $this->wikiUrl .  '/doku.php?id=tooltip:search_name&do=export_xhtmlbody" class="tooltipTrigger" title="Search Name">?</a>');
    $searchForm->setValidator('search_hash', new sfValidatorPass(array('required' => false)));
    $searchForm->setValidator('search_type_id', new sfValidatorPass(array('required' => false)));

    unset($searchForm['created_at'], $searchForm['updated_at']);
    unset($searchForm['ag_report_list']);

    if (is_array($this->s_values)) {
      $searchForm->setDefault('search_name', $this->s_values['search_name']);
      //$searchForm->setDefault('search_type_id', $this->s_values['search_type_id']);
    }
    $this->embedForm('search', $searchForm);
  }

  /**
   * sets the form up, gives it a name and embeds the forms
   * */
  public function configure()
  {
    $this->widgetSchema->setNameFormat('staff_pool[%s]');
    $this->embedAgSearchForms();
  }

  /**
   * this is the only saving that takes place since the sfForm has nothing but embedded forms
   * @param $con to maintain amorphism this interface is copied
   * @param $forms to maintain amorphism this interface is copied
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (isset($this->embeddedForms['search'])) {
      $form = $this->embeddedForms['search'];
      $values = $this->values['search'];
      $this->saveSearchForm($values);
      unset($this->embeddedForms['search']);
    }

    if (isset($this->embeddedForms['staff_generator'])) {
      $form = $this->embeddedForms['staff_generator'];
      $values = $this->values['staff_generator'];
      $this->saveStaffGenForm($form, $values);
      unset($this->embeddedForms['staff_generator']);
    }
  }

  /**
   * save the embedded search form
   * @param sfForm $form a form to process
   * @param mixed $values a set of values coming from a post
   */
  public function saveSearchForm($values)
  {
    $searchCondition = json_decode($values['search_condition'],true);
    $this->search_id = agSearchHelper::getSearchId(
        $searchCondition,
        TRUE,
        $values['search_name'],
        agStaffGeneratorHelper::getDefaultSearchTypeId());
  }

  /**
   * save the embedded scenario staff generator form
   * @param sfForm $form a form to process
   * @param mixed $values a set of values coming from a post
   */
  public function saveStaffGenForm($form, $values)
  {
    $form->updateObject($values);
    if ($form->getObject()->search_id == null) {
      $form->getObject()->search_id = $this->search_id;
      $form->getObject()->scenario_id = $this->scenario_id;
    }
    $form->getObject()->save();
    //$this->staff_resource_id = $form->getObject()->id;
  }

}

