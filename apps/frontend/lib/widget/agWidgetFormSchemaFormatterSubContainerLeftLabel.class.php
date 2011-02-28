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
  $rowFormat       = "<div class=\"rowFormat5\"><div class=\"groupLabel\" class=\"displayInlineBlock margin4Px\">%label%:</div><div class=\"displayInlineBlock\">%error%%field%%help%%hidden_fields%</div></div>\n",
    $errorRowFormat  = "<tr><td colspan=\"2\">\n%errors%</td></tr>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = "<div class=\"decoratorFormatContent3 floatRight\">\n%content%</div>";
}