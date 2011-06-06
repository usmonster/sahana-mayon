<?php
//throw new Exception('Only for test, don\'t forget to remove it!');
include_partial('list', array('list' => $list));

/*
 * $list->ag_persons  = our array of people
 *
 */
$ag_person_name_types = Doctrine::getTable('agPersonNameType')
        ->createQuery('b')
        ->execute();
/* rowspan is going to be the depth of the array
 *
 * colspan is going to be the depth of the name array
 *
 * variable columns are going to
 *  */
$bar = $pager->getNbResults();
$bar = $pager->getClass();
$bat = new $bar();
$tabula = Doctrine_Core::getTable($bar);
foreach ($tabula->getRelations() as $relation) {
  $relationsLevel1[] = $relation->getClass();
}
echo $bar;
echo '<hr class="ruleGray">';
$desired_fields =
    Array(
      'agPersonNameType', 'agPersonSex', 'agPersonMjAgNationality', 'agPersonEthnicity',
      'agPersonMjAgReligion', 'agPersonMjAgLanguage', 'agPersonMjAgPersonName'
);
print_r($relationsLevel1);
$desired_fields = array_diff($desired_fields, $relationsLevel1);
?>
<table class="staffTable">
  <caption>Staff Members
    <?php
// Output the current staff members being shown, as well total number in the list.
    echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count();
    ?>
  </caption>

  <thead>
    <tr class="tableRow">
      <th class="head" rowspan="2"></th>
      <th class="head" colspan="<?php echo count($ag_person_name_types); ?>"
          class="staffNameTable">Name</th>
      <th class="head" rowspan="2">Sex</th>
      <th class="head" rowspan="2">Nationality</th>
      <th class="head" rowspan="2">Ethnicity</th>
      <th class="head" rowspan="2">Language</th>
      <th class="head" rowspan="2">Religion</th>
    </tr>
    <tr>
      <?php foreach ($ag_person_name_types as $agPersonNameType): ?>
      <?php echo '<th class="subHead">' . ucwords($agPersonNameType) . '</th>' ?>
      <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
    <?php $i = $pager->getFirstIndice(); ?>
    <?php foreach ($pager->getResults() as $staffMember): ?>
          <tr>
            <td>
              <a href="<?php echo url_for('staff/show?id=' . $staffMember->getId()); ?>"
                 title="View Staff Member <?php echo $staffMember->getId(); ?>"
                 class="continueButton"><?php echo $i++; ?></a>
            </td>
      <?php
          foreach ($ag_person_name_types as $agPersonNameType) {
            echo "<td>";
            $names = $staffMember->agPersonNameGet();
            foreach ($names as $key => $value) {
              if (strtolower($key) == $agPersonNameType->getPersonNameType()) {
                echo $value;
              }
            }
            echo "</td>";
          }
      ?>

          <td>
        <?php
          $q = agDoctrineQuery::create()
                  ->select('sex_id')
                  ->from('agPersonSex')
                  ->where('person_id = ?', $staffMember->getId());

          $s = $q->execute();
          $sex = $s[0]->getAgSex()->sex;
          if (!$sex == null) {
            echo ucwords($sex);
          }
        ?>
        </td>
        <td>
        <?php
          $nationalities = $staffMember->getAgNationality();
          foreach ($nationalities as $nationality) {
            if ($nationality->getAppDisplay() == 1) {
              echo $nationality . "<br /> ";
            }
          }
        ?>
        </td>
        <td>
        <?php $ethnicities = $staffMember->getAgEthnicity(); ?>
        <?php foreach ($ethnicities as $ethnicity): ?>
        <?php echo $ethnicity . "<br /> "; ?>
        <?php endforeach; ?>
          </td>
          <td>
        <?php $languages = $staffMember->getAgLanguage(); ?>
        <?php foreach ($languages as $language): ?>
        <?php echo $language . "<br /> "; ?>
        <?php endforeach; ?>
            </td>
            <td>
        <?php $religions = $staffMember->getAgReligion(); ?>
        <?php foreach ($religions as $religion): ?>
        <?php echo $religion . "<br /> "; ?>
        <?php endforeach; ?>
              </td>
            </tr>
    <?php endforeach; ?>
              </tbody>
            </table>
            <br />
            <div class="floatLeft">
              <a href="<?php echo url_for('staff/new') ?>" class="continueButton"
                 title="Create New Staff Member">Create New</a>
            </div>
            <div class="floatRight">
  <?php
//This block creates the navigation links for paginated staff members.
//
//First Page link (or inactive if we're at the first page).
                if (!$pager->isFirstPage()) {
                  echo '<a href="' . url_for('staff/list') . '?page=' . $pager->getFirstPage() .
                  '" class="buttonText" title="First Page">&lt;&lt;</a>';
                } else {
                  echo '<a class="buttonTextOff">&lt;&lt;</a>';
                }

//Previous Page link (or inactive if we're at the first page).
                if (!$pager->isFirstPage()) {
                  echo '<a href="' . url_for('staff/list') . '?page=' . $pager->getPreviousPage() .
                  '" class="buttonText" title="Previous Page">&lt;</a>';
                } else {
                  echo '<a class="buttonTextOff">&lt;</a>';
                }

//Next Page link (or inactive if we're at the last page).
                if (!$pager->isLastPage()) {
                  echo '<a href="' . url_for('staff/list') . '?page=' . $pager->getNextPage() .
                  '" class="buttonText" title="Next Page">&gt;</a>';
                } else {
                  echo '<a class="buttonTextOff">&gt;</a>';
                }

//Last Page link (or inactive if we're at the last page).
                if (!$pager->isLastPage()) {
                  echo '<a href="' . url_for('staff/list') . '?page=' . $pager->getLastPage() .
                  '" class="buttonText" title="Last Page">&gt;&gt;</a>';
                } else {
                  echo '<a class="buttonTextOff">&gt;&gt;</a>';
                }
  ?>
</div>
