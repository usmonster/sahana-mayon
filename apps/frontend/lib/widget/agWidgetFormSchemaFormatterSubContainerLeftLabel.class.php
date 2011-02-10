<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormSchemaFormatterTable.class.php 5995 2007-11-13 15:50:03Z fabien $
 */
class agWidgetFormSchemaFormatterSubContainerLeftLabel extends sfWidgetFormSchemaFormatter
{
  protected
  //"<div class=\"groupLabel\">%label%</div>%error%%field%%help%%hidden_fields%",
  $rowFormat       = "<div style=\"display: block; margin-right: 4px; text-align: left;\"><div class=\"groupLabel\" style=\"display: inline-block; margin: 4px;\">%label%:</div><div style=\"display: inline-block;\">%error%%field%%help%%hidden_fields%</div></div>\n",
    $errorRowFormat  = "<tr><td colspan=\"2\">\n%errors%</td></tr>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = "<div style=\"margin: 4px 0px; padding: 0; float: right;\">\n%content%</div>";
}