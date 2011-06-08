<?php use_stylesheets_for_form($metaForm) ?>
<?php use_javascripts_for_form($metaForm) ?>

<?php
$confirmScript = "";
if($event_name != ""){
  $formAct = url_for('event/meta?event=' . urlencode($sf_data->getRaw('event_name')));

  //we also need to bind the submit button to a confirmation alert
  $confirmScript = ' onclick="return confirm(\'Are you sure?  Changing the date and time of your event will affect the facilities that are not yet staffed.  For more information click Cancel and the help link.\');"';
}
else{
  $formAct = url_for('event/meta');
}
?>

<form action="<?php echo $formAct;?>" method="post"  class="formSmall">
  <table>
    <tfoot>
      <tr>
        <td colspan="2" style="padding-top:10px">
          <input type="submit" value="Save" name="Save" class="continueButton"/>
          <input type="submit" value="Save and Deploy" name ="Deploy" class="continueButton"<?php echo $confirmScript ?>>
          <input type="hidden" value="<?php echo $scenario_id ?>" name="scenario_id">
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $metaForm ?>
      
    </tbody>
  </table>
</form>