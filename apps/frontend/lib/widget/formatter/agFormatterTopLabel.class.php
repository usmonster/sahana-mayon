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
class agFormatterTopLabel extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<div class=\"geoLine\"><h4 class=\"agFormatterAddressLevelTwo\">%label%</h4><div class=\"agFormatterAddressLevelTwo\">%field%%help%\n%hidden_fields%</div></div>",
    $errorRowFormat  = "<span>%errors%</span>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = "<h4>Geo Data</h4><hr class=\"ruleGray\"/><div>%content%</div>";
}