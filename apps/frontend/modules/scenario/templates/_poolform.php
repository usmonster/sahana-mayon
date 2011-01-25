<?php use_stylesheets_for_form($poolform) ?>
<?php //use_javascript('tooltip');  ?>

<form action="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) ?> " method="post">

  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $poolform->renderHiddenFields(false) ?>
          <input type="submit" value="Save" class="linkButton"/>
          <input type="submit" value="Preview" class="linkButton" name="Preview"/>
          <input type="submit" value="New" class="linkButton"/> <!--this should be used if you are 'editing' a search condition but then want to create a new one, without 'refreshing' the page -->
        </td>
      </tr>
    </tfoot>
    <tbody>
      <tr>
        <td>
          <?php echo $poolform ?>
        </td>
      </tr>
      <tr> 
        <td>

          <h3>Construct Search Conditions:</h3>

          <label style="font-weight: bold;">Filter By Staff Type:</label>
          <?php echo $filterForm['staff_type']; ?>

          <label style="font-weight: bold;">Filter by Organization:</label>          
          <?php echo $filterForm['organization']; ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>




