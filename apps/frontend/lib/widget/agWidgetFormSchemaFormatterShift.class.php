<?php

class agWidgetFormSchemaFormatterShift extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<td>%error%<h5>%label%</h5>%field%%help%\n%hidden_fields%</td>",
    $errorRowFormat  = "<span>\n%errors%</span>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = "<table class='shifts'><tr>\n  %content%</tr></table>";
}

