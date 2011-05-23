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
    $rowFormat       = "<li style=\"padding: .5em 0;\"><h4 style=\"display: inline-block; width: 60px; text-align: left;\">%error%%label%:</h4>\n  <div class=\"level2\" style=\"display: inline-block\">%field%%help%\n%hidden_fields%</div></li>\n",
    $errorRowFormat  = "<li>\n%errors%</li>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = "<ul class=\"level2container\" style=\"display: inline; list-style-type: none;\">\n  %content%</ul>";
}