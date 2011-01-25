<?php use_stylesheets_for_form($metaForm) ?>
<?php use_javascripts_for_form($metaForm) ?>

<form action="<?php echo url_for('event/deploy') ?> " method="post">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="Continue with Pre-Deployment" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $metaForm ?>
    </tbody>
  </table>
</form>
