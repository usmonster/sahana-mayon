<?php use_helper('I18N') ?>
<?php
  $filterForm = new sfForm();
  $choices = array('male' => 'male', 'female' => 'female');
  $filterForm->setWidgets(array(
    'sex'          => new sfWidgetFormChoice(array('multiple' => true, 'choices' => $choices)),
    'nationality'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agNationality')),
    'ethnicity'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEthnicity'  )),
    'religion'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agReligion')),
  ));

 $filterForm->getWidget('sex')->setAttribute('style', 'width: 100%');
 $filterForm->getWidget('nationality')->setAttribute('style', 'width: 100%');
 $filterForm->getWidget('ethnicity')->setAttribute('style', 'width: 100%');
 $filterForm->getWidget('religion')->setAttribute('style', 'width: 100%');
?>
<div class="filterBox">
  <h3>Filter:</h3>
  <form action="<?php echo url_for('staff_search') ?>" method="get" style="display: inline;">
    <label class ="filterButton">Search String:</label>
    <input type="hidden" name="query" value="<?php echo $sf_request->getParameter('query') ?>"/>
    <input type="text" name="filter" value="<?php echo $sf_request->getParameter('filter') ?>" id="search_keywords" class="searchFilter"/>

    <label class ="filterButton">Filter By Sex:</label>
    <?php echo $filterForm['sex']; ?>

    <label class ="filterButton">Filter by Nationality:</label>
    <?php echo $filterForm['nationality']; ?>

    <label class ="filterButton">Filter by Ethnicity:</label>
    <?php echo $filterForm['ethnicity']; ?>

    <label class ="filterButton">Filter by Religion:</label>
    <?php echo $filterForm['religion']; ?>

    <input type="submit" value="Filter" class="buttonWhite" />
  </form>
</div>
