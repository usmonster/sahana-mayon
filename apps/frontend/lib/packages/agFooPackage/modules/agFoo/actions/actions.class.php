<?php

/**
 * foo actions.
 *
 * @package    AGASTI_CORE
 * @subpackage foo
 * @author     CUNY SPS
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agFooActions extends agActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $this->ag_foos = Doctrine_Core::getTable('agFoo')
            ->createQuery('a')
            ->execute();

    $normObj = new agStaffImportNormalization('temp_staffImport');
    $test = $normObj->testDataNorm();
  }

  public function executeList(sfWebRequest $request)
  {
    /**
     * Query the database for agFacility records joined with
     * agFacilityResource records
     */
    $query = agDoctrineQuery::create()
            ->select('f.*')
            ->from('agFoo f');

    /**
     * Create pager
     */
    $this->pager = new sfDoctrinePager('agFoo', 5);

    /**
     * Check if the client wants the results sorted, and set pager
     * query attributes accordingly
     */
    $this->sortColumn = $request->getParameter('sort') ? $request->getParameter('sort') : 'foo';
    $this->sortOrder = $request->getParameter('order') ? $request->getParameter('order') : 'ASC';

    if ($request->getParameter('sort')) {
      $query = $query->orderBy($request->getParameter('sort', 'foo') . ' ' . $request->getParameter('order', 'ASC'));
    }

    /**
     * Set pager's query to our final query including sort
     * parameters
     */
    $this->pager->setQuery($query);

    /**
     * Set the pager's page number, defaulting to page 1
     */
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();

    $this->setTemplate(sfConfig::get('sf_app_template_dir') . DIRECTORY_SEPARATOR . 'listFoo');
  }

  public function executeShow(sfWebRequest $request)
  {
// <-------- CUT HERE -------->

    $headers = array();

    $q = agDoctrineQuery::create()
      ->select('s.id')
          ->addSelect('p.id')
          ->addSelect('e.id')
          ->addSelect('pn1.person_name AS name1')
          ->addSelect('pn3.person_name AS name3')
        ->from('agStaff AS s')
          ->innerJoin('s.agPerson AS p')
          ->innerJoin('p.agEntity AS e');

    $headers['s_id'] = array('s.id', 'Staff ID');
    $headers['p_id'] = array('p.id', 'Person ID');
    $headers['e_id'] = array('e.id', 'Entity ID');

    // do this to get the ID types ordered property
    $nameHelper = new agPersonNameHelper();
    $nameComponents = $nameHelper->defaultNameComponents;
    unset($nameHelper);

    // do this to get the string types, again ordered properly
    $nameTypes = json_decode(agGlobal::getParam('default_name_components'));

    // loop through each of the name types
    foreach ($nameComponents as $ncIdx => $nc)
    {
      // grab our type id
      $ncId = $nc[0];

      // build the clause strings
      $column = 'pn' . $ncId . '.person_name';
      $select = $column . ' AS name' . $ncId;
      $pmpnJoin = 'p.agPersonMjAgPersonName AS pmpn' . $ncId . ' WITH pmpn' . $ncId .
        '.person_name_type_id = ?';
      $pnJoin = 'pmpn' . $ncId . '.agPersonName AS pn' . $ncId;

      $where = '(' .
        '(EXISTS (' .
          'SELECT sub.id ' .
            'FROM agPersonMjAgPersonName AS sub ' .
            'WHERE sub.person_name_type_id = ? ' .
              'AND sub.person_id = pmpn' . $ncId . '.person_id ' .
            'GROUP BY sub.id ' .
            'HAVING MIN(sub.priority) = pmpn' . $ncId . '.priority' .
          ')) ' .
        'OR (pmpn' . $ncId . '.id IS NULL)' .
        ')';

      // add the clauses to the query
      $q->addSelect($select)
        ->leftJoin($pmpnJoin, $ncId)
        ->leftJoin($pnJoin)
        ->where($where, $ncId);

      // add header information
      $header = 'pn' . $ncId . '_name' . $ncId;
      $headers[$header] = array($column, $nameTypes[$ncIdx][0]);
    }

    

    $results = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    print_r($results) ;
// <-------- CUT HERE -------->

    $this->ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->ag_foo);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agFooForm();
    unset($this->form['created_at'], $this->form['updated_at']);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agFooForm();
    unset($this->form['created_at'], $this->form['updated_at']);
    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id'))), sprintf('Object ag_foo does not exist (%s).', $request->getParameter('id')));
    $this->form = new agFooForm($ag_foo);
    unset($this->form['created_at'], $this->form['updated_at']);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id'))), sprintf('Object ag_foo does not exist (%s).', $request->getParameter('id')));
    $this->form = new agFooForm($ag_foo);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_foo = Doctrine_Core::getTable('agFoo')->find(array($request->getParameter('id'))), sprintf('Object ag_foo does not exist (%s).', $request->getParameter('id')));
    $ag_foo->delete();

    $this->redirect('foo/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $ag_foo = $form->save();

      $this->redirect('foo/edit?id=' . $ag_foo->getId());
    }
  }

}
