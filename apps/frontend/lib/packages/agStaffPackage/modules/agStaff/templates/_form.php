<?php use_javascript('agasti.js') ?>
<?php use_stylesheets_for_form($form);
use_javascripts_for_form($form);
//include_javascripts_for_form($form);
?>
<form action="<?php echo url_for('agStaff/' . ($form->getObject()->isNew() ? 'create' : 'update') . (!$form->getObject()->isNew() ? '?id=' . $form->getObject()->getAgStaff()->getFirst()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
    <input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
    <div class="fooTable" class="displayInlineBlock">
      <br />
      <h3>Staff Information</h3>
      <div class="infoHolder">
<?php echo $form['staff']['status'] ?>

<?php foreach($form['staff']['type'] as $staff_type_form): ?>
        <div class="displayInline staffCounter">
            <span class="ui-icon ui-icon-circle-minus removeStaffResource floatRight"></span>
    <?php echo $staff_type_form; ?>

        </div>
    <?php endforeach; ?>
      <div class="displayInline staffCounter">
        <a href="#" name="groupStatus" class="includeAndAdd linkText" id="staff_id_1">Add Staff Information</a>
        <script>
          function addStaffResource(num) {
            var r = $.ajax({
              type: 'GET',
              url: '<?php echo url_for('staff/addstaffresource') . '?num=' ?>' + num,
              async: false
            }).responseText;
            return r;
          }
          $().ready(function() {
            $('.linkText').click(function() {
              var passId = '#' + $(this).attr('id');
              var $poster = $(this);
              var resources = $('.staffCounter').length
              $(passId).parent().prepend(addStaffResource(resources) + '<br \>');
            });
            $('.removeStaffResource').click(function() {
              //if there is no id for this record(db_not_exists)
              var passId = '#' + $(this).attr('id');
              var $inputs = $('#myForm :input:hidden');
              //send get/post to call delete
              $(this).parent().remove();
            });

          });

        </script>

      </div>
    </div>
    <div class="clearBoth"> </div>

    <h3>Primary Information</h3>
    <div class="infoHolder">
<?php echo $form['id'] ?>
      <h4><?php echo $form['name']->renderLabel(); ?></h4>
      <div class="clearBoth"> </div>
<?php echo $form['name'] ?>
      <div class="clearBoth"> </div>
      <div class="displayInlineBlock">
        <h4><?php echo $form['ag_sex_list']->renderLabel(); ?></h4>
<?php echo $form['ag_sex_list'] ?>
      </div>
      <div class="displayInlineBlock">
<?php echo $form['date of birth']; ?>
        <script type="text/javascript">
          $(function() {
            $("#dob").datepicker({
              changeMonth: true,
              changeYear: true,
              defaultDate: new Date($("#dob").val()),
              duration: 'fast',
              minDate: -110*365,
              maxDate: 0,
              yearRange: 'c-110:c'
            });
          });
        </script>
      </div>
      <br />
      <br />
      <div class="displayBlock">
        <h4><?php echo $form['languages']->renderLabel(); ?></h4>
<?php echo $form['languages']; ?>
      </div>
      <div class="clearBoth"> </div>
      <br />
      <h3>Contact</h3>
      <div class="infoHolder">
        <fieldset>
          <h3><?php echo $form['email']->renderLabel(); ?></h3>
          <div class="clearBoth"> </div>
<?php echo $form['email'] ?>
          <div class="clearBoth"> </div>
          <h3><?php echo $form['phone']->renderLabel(); ?></h3>
          <div class="clearBoth"> </div>
<?php echo $form['phone'] ?>
        </fieldset>
      </div>
<?php echo $form['_csrf_token'] ?>

      <br />

      <div class="address infoHolder">
        <h3><?php echo $form['address']->renderLabel(); ?></h3>
        <div class="clearBoth"> </div>
<?php echo $form['address']; ?>
      </div>
    </div>
  </div>
  <br />
  &nbsp;<a href="<?php echo url_for('staff/list') ?>" class="linkButton">List</a>
<?php if (!$form->getObject()->isNew()): ?>
      &nbsp;<?php echo link_to('Delete', 'agStaff/delete?id=' . $form->getObject()->getAgStaff()->getFirst()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'deleteButton')) ?>
<?php endif; ?>
  <input type="submit" value="Save" class="linkButton" />
  <input type="submit" value="Save and Create Another" name="CreateAnother" class="linkButton" />
</form>
