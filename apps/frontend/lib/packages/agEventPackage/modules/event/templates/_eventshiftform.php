<?php   use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  ?>

<?php $append = '?shiftid=' . ($eventshiftform->getObject()->isNew() ? 'new' : $eventshiftform->getObject()->getId()) ?>

<form name="event_shift_form" id="event_shift_form" action="
<?php echo url_for('event/shifts?id=' . $event_id) . $append ?>" method="post">

  <?php echo $eventshiftform; ?>

          <br /><br />

<td><a href="<?php echo url_for('event/staffshift?id=' . $event_id) . '?shiftid=' . $eventshiftform->getObject()->getId() ?>" class="linkButton modalTrigger" title="Add Staff to Shift">add staff</a></td>
          <table>
            <tfoot>
              <tr>
                <td colspan="2">
          <?php echo $eventshiftform->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('event/shifts') ?>" class="linkButton">Back to list</a>
          <?php if (!$eventshiftform->getObject()->isNew()): ?>
            &nbsp;<?php
            echo link_to('Delete', 'event/shifts?id=' . $event_id . $append,
                array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton', 'name' => 'Delete')) ?>
<?php endif; ?>
          <input type="submit" value="Save" class="linkButton" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>

<?php echo javascript_include_tag('agModal.js'); ?>
          <?php// echo url_for('scenario/staffpool')?event=' . urlencode($facility['e_event_name']) . '&group=' . urlencode($facility['efg_event_facility_group'])) ?>