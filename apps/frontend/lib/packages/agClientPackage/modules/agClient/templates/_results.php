<?php
$ag_person_name_types = Doctrine::getTable('agPersonNameType')
        ->createQuery('b')
        ->execute();
/**
 * what we want to do here is add a check to see if we want to create a new query
 * with our filter(if the data has not been updated) or somehow just filter the
 * $pager / doctrine_collection object
 *
 */
?>
<table class="staffTable">
  <h3>Search Results:
    <?php
    // Output the current staff members being shown, as well total number in the list.
    if (!$pager->count() == 0) {
      echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count();
    } else {
      echo " search query returned no results";
    }
    /**
     * These blocks are for the list sorting, highlight an item if it is the
     * sorted field, indicate in which direction the sort goes
     * params we need to come in:
     *   column being sorted.
     *   column direction.
     *
     * for column highlighting, if there is a large set of columns that might
     * be highlighted, we can use the output buffer to pre-highlight those terms:
     *  ob_start('column_highlight'); something like that.
     */
    $sortColumn = $sf_request->getGetParameter('sort');
    $sortOrder = $sf_request->getGetParameter('order');
    ($sf_request->getGetParameter('filter')) ?
            $filterAppend = '&filter=' . $sf_request->getGetParameter('filter') :
            $filterAppend = '';
    ($sf_request->getGetParameter('sort')) ?
            $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') :
            $sortAppend = '';
    ($sf_request->getGetParameter('order')) ?
            $orderAppend = '&order=' . $sf_request->getGetParameter('order') :
            $orderAppend = '';
    ?>
  </h3>
  <thead>
    <tr class="search">
      <th class="head" rowspan="2"></th>
      <!--we can use an image here for sort icons instead of v and ^,
      this code can also be cleaned up later for re-use -->
      <th class="<?php ($sortColumn == 'person_name') ? print 'headsort' : print 'head' ?>"
          colspan="<?php echo count($ag_person_name_types); ?>" class="staffNameTable">Name</th>
      <th class="<?php ($sortColumn == 'sex') ? print 'headsort' : print 'head' ?>" rowspan="2" >Sex
        <?php
        if ($sortColumn == 'sex' && $sortOrder == 'DESC') {
          echo '<a href="' . url_for('staff/search') . '/page/' .
          $sf_request->getGetParameter('page') .
          '?query=' . $query . $filterAppend .
          '&sort=sex&order=ASC" class="buttonText" title="ascending">
            <img src="' . url_for('images/arrowUpGrey.png') . '" alt="asc" /></a>';
        } else {
          echo '<a href="' . url_for('staff/search') . '/page/' .
          $sf_request->getGetParameter('page') .
          '?query=' . $query . $filterAppend .
          '&sort=sex&order=DESC" class="buttonText" title="descending"><img src="' .
          url_for('images/arrowDownGrey.png') . '" alt="desc" /></a>';
        }
        ?>
      </th>
      <th class="<?php ($sortColumn == 'nationality') ? print 'headsort' : print 'head' ?>"
          rowspan="2">Nationality
            <?php
            if ($sortColumn == 'nationality' && $sortOrder == 'DESC') {
              echo '<a href="' . url_for('staff/search') . '/page/' .
              $sf_request->getGetParameter('page') . '?query=' . $query . $filterAppend .
              '&sort=nationality&order=ASC" class="buttonText" title="ascending"><img src="' .
              url_for('images/arrowUpGrey.png') . '" alt="asc" /></a>';
            } else {
              echo '<a href="' . url_for('staff/search') . '/page/' .
              $sf_request->getGetParameter('page') . '?query=' . $query . $filterAppend .
              '&sort=nationality&order=DESC" class="buttonText" title="descending"><img src="' .
              url_for('images/arrowDownGrey.png') . '" alt="desc" /></a>';
            }
            ?>
      </th>
      <th class="<?php ($sortColumn == 'ethnicity') ? print 'headsort' : print 'head' ?>"
          rowspan="2">Ethnicity
            <?php
            if ($sortColumn == 'ethnicity' && $sortOrder == 'DESC') {
              echo '<a href="' . url_for('staff/search') . '/page/' .
              $sf_request->getGetParameter('page') . '?query=' . $query . $filterAppend .
              '&sort=ethnicity&order=ASC" class="buttonText" title="ascending"><img src="' .
              url_for('images/arrowUpGrey.png') . '" alt="asc" /></a>';
            } else {
              echo '<a href="' . url_for('staff/search') . '/page/' .
              $sf_request->getGetParameter('page') . '?query=' . $query . $filterAppend .
              '&sort=ethnicity&order=DESC" class="buttonText" title="descending"><img src="' .
              url_for('images/arrowDownGrey.png') . '" alt="desc" /></a>';
            }
            ?>
      </th>
      <th class="<?php ($sortColumn == 'language') ? print 'headsort' : print 'head' ?>"
          rowspan="2">Language
            <?php
            if ($sortColumn == 'language' && $sortOrder == 'DESC') {
              echo '<a href="' . url_for('staff/search') . '/page/' .
              $sf_request->getGetParameter('page') . '?query=' . $query . $filterAppend .
              '&sort=language&order=ASC" class="buttonText" title="ascending"><img src="' .
              url_for('images/arrowUpGrey.png') . '" alt="asc" /></a>';
            } else {
              echo '<a href="' . url_for('staff/search') . '/page/' .
              $sf_request->getGetParameter('page') . '?query=' . $query . $filterAppend .
              '&sort=language&order=DESC" class="buttonText" title="descending"><img src="' .
              url_for('images/arrowDownGrey.png') . '" alt="desc" /></a>';
            }
            ?>
      </th>
      <th class="<?php ($sortColumn == 'religion') ? print 'headsort' : print 'head' ?>"
          rowspan="2">Religion
            <?php
            if ($sortColumn == 'religion' && $sortOrder == 'DESC') {
              echo '<a href="' . url_for('staff/search') . '/page/' .
              $sf_request->getGetParameter('page') . '?query=' . $query . $filterAppend .
              '&sort=religion&order=ASC" class="buttonText" title="ascending"><img src="' .
              url_for('images/arrowUpGrey.png') . '" alt="asc" /></a>';
            } else {
              echo '<a href="' . url_for('staff/search') . '/page/' .
              $sf_request->getGetParameter('page') . '?query=' . $query . $filterAppend .
              '&sort=religion&order=DESC" class="buttonText" title="descending"><img src="' .
              url_for('images/arrowDownGrey.png') . '" alt="desc" /></a>';
            }
            ?>
      </th>
    </tr>
    <tr>
      <?php foreach ($ag_person_name_types as $agPersonNameType): ?>
      <?php echo '<th class="subHead">' . ucwords($agPersonNameType) . '</th>' ?>
      <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
    <?php if (!$pager->count() == 0): ?>
    <?php $i = $pager->getFirstIndice(); ?>
    <?php foreach ($pager->getResults() as $staffMember): ?>
                  <tr>
                    <td>
                      <a href="
           <?php echo url_for('staff/show') . '/page/' . $i . '?query=' . $query; ?>"
                  title="View Staff Member <?php echo $staffMember->getId(); ?>"
                  class="linkButton"><?php echo $i++; ?></a>
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
                  $q = Doctrine_Query::create()
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
    <?php endif ?>
                      </tbody>
                    </table>
                    <br />
                    <div class="floatLeft">
                      <a href="<?php echo url_for('staff/new') ?>" class="linkButton"
                         title="Create New Staff Member">Create New</a>
                    </div>
                    <div class="floatRight">
  <?php
