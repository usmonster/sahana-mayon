<?php

/**
 * <description of what the class does here>
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 * This file is modifed from the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class agWidgetFormSchemaFormatterInlineBlock extends sfWidgetFormSchemaFormatter
{

  protected
  //$rowFormat = "<div class=\"rowFormat2\"><span>%error%%field%%help%%hidden_fields%</span></div>\n",
  $rowFormat = "<span class=\"rowFormat3\">%label%</span><div class=\"rowFormat2\"><span>%error%%field%%help%%hidden_fields%</span></div>\n",
  $errorRowFormat = "<span>\n%errors%<span>\n",
  $helpFormat = '<br />%help%',
  $decoratorFormat = "<div class=\"facgroup decoratorFormatContent\">\n  %content%</div>";
}
