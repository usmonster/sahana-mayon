<?php
/**
 * This class allow to render a datagrid
 * 
 * For the good work on the datagrid, you must enable the following 
 * modules :
 * - Tag
 * - Url
 * - Javascript
 * - Form
 * - I18N => if you use it.
 * 
 * You must enable this module in the 
 * applicationConfiguration class with the following line : 
 * sfLoader::loadHelpers(array('Url', 'Javascript', 'Tag', 'I18N', 'Form'));
 * 
 * @author		David Zeller	<zellerda01@gmail.com>
 */
abstract class sfDatagridFormatter
{
	protected
		// The datagrid container
		$datagridContainer = '%loader%%flash%%pager%%actions%<table border="0" cellspadding="0" cellspacing="1" class="grid">%headers%%filters%%rows%</table>',
		// The datagrid container with a bottom pager
		$datagridContainerPagerBottom = '%loader%%flash%%pager%%actions%<table border="0" cellspadding="0" cellspacing="1" class="grid">%headers%%filters%%rows%</table>%pager_bottom%',
		// The datagrid rowCell
		$datagridRows = '<td %row_options%>%value%</td>',
		// The datagrid headerCell
		$datagridHeaders = '<th %header_options%>%value%</th>',
		// The datagrid headerCell if no sort
		$datagridHeadersNoSort = '<div>%value%</div>',
		// The datagrid pager container
		$datagridPagerContainer = '<table cellspacing="1" cellpadding="0" class="grid-pager"><tr><td valign="middle"><span class="pager">%pager%</span></td><td>%search%</td></table>',
		// The datagrid pager details
		$datagridPager = '%pager%&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;%grid_totals%',
		// The datagrid action bar
		$datagridActions = '<table cellspacing="0" cellpadding="0" class="grid-actions"><tr><td valign="middle" class="left-actions"><span class="pager">%links%</span></td><td align="right" valign="middle" class="right-actions">%actions%</td></table>',
		// To check all for batch_actions
		$datagridCheckAll= '<input type="checkbox" name="check_all" onchange="dg_check_all(this)" />',
		//The formater for onclick 
		$onClick="document.location.href='%url%'";
	protected
		$P_ORDER = 'dg_order',
		$P_SORT = 'dg_sort',
		$P_PAGE = 'dg_page';

	/**
	 * Get the html output for the global datagrid
	 *
	 * @param sfDatagrid $object The datagrid object
	 * @param string $headers The html of the headers
	 * @param string $rows The html of the rows
	 * @param string $actions The html of the actions
	 * @return string The html output of the datagrid
	 * 
	 */
	public function renderDatagrid($object, $headers, $rows, $filters, $pager, $actions,$pager_bottom='')
	{
		$formOptions = array(
						'action' => '',
						'id' => $object->_get('datagridName') . '-form',
						'method' => 'post',
						'onsubmit' => 'return false;'
					   );
		if(sfDatagrid::getConfig('insert_pager_bottom',false)){
			$this->datagridContainer=$this->datagridContainerPagerBottom;
		}
	    
		$js='';
		
		
		return '<form ' . _tag_options($formOptions) . '>' . strtr($this->datagridContainer, array(
			'%flash%' => $this->getFlash('datagrid'),
			'%headers%' => $headers,
			'%rows%' => $rows,
			'%pager%' => $pager,
		    '%pager_bottom%'=> $pager_bottom,
			'%actions%' => $actions,
			'%filters%' => $filters,
            '%loader%' => '<div class="datagrid-loader" id="loader-' . $object->_get('datagridName') . '">' . sfDatagrid::getConfig('text_loading') . '</div>'
		)) . '</form>';
	}
	
