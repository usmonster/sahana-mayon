<h2>Staff Resource Pool</h2> <br>
Your staff resource pool is essentially a set of searches that let you refine who is available to deploy.
<p> On this page you decide what staff to pull from and give them weight.  This is all done through search.</p>
<h3>Existing Saved Searches
<table>
  <thead>
    <tr>
      <th>Search Name</th>
      <th>Search Type</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($saved_searches as $saved_search): ?>
    <tr>
      <td><a href="<?php echo url_for('report/editsearch?id='.$saved_search->getId()) ?>"><?php echo $saved_search->query_name ?></a></td>
      <td><?php echo $ag_facility_group_type->getLuceneSearchType() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include_partial('poolform', array('poolform' => $poolform, 'scenario_id' => $scenario_id)) ?>


  <div id="searchresults">
<?php if(isset($searchquery)){ ?>
<?php include_partial('global/search', array('hits' => $hits, 'searchquery' => $searchquery, 'results' => $results)) ?>
<?php } ?>
  </div>
<p> THIS PORTION BELOW IS NOT FUNCTIONAL AND ONLY FOR PROOF OF CONCEPT


  <?php use_helper('I18N') ?>
  <?php
  $filterForm = new sfForm();
  $filterForm->setWidgets(array(
    'staff_type' => new sfWidgetFormDoctrineChoice(array('model' => 'agStaffResourceType')),
    'organization' => new sfWidgetFormDoctrineChoice(array('model' => 'agOrganization', 'method' => 'getOrganization')),
  ));

  $filterForm->getWidget('staff_type')->setAttribute('style', 'width: 100%');
  $filterForm->getWidget('organization')->setAttribute('style', 'width: 100%');
  ?>
<div style="border: solid 1px #dadada; margin-right: 1em; padding: .5em; -moz-border-radius: 5px;">
  <h3>Filter:</h3>
  <form action="<?php echo url_for('global/search') ?>" method="get" style="display: inline;">
    <label style="font-weight: bold;">Search String:</label>
    <input type="hidden" name="query" value="<?php echo $sf_request->getParameter('query') ?>"/>
    <input type="text" name="filter" value="<?php echo $sf_request->getParameter('filter') ?>" id="search_keywords" class="search" style="margin: auto; vertical-align: middle; display:inline; width: 100%" />

    <label style="font-weight: bold;">Filter By Staff Type:</label>
    <?php echo $filterForm['staff_type']; ?>

    <label style="font-weight: bold;">Filter by Organization:</label>
    <?php echo $filterForm['organization']; ?>


    <input type="submit" value="Filter" class="buttonWhite" />
  </form>
</div>