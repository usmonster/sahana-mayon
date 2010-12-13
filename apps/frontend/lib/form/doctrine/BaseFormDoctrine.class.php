<?php

/**
 * Project form base class.
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
abstract class BaseFormDoctrine extends sfFormDoctrine
{

  /**
   * setup() extends the setup() method of sfFormDoctrine
   */
  public function setup()
  {

  }

  /**
   * _formValuesAreBlank() checks for whether the form's field values are blank
   *
   * @return false if any field values are blank, true if none are blank
   * @param $fieldNames array of field names
   * @param $values array of field values corresponding to $fieldNames
   */
  public static function _formValuesAreBlank(array $fieldNames, array $values)
  {
    foreach ($fieldNames as $fieldName) {
      if (isset($values[$fieldName]) && !self::formValueIsBlank($values[$fieldName]))
        return false;
    }

    return true;
  }

  /**
   * formValueIsBlank() determines if a single form value is blank
   *
   * @return false if the form value is blank, true if it is not blank
   * @param $value the value that we want to check; can be a single value or an array
   */
  public static function formValueIsBlank($value)
  {
    if (is_array($value)) {
      foreach ($value as $subValue) {
        if (!self::formValueIsBlank($subValue))
          return false;
      }

      return true;
    }

    return $value ? false : true;
  }

  /**
   * saveEmbeddedForms extends the saveEmbeddedForms method of sfFormDoctrine;
   * Recursively saves all embedded forms, and calls updateLuceneIndex() if the
   * form object is of type agPerson. The last part should be moved out to a
   * better place, per the todo below.
   *
   * @param $con the Doctrine connection
   * @param $forms the embedded forms of the object.
   *
   * @todo The check at the bottom of this function to update the Lucene Index should be moved. Right now it's
   * ensuring that all values are indexed, without it, a person created with one or more new names that aren't
   * in the database won't have those names indexed.
   * Probably can be fixed by overriding saveEmbeddedForms in agPersonForm.
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $con) {
      $con = $this->getConnection();
    }

    if (null === $forms) {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $key => $form) {
      if ($form instanceof sfFormObject) {
        unset($form[self::$CSRFFieldName]);
        if (isset($this->taintedValues[$key])) {
          $form->bindAndSave($this->taintedValues[$key], $this->taintedFiles, $con);
        } else {
          $form->bindAndSave(array(), $this->taintedFiles, $con);
        }
        $form->saveEmbeddedForms($con);
      } else {
        $this->saveEmbeddedForms($con, $form->getEmbeddedForms());
      }
    }
    if ($this->getObject() instanceof agPerson) {
      $this->getObject()->updateLuceneIndex();
    }
  }

}
