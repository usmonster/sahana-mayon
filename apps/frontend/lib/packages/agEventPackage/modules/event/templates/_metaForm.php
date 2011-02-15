<?php use_stylesheets_for_form($metaForm) ?>
<?php use_javascripts_for_form($metaForm) ?>
<?php if($event_id != ""){
  $formAct = url_for('event/meta?id=' . $event_id);
}
else{
  $formAct = url_for('event/meta');
}
?>

<form action="<?php echo $formAct; ?>" method="post">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="Continue with Pre-Deployment" class="linkButton"/>
          <input type="hidden" value="<?php echo $scenario_id ?>" name="scenario_id">
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $metaForm ?>
    </tbody>
  </table>
</form>
