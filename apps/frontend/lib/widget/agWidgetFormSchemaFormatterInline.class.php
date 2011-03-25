<?php

/**
 * <description of what the class does here>
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
class agWidgetFormSchemaFormatterInline extends sfWidgetFormSchemaFormatter
{

  protected
  $rowFormat = "<h5>%label%</h5><span>%error%%field%%help%%hidden_fields%</span>\n",
  $errorRowFormat = "<span>\n%errors%<span>\n",
  $helpFormat = '<br />%help%',
  $decoratorFormat = "<div class=\"decoratorFormat\">\n  %content%</div>";

}