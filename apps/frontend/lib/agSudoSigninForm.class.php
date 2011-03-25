<?php

/**
 * Agasti Sudo Signin Form extends the basic sfGuardSigninForm class to allow
 * for a bypass of the normal sfGuard Validator
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
class agSudoSigninForm extends sfGuardFormSignin
{

  /**
   * @todo add description to this function
   * @return a call that sets the validator of the signin form to a custom validator
   */
  public function configure()
  {
    parent::configure();
    $this->validatorSchema->setPostValidator(new sfGuardValidatorCustomUser());
  }

}
