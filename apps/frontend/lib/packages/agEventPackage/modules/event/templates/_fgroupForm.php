<?php use_stylesheets_for_form($fgroupForm) ?>
<?php use_javascripts_for_form($fgroupForm) ?>

<form action="<?php echo url_for('event/fgroup?id=' . $event_id) ?> " method="post">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="Continue with Pre-Deployment" class="linkButton"/>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $fgroupForm ?>
    </tbody>
  </table>
</form>
