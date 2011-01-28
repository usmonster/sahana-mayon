<?php use_stylesheets_for_form($scenarioForm) ?>
<?php use_javascripts_for_form($scenarioForm) ?>
<form action="<?php echo url_for('event/meta') ?> " method="post" name="scenario">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="Go to Pre-Deployment" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $scenarioForm ?>
    </tbody>
  </table>
</form>
