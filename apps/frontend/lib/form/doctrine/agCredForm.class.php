<?php

/**
 * extends BaseagAccountForm by allowing binding of 'user' data to an 'account'
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
class agCredForm extends sfGuardPermissionForm
{

  /**
   *
   * @return a configured form object
   */
  public function configure()
  {
    //$mjform = new agAccountMjSfGuardUserForm(new agAccountMjSfGuardUser());
    //$form = new sfGuardPermissionForm();
    //$gform = new sfGuardGroupForm();
    //$mj = $this->object->getAgAccountMjSfGuardUser();
    //unset($mjform['ag_person_id']);
    unset($this['created_at'], $this['updated_at']);//, $this['ag_person_id']);
    //$mjform->setDefault('account_id',$theId);
    //$form->getObject()->id = $mj->object->sf_guard_user_id;
    //$mjform->setDefault('sf_guard_user_id',$theId);
    //$this->embedForm('agAccountMjSfGuardUserForm',$mjform);
//    $this->embedForm('sfGuardPermission', $form);
//    $this->embedForm('sfGuardGroup', $gform);
  }

  /**
   * before the saving actually happens, data is validated:
   * e.g. if i don't put in a username i will be kicked
   * @param $con is the connection doctrine is using
   * @param $forms is a collection of forms the function is working on
   */
//  protected function doSave($con = null)
//  {
//        unset($this['ag_facility_resource_order']);
//    unset($this['ag_facility_resource_list']);
//    parent::doSave($con);
//   //unset some stuff, get the data we need, save our main form
//
//
//  }
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    $forms = $this->embeddedForms;
    foreach ($forms as $form) {
      //create agAccountMjSfGuardUser object, update it... boom.
      if ($form->getName() == 'ag_account_mj_sf_guard_user') {
        $form->getObject()->setAccountId($this->getObject()->getId());
        $form->saveEmbeddedForms($con);
        $form->getObject()->save($con);
      }
      if ($form->getName() == 'sf_guard_user') {
        $form->getObject()->setUsername($this->getObject()->getAccountName());
        //$forms['agAccountMjSfGuardUserForm']->getObject()->setSfGuardUserId($form->getObject()->getId());
        $form->getObject()->save();
        $accountMjUser = new agAccountMjSfGuardUser();
        $accountMjUser->account_id = $this->getObject()->getId();
        $accountMjUser->sf_guard_user_id = $form->getObject()->getId();
        $accountMjUser->save();
      }
    }
  }

}

