<?php

/**
 * Form formatter for rows
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

class agWidgetFormSchemaFormatterRow extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<td>%error%<h5>%label%</h5>%field%%help%\n%hidden_fields%</td>",
    $errorRowFormat  = "<span>\n%errors%</span>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = "<table><tr>\n  %content%</tr></table>";
}

