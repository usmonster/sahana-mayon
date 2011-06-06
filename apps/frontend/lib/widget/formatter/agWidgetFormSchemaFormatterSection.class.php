<?php
/**
 * Form formatter for sections
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

class agWidgetFormSchemaFormatterSection extends sfWidgetFormSchemaFormatter
{
//protected
//  $rowFormat       = "<tr background-color: red; border: 1px solid;\"><td>  %error%%label%  %field%%help%\n%hidden_fields%</td></tr>",
//  $errorRowFormat  = "<span>\n%errors%</span>\n",
//  $helpFormat      = '<br />%help%',
//  $decoratorFormat = "<tr background-color: green; border: 1px solid;\"><td>\n  %content%</td></tr>";

  protected
    $rowFormat       = "<div class=infoHolder>\n  <h3>%label%</h3>\n  <div>%error%%field%%help%%hidden_fields%</div>\n</div>\n",
    $errorRowFormat  = "<span>\n%errors%<span>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = "<div>\n  %content%</div>";
}

