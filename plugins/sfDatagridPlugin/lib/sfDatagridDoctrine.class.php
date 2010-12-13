<?php
/**
 * This is class sfDatagridDoctrine
 * 
 * @author		David Zeller	<zellerda01@gmail.com>
 */
class sfDatagridDoctrine extends sfDatagrid
{
	protected
        $class = '',
        $alias = 'u',
        $columnsCompare = array(),
        $query = null;
    
	public function __construct($datagridName, $class, Doctrine_Query $base_query = null, $alias = 'u')
	{
		parent::__construct($datagridName);

        if(is_null($base_query))
        {
            $this->query = Doctrine::getTable($class)->createQuery($alias);
        }
        else
        {
            $this->query = $base_query;
        }

        $this->alias = $alias;
        $this->class = $class;
	}
    
    public function prepare()
    {
        parent::prepare();
        
        if($this->haveSort())
        {
            $this->setDoctrineSorting();
        }
        
        if($this->renderSearch)
        {
            $this->addSearch();
        }
        
        // Define the default search fields
        foreach(array_keys($this->columns) as $column)
        {
            if(!array_key_exists($column, $this->filtersTypes))
            {
                $this->filtersTypes[$column] = $this->getColumnType($column);
            }
        }
        
        // Set the pager
        $p = new sfDoctrinePager($this->class, $this->rowLimit);
        $p->setPage($this->page);
        $p->setQuery($this->query);
        $p->init();
        
        $this->pager = $p;
        return $p;
    }
    
    protected function getColumnType($column)
    {
        try
        {
            $class = $this->class;
            
            if(array_key_exists($column, $this->columnsSort))
            {
                if($this->columnsSort[$column] != 'nosort')
                {
                    $tmp = explode('::', $this->columnsSort[$column]);
                    
                    $class = $tmp[0];
                    $column = $tmp[1];
                }
                else
                {
                    return 'NOTYPE';
                }
            }
            else
            {
                $class = $this->class;
            }
            
            $definition = Doctrine::getTable($class)->getColumnDefinition($column);
            return $this->normalizeType($definition['type']);
        }
        catch(Exception $ex)
        {
            return 'NOTYPE';
        }
    }
    
    protected function haveSort()
    {
        foreach($this->columns as $col => $label)
        {
            if(array_key_exists($col, $this->columnsSort))
            {
                if($this->columnsSort[$col] != 'nosort')
                {
                    return true;
                }
            }
            else
            {
                return true;
            }
        }
        
        return false;
    }
    
    public function normalizeType($type)
    {
        $replace = array('string' => 'VARCHAR', 'boolean' => 'BOOLEAN', 'date' => 'DATE');
        
        return str_replace(array_keys($replace), array_values($replace), $type);
    }
    
    protected function setDoctrineSorting()
    {        
        if($this->sortOrder == 'asc')
        {
            $this->query->orderBy($this->sortBy . ' ASC');
        }
        else
        {
            $this->query->orderBy($this->sortBy . ' DESC');
        }
    }
    
    public function addSearch()
    {
        $columnsIds = array_keys($this->columns);
        
        foreach($columnsIds as $col)
        {
            if(is_array($this->search) && array_key_exists($col, $this->search) && !is_null($this->search[$col]) && $this->search[$col] != '')
            {
                if(array_key_exists($col, $this->columnsCompare))
                {
                    $comp = $this->columnsCompare[$col];
                }
                else
                {
                    $comp = 'LIKE';
                }
                
                switch($this->getColumnType($col))
                {                        
                    case 'NOTYPE':
                        // Do nothing
                        break;
                        
                    case 'BOOLEAN':
                        $this->query->addWhere($this->alias . '.' . $col . ' = ?', $this->search[$col]);
                        break;
                        
                    case (strtoupper($this->getColumnType($col)) == 'DATE' || strtoupper($this->getColumnType($col)) == 'TIMESTAMP'):
                        
                        try {
                            
                            if(array_key_exists('start_' . $this->datagridName, $this->search[$col]) && $this->search[$col]['start_' . $this->datagridName] != '')
                            {
                                $this->query->addWhere($this->alias . '.' . $col . ' >= ?', format_date(strtotime($this->search[$col]['start_' . $this->datagridName]), 'yyyy-MM-dd'));
                            }
                        
                        
                            if(array_key_exists('stop_' . $this->datagridName, $this->search[$col]) && $this->search[$col]['stop_' . $this->datagridName] != '')
                            {
                                $this->query->addWhere($this->alias . '.' . $col . ' <= ?', format_date(strtotime($this->search[$col]['stop_' . $this->datagridName]), 'yyyy-MM-dd'));
                            }
                            
                        } catch(Exception $ex) {
                            
                            $this->search[$col]['start_' . $this->datagridName] = '';
                            $this->search[$col]['stop_' . $this->datagridName] = '';
                        }
                        
                        break;
                        
                    default:
                        
                        if($comp == 'LIKE')
                        {
                            $this->query->addWhere($this->alias . '.' . $col . ' LIKE ?', '%' . $this->search[$col] . '%');
                        }
                        else
                        {
                            $this->query->addWhere($this->alias . '.' . $col . ' ' . $comp . ' ?', $this->search[$col]);
                        }
                        
                        break;
                }
            }
        }
    }
    
    public function setColumnsCompare($options)
    {
        $this->columnsCompare = $options;
    }
    
    /**
	 * Get the html output for the datagrid
	 *
	 * @param array $values The array of values
	 * @param array $alternate The two class to alternate
	 */
    public function getContent($values, $alternate, $formatter = '')
	{
		if($formatter != '')
		{
			return parent::getContent($values, $alternate, $formatter);
		}
		else
		{
           return parent::getContent($values, $alternate, 'doctrine');
		}
	}
}
?>