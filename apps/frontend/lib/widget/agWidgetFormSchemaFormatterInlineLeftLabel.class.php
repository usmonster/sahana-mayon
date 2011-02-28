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
class agWidgetFormSchemaFormatterInlineLeftLabel extends sfWidgetFormSchemaFormatter
{

  protected
  $rowFormat = "<div class=\"rowFormat\"><div class=\"rowFormatLabel2\">%label%:</div><div class=\"displayInlineBlock\">%error%%field%%help%%hidden_fields%</div></div>\n",
  $errorRowFormat = "<span>\n%errors%<span>\n",
  $helpFormat = '<br />%help%',
  $decoratorFormat = "<div class=\"decoratorFormatContent2\">%content%</div>";
}