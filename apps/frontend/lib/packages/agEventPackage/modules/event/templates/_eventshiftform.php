<?php use_stylesheets_for_form($eventshiftform) ?>
<?php use_javascripts_for_form($eventshiftform) ?>
<script type="text/javascript">
     $.get(
    "test1.php",
    { id: $(this).attr('id'), title: $(this).attr('title') },
    function(data) {

        $(this).attr('title', data);
    },
    "text");
</script>
<form name="event_shift_form" id="event_shift_form" action="
<?php
echo url_for('event/shifts/' . ($eventshiftform->getObject()->isNew() ? 'new' : $eventshiftform->getObject()->getId()) .
    (!$eventshiftform->getObject()->isNew() ? '?id=' .
        $eventshiftform->getObject()->getId() : '')) ?>" method="post"
      <?php $eventshiftform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
          <br />

  <?php echo $eventshiftform; ?>

          <br /><br />

          <table>
            <tfoot>
              <tr>
                <td colspan="2">
          <?php echo $eventshiftform->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('event/shifts') ?>" class="linkButton">Back to list</a>
          <?php if (!$eventshiftform->getObject()->isNew()): ?>
            &nbsp;<?php
            echo link_to('Delete', 'event/shifts?id=' . $eventshiftform->getObject()->getId(),
                array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')) ?>
<?php endif; ?>
          <input type="submit" value="Save" class="linkButton" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
