<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('client/' . ($form->getObject()->isNew() ? 'create' : 'update') . (!$form->getObject()->isNew() ? '?id=' . $form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <?php if (!$form->getObject()->isNew()): ?>
    <input type="hidden" name="sf_method" value="put" />
  <?php endif; ?>
    <div class="fooTable displayInlineBlock">
      <br />
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
        <h4><?php echo $form['ag_marital_status_list']->renderLabel(); ?></h4>
        <?php echo $form['ag_marital_status_list']; ?>
      </div>
      <div class="displayInlineBlock">
        <h4><?php echo $form['ag_ethnicity_list']->renderLabel(); ?></h4>
        <?php echo $form['ag_ethnicity_list']; ?>
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
      <div class="displayInlineBlock">
        <h4><?php echo $form['ag_nationality_list']->renderLabel(); ?></h4>
        <?php echo $form['ag_nationality_list'] ?>
      </div>
      <div class="clearBoth"> </div>
      <br />
      <h4><?php echo $form['ag_religion_list']->renderLabel(); ?></h4>
      <div class="clearBoth"> </div>
      <?php echo $form['ag_religion_list'] ?>
        <div class="clearBoth"> </div>
        <br />
        <h4><?php echo $form['ag_profession_list']->renderLabel(); ?></h4>
      <?php echo $form['ag_profession_list']; ?>
        <br />
      </div>
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
      </div>
      <br />
      <h3><?php echo $form['address']->renderLabel(); ?></h3>
      <div class="address infoHolder">
        <h3><?php echo $form['address']->renderLabel(); ?></h3>
    <?php echo $form['address']; ?>
      </div>
      <br />
      &nbsp;<a href="<?php echo url_for('client/list') ?>" class="linkButton">List</a>
  <?php if (!$form->getObject()->isNew()): ?>
          &nbsp;<?php echo link_to('Delete', 'client/delete?id=' . $form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')) ?>
  <?php endif; ?>
  <input type="submit" value="Save" class="linkButton" />
</form>
