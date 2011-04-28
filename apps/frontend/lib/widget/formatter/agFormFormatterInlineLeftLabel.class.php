<?php

/**
 * Form formatter for inline label left
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Pradeep Vijayagiri, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 * This file is modifed from the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class agFormFormatterInlineLeftLabel extends sfWidgetFormSchemaFormatter
{

  protected
  $rowFormat = "<div style=\"display: inline-block; text-align: center; margin:0px 0px; padding-bottom:10px\">
    <div style=\"color: #848484; display: block; font-weight: bold; padding: 5px 6px; background-color: #dfdfdf; width:100px\">%label%:</div>
    <div style=\"border: 1px solid #ccc; display: block;\">%error%%field%%help%%hidden_fields%</div></div>\n",
  $errorRowFormat = "<span>\n%errors%<span>\n",
  $helpFormat = '<br />%help%',
  $decoratorFormat = "<div class=\"table-container\" style=\"width:600px; overflow:wrap; padding:15px 1px 5px 12px\">%content%</div>";
}