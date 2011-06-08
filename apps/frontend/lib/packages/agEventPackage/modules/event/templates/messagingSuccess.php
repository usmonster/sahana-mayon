<?php
use_javascript('agasti.js');
use_javascript('agastiMessaging.js');
use_javascript('jQuery.fileinput.js');
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>

<h2>Staff Messaging: <span class="highlightedText"><?php echo $event_name; ?> </span></h2>

<p>Use this page to send messages and receive responses to your staff for <span class="highlightedText"><?php echo $event_name; ?></span>.</p>

<table class="blueTable">
    <tr class="head">
        <th class="row1">Steps</th>
        <th>Description</th>
    </tr>
    <tr>
        <td>

            <?php
            $exportUrl = url_for('event/exportcontacts?event=' . urlencode($sf_data->getRaw('event_name')));
            echo link_to('Export Unconfirmed Staff Contact List', $exportUrl, array('class' => 'generalButton'));
            //on click of this button, set the div content to 'exporting data, please wait'
            if (isset($exportComplete)) {
                echo $exportComplete . ' staff records exported, please send this file to your messaging vendor.';
            }
            ?></td>
        <td> description</td>
    </tr>
    <tr>
        <td>

            <span style="display: inline-block; margin: 0px; padding: 0px" >
                <?php
                $importUrl = url_for('event/importreplies?event=' . urlencode($sf_data->getRaw('event_name')));
                echo link_to('Import Staff Responses', $importUrl,
                        array('class' => 'generalButton', 'title' => 'Import Staff', 'id' => 'import'));

                $wikiUrl = url_for('@wiki') . '/doku.php?id=tooltip:staff_import&do=export_xhtmlbody';
                echo link_to('?', $wikiUrl,
                        array('class' => 'tooltipTrigger', 'title' => 'Importing Staff Replies', 'id' => 'import'));
                $foo = new agStaff();
                ?>


            </span></td>
        <td>
            <span id="descText">testing</span>
            <form id="importForm" style="position: relative; display: inline-block" action="
                  <?php echo $importUrl ?>" method="post" enctype="multipart/form-data">
                <div style="position: absolute; top: 0px; left: 0px; width: 250px">
                    <input  style="display: inline-block; color: #848484" class="inputGray" id="show" />
                    <a class="continueButton fileUploadBrowse" style="padding: 5px;">Browse</a>
                </div>

                <input type="file" name="import" id="fileUpload" style="height:25px" />


                <input type="submit" name="submit" value="Submit" class="submitLinkButton" style="position:absolute; top:0px; left: 199px"/>
            </form>
        </td>
    </tr>
    <tr>
        <td>

            <a href="#" class="generalButton">Preview Confirmed Staff Pool</a>
        </td>
        <td> description</td>
    </tr>
    <tr>
        <td>

            <?php
                  $deployUrl = url_for('event/deploystaff?event=' . urlencode($sf_data->getRaw('event_name')));
                  echo link_to('Deploy Staff to Facilities', $deployUrl, array('class' => 'generalButton'));
            ?>
              </td>
              <td> description</td>
          </tr>
          <tr>
              <td>

            <?php
                  echo link_to('Export All Staff Contact List', $exportUrl . '&all', array('class' => 'generalButton'));
                  //on click of this button, set the div content to 'exporting data, please wait'
                  if (isset($exportComplete)) {
                      echo $exportComplete . ' staff records exported, please send this file to your messaging vendor.';
                  }
            ?>
        </td>
        <td> description</td>
    </tr></table>