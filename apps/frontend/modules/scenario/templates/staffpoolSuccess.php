<h2>Staff Resource Pool</h2> <br>
Your staff resource pool is essentially a set of searches that let you refine who is available to deploy.

<?php include_partial('poolform', array('poolform' => $poolform, 'scenario_id' => $scenario_id)) ?>

<p> On this page you decide what staff to pull from and give them weight.  This is all done through search.</p>

<p> THIS PORTION BELOW IS NOT FUNCTIONAL AND ONLY FOR PROOF OF CONCEPT


  <?php use_helper('I18N') ?>
  <?php
  $filterForm = new sfForm();
  $filterForm->setWidgets(array(
    'staff_type' => new sfWidgetFormDoctrineChoice(array('model' => 'agStaffResourceType')),
    'organization' => new sfWidgetFormDoctrineChoice(array('model' => 'agOrganization', 'method' => 'getOrganization')),
    'ethnicity' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEthnicity')),
    'religion' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agReligion')),
  ));

  $filterForm->getWidget('staff_type')->setAttribute('style', 'width: 100%');
  $filterForm->getWidget('organization')->setAttribute('style', 'width: 100%');
  $filterForm->getWidget('ethnicity')->setAttribute('style', 'width: 100%');
  $filterForm->getWidget('religion')->setAttribute('style', 'width: 100%');
  ?>
<div style="border: solid 1px #dadada; margin-right: 1em; padding: .5em; -moz-border-radius: 5px;">
  <h3>Filter:</h3>
  <form action="<?php echo url_for('staff_search') ?>" method="get" style="display: inline;">
    <label style="font-weight: bold;">Search String:</label>
    <input type="hidden" name="query" value="<?php echo $sf_request->getParameter('query') ?>"/>
    <input type="text" name="filter" value="<?php echo $sf_request->getParameter('filter') ?>" id="search_keywords" class="search" style="margin: auto; vertical-align: middle; display:inline; width: 100%" />

    <label style="font-weight: bold;">Filter By Staff Type:</label>
    <?php echo $filterForm['staff_type']; ?>

    <label style="font-weight: bold;">Filter by Organization:</label>
    <?php echo $filterForm['organization']; ?>

    <label style="font-weight: bold;">Filter by Ethnicity:</label>
    <?php echo $filterForm['ethnicity']; ?>

    <label style="font-weight: bold;">Filter by Religion:</label>
    <?php echo $filterForm['religion']; ?>

    <input type="submit" value="Filter" class="buttonWhite" />
  </form>
</div>