	/**
	 * Render the pager bar
	 *
	 * @param sfDatagrid $object The datagrid object
	 * @param string $suffix The url suffix
	 * @return string The html output
	 */
	public function renderPagerBar($object, $suffix)
	{ 
		$datagridName = $object->_get('datagridName');
		$pager = $object->_get('pager');
		$page = $object->_get('page');
		$defaultSort= $object->_get('defaultSort');
		$moduleAction = $object->_get('moduleAction');
		$sortBy = $object->_get('sortBy');
		$sortOrder = $object->_get('sortOrder');
		$request = $object->_get('request');
		$renderSearch = $object->_get('renderSearch');
		$renderPager = $object->_get('renderPager');
		
		$pagerHtml = '';
		$searchHtml = '';
		$gridTotals = '';
		
		$suffixWithSorting = $suffix . '&' . $this->P_SORT . '=' . $sortBy . '&' . $this->P_ORDER . '=' . $sortOrder;
		
		if(sizeof($defaultSort)>0)
		$suffixWithDefaultSorting= $suffix . '&' . $this->P_SORT . '=' . $defaultSort['sort'] . '&' . $this->P_ORDER . '=' . $defaultSort['order'];
		else
		$suffixWithDefaultSorting=$suffixWithSorting;
		if($renderPager)
		{
			if($pager->haveToPaginate())
			{
				$pagerHtml.= $this->traduct(sfDatagrid::getConfig('text_page')) . ' : ';
				
				if($page != 1)
				{
					if($page>2){
						$pagerHtml.= self::link_to_remote(
							'<img src="' . sfDatagrid::getConfig('images_dir') . 'pager-arrow-first.gif" alt="" align="absmiddle" />',
							array(
								'url' => $moduleAction . '?' . $this->P_PAGE . '=' . $pager->getFirstPage() . '&' . $suffixWithSorting,
								'update' => $datagridName,
								'script' => true,
                                'loading' => 'dg_hide_show(\'' . $datagridName . '\')'
								));
					}
					$pagerHtml.= self::link_to_remote(
							'<img src="' . sfDatagrid::getConfig('images_dir') . 'pager-arrow-left.gif" alt="" align="absmiddle" />',
							array(
								'url' => $moduleAction . '?' . $this->P_PAGE . '=' . $pager->getPreviousPage() . '&' . $suffixWithSorting,
								'update' => $datagridName,
								'script' => true,
                                'loading' => 'dg_hide_show(\'' . $datagridName . '\')'
								));
					
				}
				
				foreach($pager->getLinks() as $item)
				{
					if($item == $page)
					{
						$pagerHtml.= self::link_to_remote(
								$item,
								array(
									'url' => $moduleAction . '?' . $this->P_PAGE . '=' . $item . '&' . $suffixWithSorting,
									'update' => $datagridName,
									'script' => true,
                                    'loading' => 'dg_hide_show(\'' . $datagridName . '\')'
									),
								array('class' => 'selected'));
					}
					else
					{
						$pagerHtml.= self::link_to_remote(
								$item,
								array(
									'url' => $moduleAction . '?' . $this->P_PAGE . '=' . $item . '&' . $suffixWithSorting,
									'update' => $datagridName,
									'script' => true,
                                    'loading' => 'dg_hide_show(\'' . $datagridName . '\')'
									));
					}
				}
				
				if($page != $pager->getLastPage())
				{
					
					$pagerHtml.= self::link_to_remote(
							'<img src="' . sfDatagrid::getConfig('images_dir') . 'pager-arrow-right.gif" alt="" align="absmiddle" />',
							array(
								'url' => $moduleAction . '?' . $this->P_PAGE . '=' . $pager->getNextPage() . '&' . $suffixWithSorting,
								'update' => $datagridName,
								'script' => true,
                                'loading' => 'dg_hide_show(\'' . $datagridName . '\')'
								));
					if($page+1!=$pager->getLastPage()){
						$pagerHtml.= self::link_to_remote(
							'<img src="' . sfDatagrid::getConfig('images_dir') . 'pager-arrow-last.gif" alt="" align="absmiddle" />',
							array(
								'url' => $moduleAction . '?' . $this->P_PAGE . '=' . $pager->getLastPage() . '&' . $suffixWithSorting,
								'update' => $datagridName,
								'script' => true,
                                'loading' => 'dg_hide_show(\'' . $datagridName . '\')'
								));
					}
				}
			}
			else
			{
				$pagerHtml.= $this->traduct(sfDatagrid::getConfig('text_page')) . ' 1';
			}
			
			$gridTotals.= $this->traduct(sfDatagrid::getConfig('text_numberofrecords')) . ' : ' . $pager->getNbResults();
		}
		
		$url = $moduleAction . '?' . $this->P_PAGE . '=1&' . $suffixWithSorting;
		$url2 = $moduleAction.'?' . $this->P_PAGE . '=1&'.$suffixWithDefaultSorting;
		if($renderSearch)
		{
			$freeze='false';
			if(sfDatagrid::getConfig('freezepanes',false)==true){
				$freeze='true';
			}
			$searchHtml.= content_tag('button', content_tag('span', $this->traduct(sfDatagrid::getConfig('text_search'))), array('type' => 'button', 'class' => 'button', 'name' => 'search_btn',
			 'onclick' => 'dg_send(\'' . $datagridName . '-form\', \'' . $datagridName . '\', \'search\', \'' . url_for($url) . '\','.$freeze.'); '));
			
			$searchHtml.= content_tag('button', content_tag('span', $this->traduct(sfDatagrid::getConfig('text_reset'))), array('type' => 'button', 'class' => 'button reset',
			 'name' => 'reset_btn', 'onclick' => 'dg_send(\'' . $datagridName . '-form\', \'' . $datagridName . '\', \'reset\', \'' . url_for($url2) . '\','.$freeze.'); '));
	
		}
		
			
		if($renderPager)
		{
			return strtr($this->datagridPagerContainer, array(
						'%pager%' => strtr($this->datagridPager, array('%pager%' => $pagerHtml, '%grid_totals%' => $gridTotals)),
						'%search%' => $searchHtml,
						));
		}
		else
		{
			if($renderSearch)
			{
				return strtr($this->datagridPagerContainer, array(
							'%pager%' => '',
							'%search%' => $searchHtml,
							));
			}
			else
			{
				return '';
			}
		}
	}
	
