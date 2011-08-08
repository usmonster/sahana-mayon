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

// <-------- CUT HERE -------->
//    $dh = agEventStaffDeploymentHelper::getInstance(6, agEventHandler::EVENT_DEBUG);
//    $results = $dh->test();
//    print_r($results) ;

    // This is to test exporting an xls file without refreshing current html page.
    // These attributes will be used in a new window.  The new window will call the executeExport
    // method to export the xls file.
    $exportFileName = 'ExportFile.xls';
    $this->getUser()->setAttribute('exportFileName', $exportFileName);

    $exportFile = '/home/s/Downloads/Facilities-Hurricane_A-17-06-11-15-21-15.xls';
    $this->getUser()->setAttribute('exportFile', $exportFile);
// <-------- CUT HERE -------->
  }

//  /**
//   * For testing purposes.  This method was to spit out an xls file that was defined in the user's
//   * attributes.
//   */
//  public function executeExport(sfWebRequest $request)
//  {
//    $this->errMsg = NULL;
//
//    if ($this->getUser()->hasAttribute('exportFile') && $this->getUser()->hasAttribute('exportFileName'))
//    {
//      $exportFileName = $this->getUser()->getAttribute('exportFileName');
//      $exportFile = $this->getUser()->getAttribute('exportFile');
//
//      $exportFile = file_get_contents($exportFile);
//
//      $this->getResponse()->setHttpHeader('Content-Type', 'application/vnd.ms-excel');
//      $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment;filename="' . $exportFileName . '"');
//
//      $this->getResponse()->setContent($exportFile);
//      $this->getResponse()->send();
//
////      $this->getUser()->getAttributeHolder()->remove('exportFile');
////      $this->getUser()->getAttributeHolder()->remove('exportFileName');
//    }
//    else
//    {
//      $this->errMsg = 'Error exporting file!';
//    }
//  }

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
