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
class agWidgetFormSchemaFormatterNoList extends sfWidgetFormSchemaFormatter
{

  protected
  $rowFormat = "<div style=\"display: inline-block; margin-right: 4px; text-align: center;\"><div style=\"color: #848484; font-weight: bold; display: inline-block; margin: 4px;\">%label%:</div><div style=\"display: inline-block;\">%error%%field%%help%%hidden_fields%</div></div>\n",
  $errorRowFormat = "<span>\n%errors%<span>\n",
  $helpFormat = '<br />%help%',
  $decoratorFormat = "<div style=\"border-bottom: solid 1px #dadada; margin-bottom: 4px; list-style: none;\">%content%</div>";
}