//This block creates the navigation links for paginated staff members.
//
//First Page link (or inactive if we're at the first page).
                        if (!$pager->isFirstPage()) {
                          echo '<a href="' . url_for('staff/search') . '/page/' .
                          $pager->getFirstPage() . '?query=' .
                          $query . $sortAppend . $orderAppend .
                          '" class="buttonText" title="First Page">&lt;&lt;</a>';
                        } else {
                          echo '<a class="buttonTextOff">&lt;&lt;</a>';
                        }

//Previous Page link (or inactive if this is the first page).
                        if (!$pager->isFirstPage()) {
                          echo '<a href="' . url_for('staff/search') . '/page/' .
                          $pager->getPreviousPage() . '?query=' .
                          $query . $sortAppend . $orderAppend . '" class="buttonText"
                                title="Previous Page">&lt;</a>';
                        } else {
                          echo '<a class="buttonTextOff">&lt;</a>';
                        }

//Next Page link (or inactive if this is the last page).
                        if (!$pager->isLastPage()) {
                          echo '<a href="' . url_for('staff/search') . '/page/' .
                          $pager->getNextPage() . '?query=' .
                          $query . $sortAppend . $orderAppend . '"
                                class="buttonText" title="Next Page">&gt;</a>';
                        } else {
                          echo '<a class="buttonTextOff">&gt;</a>';
                        }

//Last Page link (or inactive if this is the last page).
                        if (!$pager->isLastPage()) {
                          echo '<a href="' . url_for('staff/search') . '/page/' .
                          $pager->getLastPage() . '?query=' .
                          $query . $sortAppend . $orderAppend . '"
                                class="buttonText" title="Last Page">&gt;&gt;</a>';
                        } else {
                          echo '<a class="buttonTextOff">&gt;&gt;</a>';
                        }
  ?>
</div>


