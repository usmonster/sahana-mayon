<?php

/**
 * Form formatter
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
class agWidgetFormSchemaFormatterSubContainerLabel extends sfWidgetFormSchemaFormatter
{

  protected
  $rowFormat = "<div style=\"padding-top:15px;\" class=\"toggleGroup\">[-]</div><div class=\"groupLabel\">%label%</div>%error%%field%%help%%hidden_fields%",
  $errorRowFormat = "<tr><td colspan=\"2\">\n%errors%</td></tr>\n",
  $helpFormat = '<br />%help%',
  $decoratorFormat = "<div class=\"decoratorFormatContent3\">\n%content%</div>";

}