	/**
	 * Render the actions bar
	 *
	 * @param sfDatagrid $object The datagrid object
	 * @param string $defaultUrl The default Url
	 * @return string The html output
	 */
	public function renderActionsBar($object, $defaultUrl)
	{
		
		$datagridName = $object->_get('datagridName');
		$actions = $object->_get('datagridActions');
		$keepRefresh = $object->_get('refreshKeeping');
		
		$linksHtml = '';
		$actionSelect = '';
		
		if(count($actions) != 0)
		{
			$actionSelect.= select_tag('actions', array_flip($actions), array('id' => $datagridName . '_select'));
			$actionSelect.= '&nbsp';
			$actionSelect.= '<input type="button" name="actions" value="' . $this->traduct(sfDatagrid::getConfig('text_validate')) . '" onclick="dg_send(\'' . $datagridName . '-form\', \'' . $datagridName . '\', \'action\', \'\')" />';
		}
		
		if($keepRefresh){
			
			$linksHtml.= self::link_to_remote($this->traduct(sfDatagrid::getConfig('text_defaultview')), array('url' => $defaultUrl . '&d_clear=1', 'update' => $datagridName, 'loading' => 'dg_hide_show(\'' . $datagridName . '\')'));
		}
		
		if($actionSelect == '')
		{
			$actionSelect = '&nbsp;';
		}
		
		return strtr($this->datagridActions, array(
			'%links%' => $linksHtml,
			'%actions%' => $actionSelect
		));
	}
	
	/**
	 * Render the column headers
	 *
	 * @param sfDatagrid $object The datagrid object
	 * @param string $url The pre url for column sort
	 * @return string The html output
	 */
	public function renderHeaders($object, $url)
	{
		$columns = $object->_get('columns');
		$columnsOptions = $object->_get('columnsOptions');
		$columnsSorting = $object->_get('columnsSort');
		$datagridName = $object->_get('datagridName');
		$sortBy = $object->_get('sortBy');
		$sortOrder = $object->_get('sortOrder');
		$actions = $object->_get('datagridActions');
		
		$htmlOutput = '';
		$template = '';
		
		if(count($actions) != 0)
		{
			$htmlOutput.= content_tag('th', $this->datagridCheckAll, array('style' => 'width: 30px; text-align:center'));
		}
		
		foreach($columns as $key => $label)
		{
			if(!array_key_exists($key, $columnsOptions))
			{
				$columnsOptions[$key] = array();
			}
			
			if(!array_key_exists($key, $columnsSorting))
			{
				$columnsSorting[$key] = '';
			}

			if($columnsSorting[$key] == 'nosort')
			{
				$htmlOutput.= strtr($this->datagridHeaders, array(
					'%value%' => strtr($this->datagridHeadersNoSort, array('%value%' => $label)),
					'%header_options%' => _tag_options($columnsOptions[$key])
				));
			}
			else
			{
				if($key == $sortBy)
				{
					if($sortOrder == 'asc')
					{
						$order = 'desc';
					}
					else
					{
						$order = 'asc';
					}
				}
				else
				{
					$order = 'asc';
				}
				
				$htmlOutput.= strtr($this->datagridHeaders, array(
					'%value%' => $this->getSortingArrow($sortBy, $sortOrder, $key) . self::link_to_remote($label, array('update' => $datagridName, 'url' => $url . '&' . $this->P_SORT . '=' . $key . '&' . $this->P_ORDER . '=' . $order, 'script' => true, 'loading' => 'dg_hide_show(\'' . $datagridName . '\')'), $this->isSorting($sortBy, $key)),
					'%header_options%' => _tag_options($columnsOptions[$key])
				));
			}
		}
		
		return content_tag('tr', $htmlOutput);
	}
	
