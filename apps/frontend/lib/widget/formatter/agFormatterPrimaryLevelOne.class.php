<?php

/**
 * Form formatter for labels
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
class agFormatterPrimaryLevelOne extends sfWidgetFormSchemaFormatter
{

  protected
    $rowFormat       = "<div class=\"agFormatterPrimaryLevelOne\"><h4>\n  %error%%label%</h4>\n<hr class=\"ruleGray\" />\n  <div class=\"here\">%field%%help%\n%hidden_fields%</div>\n</div><br/>\n",
    $errorRowFormat  = "<li>\n%errors%</li>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = "<div class=\"holder\">\n%content%</div>";
}