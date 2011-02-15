<?php use_stylesheets_for_form($resForm) ?>
<?php use_javascripts_for_form($resForm) ?>

<form action="<?php echo url_for('event/resolution?id=' . $event_id) ?>" method="post">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="Continue" class="linkButton"/>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $resForm ?>
    </tbody>
  </table>
</form>