	/**
	 * Render the filter bar
	 *
	 * @param sfDatagrid $object The datagrid object
	 * @param string $suffix The url suffix
	 * @return string The html output
	 */
	public function renderFilters($object, $suffix)
	{		
		$columns = array_keys($object->_get('columns'));
		$columnsOptions = $object->_get('columnsOptions');
		$filtersTypes = $object->_get('filtersTypes');
		$actions = $object->_get('datagridActions');
		$values = $object->_get('search');
		$datagridName = $object->_get('datagridName');
		
		$filterHtml = '';
		
		if(count($actions) != 0)
		{
			$filterHtml.= content_tag('td', '&nbsp;', array('style' => 'width: 30px;', 'class' => 'filter', 'valign' => 'top'));
		}
		
		foreach($columns as $column)
		{
			if(array_key_exists($column, $columnsOptions))
			{
				$options = array_merge(array('class' => 'filter', 'valign' => 'top'), $columnsOptions[$column]);
			}
			else
			{
				$options = array('class' => 'filter', 'valign' => 'top');
			}
			
			if(array_key_exists($column, $filtersTypes))
			{
				$type = $filtersTypes[$column];
			}
			else
			{
				$type = 'VARCHAR';
			}
			
			$filterHtml.= content_tag('td', $this->getInputFilter($type, $column, @$values[$column], $object, $suffix), $options);
		}
		
		return $filterHtml;
	}
	
	
	
	
	/**
	 * Check if it's the sorting column
	 *
	 * @param string $sortBy The column now sorting
	 * @param string $column The column name
	 * @return array The array with the class parameter if it's the sorting column
	 */
	protected function isSorting($sortBy, $column)
	{
		if($sortBy == $column)
		{
			return array('class' => 'sorting');
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * Get the image arrow for sorting indication
	 *
	 * @param string $sortBy The column now sorting
	 * @param string $sortOrder Asc or desc
	 * @param string $column The column name
	 * @return string The html of the image
	 */
	protected function getSortingArrow($sortBy, $sortOrder, $column)
	{
		$html = '';
		/* On a localhost with alias the arrow not appear so we must set the full path */
		 $request = sfContext::getInstance()->getRequest();
  		 $sf_relative_url_root = $request->getRelativeUrlRoot();
		if ($column == $sortBy){
			
			$html = '<span><img src="' .$sf_relative_url_root.sfDatagrid::getConfig('images_dir') . 'header-arrow-' . $sortOrder . '.gif" alt="" /></span>';
		}
		
		return $html;
	}
	
	/**
	 * Get the html output for the row
	 *
	 * @param sfDatagrid $object The datagrid object
	 * @param array $rowValues The array with the values
	 * @param string $rowClass The css class for the row
	 * @param string $rowIndexDefaultValue The RowIndex Default if ! $rowIndex
	 * @return string The html output for the row
	 */
	public function renderRow($object, $rowValues, $rowClass = null,$rowIndexDefaultValue=null)
	{ 
		$columns = array_keys($object->_get('columns'));
		$rowOptions = $object->_get('columnsOptions');
		$rowAction = $object->_get('rowAction');
		$actions = $object->_get('datagridActions');
		
		//
		$filtersTypes = $object->_get('filtersTypes');
		
		$htmlOutput = '';
		$columnIncrement = 0;
		
		if(is_array($rowValues))
		{
			if(count($actions) != 0)
			{
				$firstColumn = array_shift($rowValues);
				$htmlOutput.= content_tag('td', $firstColumn, array('style' => 'width: 30px', 'align' => 'center'));
			}
			
			preg_match('/%(?P<param>\w+)%/', $rowAction, $matches);
					
			$rowIndex = array_search($matches['param'], $columns);
			$link=url_for(strtr($rowAction, array('%' . $matches['param'] . '%' => $rowIndex === false ? $rowIndexDefaultValue : $rowValues[$rowIndex])));
			foreach($rowValues as $value)
			{
				$columnName = $columns[$columnIncrement];
				
				if(!is_null($rowAction) &&($columnName!='_object_actions'))
				{
					
					if($rowIndex === false){
						if(!is_null($rowIndexDefaultValue)){
							$this->addOption('onclick', $rowOptions[$columnName], strtr($this->onClick,array('%url%'=>$link)));
							$this->addOption('style', $rowOptions[$columnName], 'cursor:pointer;');
						}else{
							
							throw new Exception("Impossible to find column ".$matches['param']);
						}
					}else{
						$this->addOption('onclick', $rowOptions[$columnName], strtr($this->onClick,array('%url%'=>$link)));
						$this->addOption('style', $rowOptions[$columnName], 'cursor:pointer;');
					}
				}
				
				if(!array_key_exists($columnName, $rowOptions))
				{				
					$rowOptions[$columnName] = '';
				}
				
				// Affichage des images pour les booleens
				if(array_key_exists($columnName, $filtersTypes))
				{
					$type = $filtersTypes[$columnName];
					if($type == "BOOLEAN"){

						if($value)
							$value = image_tag('../sfPropelPlugin/images/tick.png',array('align'=>'center'));
						else
							$value = image_tag('../sfPropelPlugin/images/delete.png',array('align'=>'center'));
					}
				}
				
				$htmlOutput.= strtr($this->datagridRows, array(
					'%row_options%' => _tag_options($rowOptions[$columnName]),
					'%value%' => $value
				));
				
				$columnIncrement++;
			}
		}
		else
		{
			if(count($actions) != 0)
			{
				$rowOptions = array('colspan' => count($columns) + 1, 'align' => 'center');
			}
			else
			{
				$rowOptions = array('colspan' => count($columns), 'align' => 'center');
			}
			
			$htmlOutput.= strtr($this->datagridRows, array(
				'%row_options%' => _tag_options($rowOptions),
				'%value%' => $this->traduct(sfDatagrid::getConfig('text_novalueinrows'))
			));
		}
		
		return content_tag('tr', $htmlOutput, array('class' => $rowClass));
	}
	
	/**
	 * Add a value to an options array
	 *
	 * @param string $optionName The name of the option
	 * @param array $optionsArray	The array of options
	 * @param mixed $value The value
	 */
	protected function addOption($optionName, &$optionsArray, $value)
	{
		if (!is_array($optionsArray))
		{
			$optionsArray = array();
		}
		
		if (array_key_exists($optionName, $optionsArray))
		{
			$optionsArray[$optionName].= $value;
		}
		else
		{
			$optionsArray[$optionName] = $value;
		}
	}

	/**
	 * Render the flash message
	 *
	 * @param string $name The name of the flash
	 * @param string $additional_classes The additionals classes
	 * @return string The html output
	 */
	protected function getFlash($name = 'flash', $additional_classes = '')
	{
		$html = '';
		
		if ($additional_classes != ''){
			
			$additional_classes = ' ' . $additional_classes;	
		}

		if (sfContext::getInstance()->getUser()->hasFlash($name)){
			
			$html.= '<div class="flash' . $additional_classes . '">' . sfContext::getInstance()->getUser()->getFlash($name) . '</div>';	
		}
		
		return $html;
	}
	
	/**
	 * Use i18n function (if is active) to traduct a value
	 *
	 * @param string $value The text to traduct
	 * @return string The traducted value
	 */
	protected function traduct($value)
	{
		if (sfConfig::get('sf_i18n'))
		{
			$value = __($value);
		}
		
		return $value;
	}
	
	public static function link_to_remote($name, $options = array(), $html_options = array()){
		
		if(sfConfig::get('app_datagrid_jsframwork','prototype')=='prototype')
			return link_to_remote($name, $options , $html_options );
		else{
			if(sfDatagrid::getConfig('freezepanes',false)==true){
				$options['complete']='freezePanes(\''.$options['update'].'\');';
			}
			return jq_link_to_remote($name, $options , $html_options );
		}
	}
}
?>