[?php

/**
 * <?php echo $this->getGeneratedModuleName() ?> actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getGeneratedModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: actions.class.php 11340 2008-09-06 07:37:02Z fabien $
 */
class <?php echo $this->getGeneratedModuleName() ?>Actions extends sfActions
{
 

  public function executeIndex(sfWebRequest $request)
  {
  	$this->forward('<?php echo $this->getModuleName(); ?>','list');
  }
  
  public function executeDatagrid($request){
  	$this->datagrid= new sfDatagridPropel('<?php echo $this->getClassName() ?>Datagrid', '<?php echo $this->getClassName() ?>'); 
  	$this->datagrid->keepOnRefresh(true);
  	$this->datagrid->setRowLimit(<?php echo $this->getParameterValue('list.max_per_page', 20) ?>);
  	
  	 <?php foreach($this->getParameterValue('list.order_by_for_filter',array()) as $column=>$order): ?>
    <?php if(is_array($order)): ?>
    $this->datagrid->setOrderByForFilter('<?php echo ($column) ?>',array('<?php echo sfInflector::camelize($order[0]) ?>','<?php echo strtoupper($order[1]) ?>'));
    <?php else: ?>
    $this->datagrid->setOrderByForFilter('<?php echo ($column) ?>',array('<?php echo sfInflector::camelize($order) ?>','ASC'));
    <?php endif; ?>
    <?php endforeach; ?>
    
  	<?php if($this->getParameterValue('list.hide_filters')): ?>
  		$array=array();
  			<?php foreach($this->getParameterValue('list.hide_filters') as $filter): ?>
					$array['<?php echo $filter ?>']='NOTYPE';
  			<?php endforeach; ?>
  			$this->datagrid->setColumnsFilters($array);
  	<?php endif; ?>
  	<?php if($this->getParameterValue('list.columns_sorting')): ?>
  		$array=array();
  		<?php foreach($this->getParameterValue('list.columns_sorting') as $col=>$value): ?>
  		$array['<?php echo $col; ?>']='<?php echo $value; ?>';
  		<?php endforeach; ?>
  		$this->datagrid->setColumnsSorting($array);
  	<?php endif; ?>
  	<?php if($sort=$this->getParameterValue('list.sort')){ ?>
$this->datagrid->setDefaultSortingColumn('<?php echo ($sort[0]) ?>','<?php echo $sort[1] ?>');
  	<?php } ?>
$this->datagrid->setRowAction('<?php echo $this->getModuleName(); ?>/<?php echo $this->getParameterValue('list.row_action', 'edit') ?>?'.<?php echo $this->getMethodParamsForGetOrCreate() ?>.'=', <?php echo $this->getMethodParamsForGetOrCreate() ?>);
  	  
  	<?php $datagrid_actions=$this->getParameterValue('list.batch_actions', array()); ?>
  	<?php if(sizeof($datagrid_actions)>0): ?>
  		<?php foreach($datagrid_actions as  $actionName => $params ){ ?>
$actions[__('<?php echo $params['name']?$params['name']:$actionName ?>')]= '<?php echo $this->getModuleName(); ?>/<?php echo $params['action']?$params['action']:sfInflector::camelize($actionName.'_selected'); ?>';
  		<?php } ?>
$this->datagrid->setDatagridActions($actions); 
  	<?php endif; ?>
  	
$columns=array();
  	<?php if(sizeof($datagrid_actions)>0): ?>
  	<?php $columns['_batch']='batch'; ?>
  	<?php endif; ?>
  	
  	
  	
  	<?php 
  	$tablePeer=$this->getClassName().'Peer';
    $builder=$this->getClassName().'MapBuilder';
    $mapBuilder=new $builder;
    $mapBuilder->doBuild();
  		?>
  	<?php $hs = $this->getParameterValue('list.hide', array()) ?>
	<?php foreach ($this->getColumns('list.display') as $column): ?>
	<?php if (in_array($column->getName(), $hs)) continue ?>
	<?php $credentials = $this->getParameterValue('list.fields.'.$column->getName().'.credentials') ?>
	<?php if ($credentials): $credentials = str_replace("\n", ' ', var_export($credentials, true)) ?>
    	[?php if ($sf_user->hasCredential(<?php echo $credentials ?>)): ?]
    <?php endif; ?>$columns['<?php echo $column->getName(); ?>']= '<?php  echo addslashes($this->getParameterValue('list.fields.'.$column->getName().'.name')) ?>';
	<?php if ($credentials): ?>
	    [?php endif; ?]
	<?php endif; ?>
	<?php endforeach; ?>
	<?php if($this->getParameterValue('list.object_actions')){
  		?> $columns['_object_actions']=__('Actions');
  		 <?php
  	} ?>
	$this->datagrid->setColumns($columns);
$p = $this->datagrid->prepare('<?php echo $this->getParameterValue('list.peer_method','doSelect') ?>', '<?php echo $this->getParameterValue('list.peer_count_method','doCount') ?>');  
$values = array(); 
$defaultValuesId=array(); 
$results=$p->getResults() ;
if(sizeof($results)>0){
foreach($results as $k=>$<?php echo $this->getSingularName() ?>) {
$defaultValuesId[$k]=$<?php echo $this->getSingularName() ?>->getPrimaryKey();
<?php if(sizeof($datagrid_actions)>0): ?>
$values[$k][]=sfDatagrid::getCheck($<?php echo $this->getSingularName() ?>->getPrimaryKey());
	   <?php endif; ?>
	      	<?php foreach ($this->getColumns('list.display') as $column): ?>
			<?php if (in_array($column->getName(), $hs)) continue ?>
			<?php $credentials = $this->getParameterValue('list.fields.'.$column->getName().'.credentials') ?>
			<?php if ($credentials): $credentials = str_replace("\n", ' ', var_export($credentials, true)) ?>
		    	[?php if ($sf_user->hasCredential(<?php echo $credentials ?>)): ?]
		    <?php endif; ?>
		 <?php     if(($column->isForeignKey()))
        { 
  
?>
if(method_exists($<?php echo $this->getSingularName() ?>,'get<?php echo $this->getRelatedClassName($column).'RelatedBy'.ucfirst(sfInflector::camelize($column->getName())) ?>')){
$values[$k][] = $<?php echo $this->getSingularName() ?>->get<?php echo $this->getRelatedClassName($column).'RelatedBy'.ucfirst(sfInflector::camelize($column->getName())) ?>(); 
}else{
$values[$k][] = $<?php echo $this->getSingularName() ?>->get<?php echo $this->getRelatedClassName($column) ?>(); 
}
<?php } else{ ?>
$values[$k][] = <?php echo $this->getColumnListTag($column) ?>; 
<?php } ?>
<?php if ($credentials): ?>
[?php endif; ?]
<?php endif; ?>
	
<?php endforeach; ?>
<?php if($this->getParameterValue('list.object_actions')){
  		?> $values[$k][]=get_partial('<?php echo $this->getModuleName(); ?>/list_td_actions',array('<?php echo $this->getSingularName() ?>'=>$<?php echo $this->getSingularName() ?>)); <?php
  	} ?>	      	
}
$this->datagrid->setRowIndexDefaultValues($defaultValuesId);
     }
      $this->getResponse()->setContent($this->datagrid->getContent($values, array('lt', 'dr'))); 
    
      
    // save page
    if ($this->getRequestParameter('page')) {
        $this->getUser()->setAttribute('page', $this->getRequestParameter('page'), 'sf_admin/<?php echo $this->getSingularName() ?>');
    }
      return sfView::NONE;  
    
  }
  
  public function executeList($request){
  	/* Show the data grid */
  }


  public function executeCreate($request)
  {
    return $this->forward('<?php echo $this->getModuleName() ?>', 'edit');
  }

  public function executeSave($request)
  {
    return $this->forward('<?php echo $this->getModuleName() ?>', 'edit');
  }

<?php $listActions = $this->getParameterValue('list.batch_actions') ?>
<?php if (null !== $listActions): ?>
  public function executeBatchAction($request)
  {
    $action = $this->getRequestParameter('sf_admin_batch_action');
    switch($action)
    {
<?php foreach ((array) $listActions as $actionName => $params): ?>
<?php
// default values
if ($actionName[0] == '_')
{
  $actionName = substr($actionName, 1);
  $name       = $actionName;
  $action     = $actionName;
}
else
{
  $name   = $actionName;
  $action = isset($params['action']) ? $params['action'] : sfInflector::camelize($actionName);
}
?>
      case "<?php echo $name ?>":
        $this->forward('<?php echo $this->getModuleName() ?>', '<?php echo $action ?>');
        break;
<?php endforeach; ?>
    }

    return $this->redirect('<?php echo $this->getModuleName() ?>/list');
  }
<?php endif; ?>

  public function executeDeleteSelected($request)
  {
    $this->selectedItems = $this->getRequestParameter('gridline', array());

    try
    {
      foreach (<?php echo $this->getPeerClassName() ?>::retrieveByPks($this->selectedItems) as $object)
      {
        $object->delete();
      }
    }
    catch (PropelException $e)
    {
      $request->setError('delete', 'Could not delete the selected <?php echo sfInflector::humanize($this->getPluralName()) ?>. Make sure they do not have any associated items.');
      return $this->forward('<?php echo $this->getModuleName() ?>', 'datagrid');
    }

    return $this->redirect('<?php echo $this->getModuleName() ?>/datagrid');
  }

  public function executeEdit($request)
  {
    $this-><?php echo $this->getSingularName() ?> = $this->get<?php echo $this->getClassName() ?>OrCreate();

    if ($request->isMethod('post'))
    {
      $this->update<?php echo $this->getClassName() ?>FromRequest();

      try
      {
        $this->save<?php echo $this->getClassName() ?>($this-><?php echo $this->getSingularName() ?>);
      }
      catch (PropelException $e)
      {
        $request->setError('edit', 'Could not save the edited <?php echo sfInflector::humanize($this->getPluralName()) ?>.');
        return $this->forward('<?php echo $this->getModuleName() ?>', 'list');
      }

      $this->getUser()->setFlash('notice', 'Your modifications have been saved');

      if ($this->getRequestParameter('save_and_add'))
      {
        return $this->redirect('<?php echo $this->getModuleName() ?>/create');
      }
      else if ($this->getRequestParameter('save_and_list'))
      {
        return $this->redirect('<?php echo $this->getModuleName() ?>/list');
      }
      else
      {
        return $this->redirect('<?php echo $this->getModuleName() ?>/edit?<?php echo $this->getPrimaryKeyUrlParams('this->') ?>);
      }
    }
    else
    {
      $this->labels = $this->getLabels();
    }
  }

  public function executeDelete($request)
  {
    $this-><?php echo $this->getSingularName() ?> = <?php echo $this->getPeerClassName() ?>::retrieveByPk(<?php echo $this->getRetrieveByPkParamsForAction(40) ?>);
    $this->forward404Unless($this-><?php echo $this->getSingularName() ?>);

    try
    {
      $this->delete<?php echo $this->getClassName() ?>($this-><?php echo $this->getSingularName() ?>);
    }
    catch (PropelException $e)
    {
      $request->setError('delete', 'Could not delete the selected <?php echo sfInflector::humanize($this->getSingularName()) ?>. Make sure it does not have any associated items.');
      return $this->forward('<?php echo $this->getModuleName() ?>', 'list');
    }

<?php foreach ($this->getColumnCategories('edit.display') as $category): ?>
<?php foreach ($this->getColumns('edit.display', $category) as $name => $column): ?>
<?php $input_type = $this->getParameterValue('edit.fields.'.$column->getName().'.type') ?>
<?php if ($input_type == 'admin_input_file_tag'): ?>
<?php $upload_dir = $this->replaceConstants($this->getParameterValue('edit.fields.'.$column->getName().'.upload_dir')) ?>
      $currentFile = sfConfig::get('sf_upload_dir')."/<?php echo $upload_dir ?>/".$this-><?php echo $this->getSingularName() ?>->get<?php echo $column->getPhpName() ?>();
      if (is_file($currentFile))
      {
        unlink($currentFile);
      }

<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>
    return $this->redirect('<?php echo $this->getModuleName() ?>/list');
  }

  public function handleErrorEdit()
  {
    $this->preExecute();
    $this-><?php echo $this->getSingularName() ?> = $this->get<?php echo $this->getClassName() ?>OrCreate();
    $this->update<?php echo $this->getClassName() ?>FromRequest();

    $this->labels = $this->getLabels();

    return sfView::SUCCESS;
  }

  protected function save<?php echo $this->getClassName() ?>($<?php echo $this->getSingularName() ?>)
  {
    $<?php echo $this->getSingularName() ?>->save();

<?php foreach ($this->getColumnCategories('edit.display') as $category): ?>
<?php foreach ($this->getColumns('edit.display', $category) as $name => $column): $type = $column->getType(); ?>
<?php $name = $column->getName() ?>
<?php if ($column->isPrimaryKey()) continue ?>
<?php $credentials = $this->getParameterValue('edit.fields.'.$column->getName().'.credentials') ?>
<?php $input_type = $this->getParameterValue('edit.fields.'.$column->getName().'.type') ?>
<?php

$user_params = $this->getParameterValue('edit.fields.'.$column->getName().'.params');
$user_params = is_array($user_params) ? $user_params : sfToolkit::stringToArray($user_params);
$through_class = isset($user_params['through_class']) ? $user_params['through_class'] : '';
$remote_column = isset($user_params['related_column']) ? $user_params['related_column'] : '';

?>
<?php if ($through_class): ?>
<?php

$class = $this->getClassName();
$related_class = sfPropelManyToMany::getRelatedClass($class, $through_class, $remote_column);
$related_table = constant(constant($related_class.'::PEER').'::TABLE_NAME');
$middle_table = constant(constant($through_class.'::PEER').'::TABLE_NAME');
$this_table = constant(constant($class.'::PEER').'::TABLE_NAME');

$related_column = sfPropelManyToMany::getRelatedColumn($class, $through_class, $remote_column);
$column = sfPropelManyToMany::getColumn($class, $through_class, $remote_column);

?>
<?php if ($input_type == 'admin_double_list' || $input_type == 'admin_check_list' || $input_type == 'admin_select_list'): ?>
<?php if ($credentials): $credentials = str_replace("\n", ' ', var_export($credentials, true)) ?>
    if ($this->getUser()->hasCredential(<?php echo $credentials ?>))
    {
<?php endif; ?>
      // Update many-to-many for "<?php echo $name ?>"
      $c = new Criteria();
      $c->add(<?php echo constant($through_class.'::PEER') ?>::<?php echo strtoupper($column->getColumnName()) ?>, $<?php echo $this->getSingularName() ?>->getPrimaryKey());
      <?php echo constant($through_class.'::PEER') ?>::doDelete($c);

      $ids = $this->getRequestParameter('associated_<?php echo $name ?>');
      if (is_array($ids))
      {
        foreach ($ids as $id)
        {
          $<?php echo ucfirst($through_class) ?> = new <?php echo $through_class ?>();
          $<?php echo ucfirst($through_class) ?>->set<?php echo $column->getPhpName() ?>($<?php echo $this->getSingularName() ?>->getPrimaryKey());
          $<?php echo ucfirst($through_class) ?>->set<?php echo $related_column->getPhpName() ?>($id);
          $<?php echo ucfirst($through_class) ?>->save();
        }
      }

<?php if ($credentials): ?>
    }
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>
  }

  protected function delete<?php echo $this->getClassName() ?>($<?php echo $this->getSingularName() ?>)
  {
    $<?php echo $this->getSingularName() ?>->delete();
  }

  protected function update<?php echo $this->getClassName() ?>FromRequest()
  {
    $<?php echo $this->getSingularName() ?> = $this->getRequestParameter('<?php echo $this->getSingularName() ?>');

<?php foreach ($this->getColumnCategories('edit.display') as $category): ?>
<?php foreach ($this->getColumns('edit.display', $category) as $name => $column): $type = $column->getType(); ?>
<?php $name = $column->getName() ?>
<?php $input_type = $this->getParameterValue('edit.fields.'.$column->getName().'.type') ?>
<?php if ($column->isPrimaryKey() || $input_type == 'plain') continue ?>
<?php $credentials = $this->getParameterValue('edit.fields.'.$column->getName().'.credentials') ?>
<?php if ($credentials): $credentials = str_replace("\n", ' ', var_export($credentials, true)) ?>
    if ($this->getUser()->hasCredential(<?php echo $credentials ?>))
    {
<?php endif; ?>
<?php if ($input_type == 'admin_input_file_tag'): ?>
<?php $upload_dir = $this->replaceConstants($this->getParameterValue('edit.fields.'.$column->getName().'.upload_dir')) ?>
    $currentFile = sfConfig::get('sf_upload_dir')."/<?php echo $upload_dir ?>/".$this-><?php echo $this->getSingularName() ?>->get<?php echo $column->getPhpName() ?>();
    if (!$this->getRequest()->hasErrors() && isset($<?php echo $this->getSingularName() ?>['<?php echo $name ?>_remove']))
    {
      $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>('');
      if (is_file($currentFile))
      {
        unlink($currentFile);
      }
    }

    if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('<?php echo $this->getSingularName() ?>[<?php echo $name ?>]'))
    {
<?php elseif ($type != PropelColumnTypes::BOOLEAN): ?>
    if (isset($<?php echo $this->getSingularName() ?>['<?php echo $name ?>']))
    {
<?php endif; ?>
<?php if ($input_type == 'admin_input_file_tag'): ?>
<?php if ($this->getParameterValue('edit.fields.'.$column->getName().'.filename')): ?>
      $fileName = "<?php echo str_replace('"', '\\"', $this->replaceConstants($this->getParameterValue('edit.fields.'.$column->getName().'.filename'))) ?>";
<?php else: ?>
      $fileName = md5($this->getRequest()->getFileName('<?php echo $this->getSingularName() ?>[<?php echo $name ?>]').time().rand(0, 99999));
<?php endif ?>
      $ext = $this->getRequest()->getFileExtension('<?php echo $this->getSingularName() ?>[<?php echo $name ?>]');
      if (is_file($currentFile))
      {
        unlink($currentFile);
      }
      $this->getRequest()->moveFile('<?php echo $this->getSingularName() ?>[<?php echo $name ?>]', sfConfig::get('sf_upload_dir')."/<?php echo $upload_dir ?>/".$fileName.$ext);
      $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($fileName.$ext);
<?php elseif ($type == PropelColumnTypes::DATE || $type == PropelColumnTypes::TIMESTAMP): ?>
      if ($<?php echo $this->getSingularName() ?>['<?php echo $name ?>'])
      {
        try
        {
          $dateFormat = new sfDateFormat($this->getUser()->getCulture());
          <?php $inputPattern  = $type == PropelColumnTypes::DATE ? 'd' : 'g'; ?>
          <?php $outputPattern = $type == PropelColumnTypes::DATE ? 'i' : 'I'; ?>
          if (!is_array($<?php echo $this->getSingularName() ?>['<?php echo $name ?>']))
          {
            $value = $dateFormat->format($<?php echo $this->getSingularName() ?>['<?php echo $name ?>'], '<?php echo $outputPattern ?>', $dateFormat->getInputPattern('<?php echo $inputPattern ?>'));
          }
          else
          {
            $value_array = $<?php echo $this->getSingularName() ?>['<?php echo $name ?>'];
            $value = $value_array['year'].'-'.$value_array['month'].'-'.$value_array['day'].(isset($value_array['hour']) ? ' '.$value_array['hour'].':'.$value_array['minute'].(isset($value_array['second']) ? ':'.$value_array['second'] : '') : '');
          }
          $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($value);
        }
        catch (sfException $e)
        {
          // not a date
        }
      }
      else
      {
        $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>(null);
      }
<?php elseif ($type == PropelColumnTypes::BOOLEAN): ?>
    $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>(isset($<?php echo $this->getSingularName() ?>['<?php echo $name ?>']) ? $<?php echo $this->getSingularName() ?>['<?php echo $name ?>'] : 0);
<?php elseif ($column->isForeignKey()): ?>
    $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($<?php echo $this->getSingularName() ?>['<?php echo $name ?>'] ? $<?php echo $this->getSingularName() ?>['<?php echo $name ?>'] : null);
<?php else: ?>
      $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($<?php echo $this->getSingularName() ?>['<?php echo $name ?>']);
<?php endif; ?>
<?php if ($type != PropelColumnTypes::BOOLEAN): ?>
    }
<?php endif; ?>
<?php if ($credentials): ?>
      }
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>
  }

  protected function get<?php echo $this->getClassName() ?>OrCreate(<?php echo $this->getMethodParamsForGetOrCreate() ?>)
  {
    if (<?php echo $this->getTestPksForGetOrCreate() ?>)
    {
      $<?php echo $this->getSingularName() ?> = new <?php echo $this->getClassName() ?>();
    }
    else
    {
      $<?php echo $this->getSingularName() ?> = <?php echo constant($this->getClassName().'::PEER') ?>::retrieveByPk(<?php echo $this->getRetrieveByPkParamsForGetOrCreate() ?>);

      $this->forward404Unless($<?php echo $this->getSingularName() ?>);
    }

    return $<?php echo $this->getSingularName() ?>;
  }

 

  protected function getLabels()
  {
    return array(
<?php foreach ($this->getColumnCategories('edit.display') as $category): ?>
<?php foreach ($this->getColumns('edit.display', $category) as $name => $column): ?>
      '<?php echo $this->getSingularName() ?>{<?php echo $column->getName() ?>}' => '<?php $label_name = str_replace("'", "\\'", $this->getParameterValue('edit.fields.'.$column->getName().'.name')); echo $label_name ?><?php if ($label_name): ?>:<?php endif ?>',
<?php endforeach; ?>
<?php endforeach; ?>
    );
  }
}
