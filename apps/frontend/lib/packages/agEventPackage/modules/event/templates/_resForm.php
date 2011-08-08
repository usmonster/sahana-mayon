<?php use_stylesheets_for_form($resForm) ?>
<?php use_javascripts_for_form($resForm) ?>

<form action="<?php echo url_for('event/resolution?event=' . urlencode($sf_data->getRaw('event_name'))) ?>" method="post">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="Continue" class="continueButton"/>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $resForm ?>
    </tbody>
  </table>
</form>