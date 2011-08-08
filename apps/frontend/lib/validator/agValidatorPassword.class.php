<?php

/**
 * agValidatorPassword validates a password according to complexity reequirements
 * 
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @package    agasti
 * @subpackage validator
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agValidatorPassword extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * max_length: The maximum length of the string
   *  * min_length: The minimum length of the string
   *
   * Available error codes:
   *
   *  * max_length
   *  * min_length
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    // Initialize parent
    parent::initialize($context);

    // Set default parameters value
    $this->setParameter('min_alpha_chars', 1);
    $this->setParameter('min_upper_alpha_chars', 0);
    $this->setParameter('min_lower_alpha_chars', 0);

    $this->setParameter('min_alpha_numeric_chars', 0);

    $this->setParameter('min_numeric_chars', 1);
    $this->setParameter('min_special_chars', 1);

    $this->setParameter('min_password_length', 8);
    $this->setParameter('max_password_length', 9999);

    $this->setParameter('complexity_error', 'Password is not complex enough');

    // Set parameters
    $this->getParameterHolder()->add($parameters);





//    $this->addMessage('max_length', '"%value%" is too long (%max_length% characters max).');
//    $this->addMessage('min_length', '"%value%" is too short (%min_length% characters min).');
//
//    $this->addOption('max_length');
//    $this->addOption('min_length');
//
//    $this->setOption('empty_value', '');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {

    $matches = array();
    $alpha_char_count = preg_match_all('/[a-zA-Z]/', $value, $matches);
    $upper_alpha_char_count = preg_match_all('/[A-Z]/', $value, $matches);
    $lower_char_count = preg_match_all('/[a-z]/', $value, $matches);
    $number_char_count = preg_match_all('/[\d]/', $value, $matches);
    $alpha_numeric_char_count = preg_match_all('/[a-zA-Z\d]/', $value, $matches);
    $special_char_count = preg_match_all('/[^a-zA-Z\d]/', $value, $matches);

    $pw_length = strlen($value);

    $ret = true;
    if( $alpha_char_count < $this->getParameter('min_alpha_chars') ) {
	$ret = false;
    }

    if( $upper_alpha_char_count < $this->getParameter('min_upper_alpha_chars') ) {
	$ret = false;
    }

    if( $lower_char_count < $this->getParameter('min_lower_alpha_chars') ) {
	$ret = false;
    }

    if( $number_char_count < $this->getParameter('min_numeric_chars') ) {
	$ret = false;
    }

    if( $alpha_numeric_char_count < $this->getParameter('min_alpha_numeric_chars') ) {
	$ret = false;
    }

    if( $special_char_count < $this->getParameter('min_special_chars') ) {
	$ret = false;
    }

    if( $pw_length < $this->getParameter('min_password_length') ) {
	$ret = false;
    }

    if( $pw_length > $this->getParameter('max_password_length') ) {
	$ret = false;
    }

    if( ! $ret ) {
	$error = $this->getParameter('complexity_error');
    }

    return($ret);




//    $clean = (string) $value;
//
//    $length = function_exists('mb_strlen') ? mb_strlen($clean, $this->getCharset()) : strlen($clean);
//
//    if ($this->hasOption('max_length') && $length > $this->getOption('max_length'))
//    {
//      throw new sfValidatorError($this, 'max_length', array('value' => $value, 'max_length' => $this->getOption('max_length')));
//    }
//
//    if ($this->hasOption('min_length') && $length < $this->getOption('min_length'))
//    {
//      throw new sfValidatorError($this, 'min_length', array('value' => $value, 'min_length' => $this->getOption('min_length')));
//    }
//
//    return $clean;
  }



}
