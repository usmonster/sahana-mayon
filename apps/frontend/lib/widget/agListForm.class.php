<?php

/**
 * agListForm is a basic list constructing/rendering class, for now it only renders a list.
 */

class agListForm
{

  public static function foolist($sf_request, $title, $columns, $pager)
  {
    $sortColumn = $sf_request->getGetParameter('sort');
    $sortOrder = $sf_request->getGetParameter('order');
    ($sf_request->getGetParameter('filter')) ? $filterAppend = '&filter=' . $sf_request->getGetParameter('filter') : $filterAppend = '';
    ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
    ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

    $thisUrl = url_for('foo/list');

    ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
    ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

    $ascArrow = '&#x25B2;';
    $descArrow = '&#x25BC;';

    $listheader = '<h3>' . $title . '</h3>';
    $listbody = '<table class="staffTable">
  <thead>
    <tr class="head">';
    foreach ($columns as $column => $columnCaption) {

      $listbody .= '  <th>' . $columnCaption['title'];
      if ($columnCaption['sortable']) {
        $listbody .= $sortColumn == $column && $sortOrder == 'ASC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>';
        $listbody .= $sortColumn == $column && $sortOrder == 'DESC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>';
      }
      $listbody .= '</th>';
    }
    $listbody .= ' </tr>
    </thead>
    <tbody>';

    foreach ($pager->getResults() as $result) {

      $listbody .='<tr>
          <td><a class=linkButton href="' . url_for('foo/show?id=' . $result->getId()) . '">' . $result->getId() . '</a></td>';
      $listbody .='<td>' . $result->getFoo() . '</td>';
      $listbody .='<td>' . $result->getBar() . '</td>';
      $listbody .='<td>';
      $comma = 0;
      $listbody .= '</tr>';
    }

    $listbody .='
      </tbody>
      </table>

      <br>
      <div>';
    $listbody .= '<a href="' . url_for('foo/new') . '" class="linkButton" title="Create New Foo">Create New</a>
        </div>';

    $listfoot = '<div class="floatRight">';
//
//First Page link (or inactive if we're at the first page).
    $listfoot .= ( !$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
    $listfoot .= ( !$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
    $listfoot .= ( !$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
    $listfoot .= ( !$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
    $listfoot .= '</div>';

//we should put in some new lines here so that we don't destroy the

    $nice_list = $listheader . $listbody . $listfoot;

    return $nice_list;
  }

  public static function staffpoollist($sf_request, $title, $columns, $pager)
  {
    $sortColumn = $sf_request->getGetParameter('sort');
    $sortOrder = $sf_request->getGetParameter('order');
    ($sf_request->getGetParameter('filter')) ? $filterAppend = '&filter=' . $sf_request->getGetParameter('filter') : $filterAppend = '';
    ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
    ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

    $thisUrl = url_for('facility/list');

    ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
    ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

    $ascArrow = '&#x25B2;';
    $descArrow = '&#x25BC;';

    $listheader = '<h3>' . $title . '</h3>';
    $listbody = '<table class="staffTable">
  <thead>
    <tr class="head">';
    foreach ($columns as $column => $columnCaption) {

      $listbody .= '  <th>' . $columnCaption['title'];
      if ($columnCaption['sortable']) {
        $listbody .= $sortColumn == $column && $sortOrder == 'ASC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>';
        $listbody .= $sortColumn == $column && $sortOrder == 'DESC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>';
      }
      $listbody .= '</th>';
    }
    $listbody .= ' </tr>
    </thead>
    <tbody>';

    foreach ($pager->getResults() as $result) {

      $listbody .='<tr>
          <td><a class=linkButton href="' . url_for('facility/show?id=' . $result->getId()) . '">' . $result->getId() . '</a></td>';
      $listbody .='<td>' . $result->getFacilityCode() . '</td>';
      $listbody .='<td>' . $result->getFacilityName() . '</td>';

      $listbody .='<td>';
      $comma = 0;
      foreach ($result->getAgFacilityResource() as $n) {
        $listbody .= ( $comma++ > 0 ? ', <br />' : '') . ucwords($n->getAgFacilityResourceType()->getFacilityResourceType());
        $listbody .= ( count($result->getAgFacilityResource()) ? ' (' . count($result->getAgFacilityResource()) . ')' : '(None)');
      }

      $listbody .= '</td></tr>';
    }

    $listbody .='
      </tbody>
      </table>

      <br>
      <div>';
    $listbody .= '<a href="' . url_for('facility/new') . '" class="linkButton" title="Create New Facility">Create New</a>
        </div>';

    $listfoot = '<div class="floatRight">';
//
//First Page link (or inactive if we're at the first page).
    $listfoot .= ( !$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
    $listfoot .= ( !$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
    $listfoot .= ( !$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
    $listfoot .= ( !$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
    $listfoot .= '</div>';

//we should put in some new lines here so that we don't destroy the

    $nice_list = $listheader . $listbody . $listfoot;

    return $nice_list;
  }

  public static function facilitylist($sf_request, $title, $columns, $pager, $widget = NULL)
  {
    $sortColumn = $sf_request->getGetParameter('sort');
    $sortOrder = $sf_request->getGetParameter('order');
    ($sf_request->getGetParameter('filter')) ? $filterAppend = '&filter=' . $sf_request->getGetParameter('filter') : $filterAppend = '';
    ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
    ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

    $thisUrl = url_for('facility/list');

    ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
    ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

    $ascArrow = '&#x25B2;';
    $descArrow = '&#x25BC;';

    //$listheader = '<h3>' . $title . '</h3>';


    $listbody = '<table class="staffTable">';
    $listbody .= '<caption>Facilities ' . $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count() . '</caption>';
    $listbody .= '<thead>
    <tr class="head">';
    foreach ($columns as $column => $columnCaption) {

      $listbody .= '  <th>' . $columnCaption['title'];
      if ($columnCaption['sortable']) {
        $listbody .= $sortColumn == $column && $sortOrder == 'ASC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>';
        $listbody .= $sortColumn == $column && $sortOrder == 'DESC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>';
      }
      $listbody .= '</th>';
    }
    $listbody .= ' </tr>
    </thead>
    <tbody>';

    foreach ($pager->getResults() as $result) {

      $listbody .='<tr>
          <td><a class=linkButton href="' . url_for('facility/show?id=' . $result->getId()) . '">' . $result->getId() . '</a></td>';
      //$listbody .='<td>' . $result->getFacilityCode() . '</td>';
      $listbody .='<td>' . $result->getFacilityName() . '</td>';


      $comma = 0;
      $listbody .='<td>';
      foreach ($result->getAgFacilityResource() as $n) {
        $nextrow = ( $comma++ > 0 ? ', <br />' : '') . ucwords($n->getAgFacilityResourceType()->getFacilityResourceType());//  ( $comma++ > 0 ? ', <br />' : '') .
        $listbody .= $nextrow;
        //$listbody .= ( count($result->getAgFacilityResource()) ? ' (' . count($result->getAgFacilityResource()) . ')' : '(None)');
        //$listbody .='<td>' . $result->getFacilityCode() . '</td>';

      }
      $listbody .='</td>';
      
      $comma = 0;
      $listbody .='<td>';
      foreach ($result->getAgFacilityResource() as $n) {

        //$listbody .= ucwords($n->getAgFacilityResourceType()->getFacilityResourceType());
        //$listbody .= ( count($result->getAgFacilityResource()) ? ' (' . count($result->getAgFacilityResource()) . ')' : '(None)');
        $listbody .=  ( $comma++ > 0 ? ', <br />' : '') . $n->getFacilityResourceCode();

      }

        $listbody .='</td>';
      //$listbody .= '</td></tr>';

      $listbody .= '</tr>';
    }

    $listbody .='
      </tbody>
      </table>

      <br>
      <div>';
    $listbody .= '<a href="' . url_for('facility/new') . '" class="linkButton" title="Create New Facility">Create New</a>
        </div>';

    $listfoot = '<div class="floatRight">';
//
//First Page link (or inactive if we're at the first page).
    $listfoot .= ( !$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
    $listfoot .= ( !$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
    $listfoot .= ( !$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
    $listfoot .= ( !$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
    $listfoot .= '</div>';

    $nice_list = $listheader . $listbody . $listfoot;

    return $nice_list;
  }




}

