<?php

/**
 * Form formatter for labels
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 * This file is modifed from the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class agFormatterAddressLevelTwo extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<li class=\"agFormatterAddressLevelTwo\"><h4 class=\"agFormatterAddressLevelTwo\">%error%%label%</h4>\n  <div class=\"agFormatterAddressLevelTwo\">%field%%help%\n%hidden_fields%</div></li>\n",
    $errorRowFormat  = "<li>\n%errors%</li>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = "<ul class=\"agFormatterAddressLevelTwo\">\n  %content%</ul>";

  public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
  {
    if($label === false) {
      $this->rowFormat = str_replace('<h4 class="agFormatterAddressLevelTwo">%error%%label%</h4>', '', $this->rowFormat);
    }
    return strtr($this->getRowFormat(), array(
      '%label%'         => $label,
      '%field%'         => $field,
      '%error%'         => $this->formatErrorsForRow($errors),
      '%help%'          => $this->formatHelp($help),
      '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
    ));
  }
}