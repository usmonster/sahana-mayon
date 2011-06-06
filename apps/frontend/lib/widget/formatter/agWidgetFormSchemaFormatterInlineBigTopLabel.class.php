<?php

/**
 * Form formatter for label
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
class agWidgetFormSchemaFormatterInlineBigTopLabel extends sfWidgetFormSchemaFormatter
{

  protected
  $rowFormat = "<div class=\"rowFormat\"><div class=\"rowFormatLabel\">%label%</div><div>%field%%help%%hidden_fields%</div></div>\n",
  $errorRowFormat = "",
  $helpFormat = '<br />%help%',
  $decoratorFormat = "<div class=\"inlinetoplabel\">%content%</div>";

}