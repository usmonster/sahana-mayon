<?php

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

