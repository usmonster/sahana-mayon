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
class agWidgetFormSchemaFormatterSubContainerLeftLabel extends sfWidgetFormSchemaFormatter
{

  protected
  //"<div class=\"groupLabel\">%label%</div>%error%%field%%help%%hidden_fields%",
  $rowFormat = "<div class=\"rowFormat5\"><div class=\"groupLabel\" class=\"displayInlineBlock margin4Px\">%label%:</div><div class=\"displayInlineBlock\">%error%%field%%help%%hidden_fields%</div></div>\n",
  $errorRowFormat = "<tr><td colspan=\"2\">\n%errors%</td></tr>\n",
  $helpFormat = '<br />%help%',
  $decoratorFormat = "<div class=\"decoratorFormatContent3 floatRight\">\n%content%</div>";

}