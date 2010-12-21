<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LuceneSearch
 *
 * @author Arend
 */
class LuceneSearch {

    private $_queryString = "";
    private $_queryObject;
    private $_results = array();
    private $_models = array();
    private $_fuzzy = false;
    private $_pks = false;
    private $_hits = array();
    private $_executed = false;
    private $_index;
    private $_records = false;
    /**
     * create a new object
     * @param <type> $sSearch
     * @return LuceneSearch
     */
    static public function find($sSearch)
    {
        return new self($sSearch);
    }

    /**
     * create a new object
     * @param <type> $sSearch
     * @return LuceneSearch
     */
    static public function filterSearch(Doctrine_Query $query, $sSearch, $sModel)
    {
        return new self($sSearch);
    }

    /**
     * create a new object
     * @param <type> $sSearch
     * @return LuceneSearch
     */
    public function __construct($sSearch = null)
    {
        $this->_index = LuceneHandler::getLuceneIndex();
        $this->_queryString = $sSearch;
        return $this;
    }

    /**
     * Search in Model sModel
     * @param <type> $sModel, $sModel2, $sModel3
     * @return LuceneSearch
     */
    public function in($sModel)
    {
        if (is_array($sModel))
        {
            $this->_models = $sModel;
        } else {
            $this->_models = func_get_args();
        }
        return $this;
    }

    /**
     * Enable fuzzy searching
     * @return LuceneSearch
     */
    public function fuzzy($bBoolean = true)
    {
        $this->_fuzzy = $bBoolean;
        return $this;
    }

    /**
     * Execute the search
     * @return LuceneSearch
     */
    public function execute ()
    {
        if ($this->_executed)
        {
            return $this;
        }
        $this->_parseQuery();
        $this->_addModels();
        $this->_hits = LuceneHandler::getLuceneIndex()->find($this->_queryString);
        $this->_executed =true;
        return $this;
    }

    /**
     *
     * @return <type>
     */
    public function getHits()
    {

        return $this->execute()->_hits;
    }

    /**
     * Parse the pks from the hits
     * @param <type> $sModel
     * @return array
     */
    public function getPks()
    {
        $this->execute();
        if ($this->_pks != false)
        {
            return $this->_pks;
        }
        $pks = array();
        foreach ($this->_hits as $hit)
        {
            if (!isset($pks[$hit->model]))
            {
                $pks[$hit->model] = array();
            }
            $pks[$hit->model][] = $hit->pk;
        }
        $this->_pks = $pks;
        return $pks;
    }


    public function getRecords($iLimit = 20, $hydration = Doctrine::HYDRATE_RECORD )
    {
          $this->getPks();
          $this->_results = array();
          foreach ($this->_pks as $sName => $aValue)
          {
              $this->_results[$sName] = Doctrine_Query::create()
              ->from($sName . ' x INDEXBY x.id')
              ->limit($iLimit)
              ->whereIn('x.id', $aValue)
              ->execute(array(),$hydration);
          }
          return $this->_results;
     }
     /**
     * Get the pks for a specific model
     * @param <type> $sModel
     * @return array
     */
    public function getPksForModel($sModel = null)
    {
        if (is_null($sModel))
        {
            $sModel = $this->_models[0];
        }
        $this->getPks();
        if (!isset($this->_pks[$sModel]))
        {
            return array();
        } else {
            return $this->_pks[$sModel];
        }
    }

    public function setFilterQuery(Doctrine_Query $query, $field = "id", $sModel = false)
    {
        if (count($this->_models) != 1)
        {
            throw new InvalidArgumentException('The setFilterQuery method only works on searching in one model, multiple models given:' . join(', ',$this->_models));
        }
        $sModel = $this->_models[0];
        $pks = $this->getPksForModel($sModel);
        if (count($pks) > 0)
        {
            return $query->andWhereIn($field,$this->getPksForModel($sModel));
        } else {
            // fix for AndWhereIn(array()) not returning nothing, but everything.
            return $query->andWhere('TRUE = FALSE');
        }
        //return $query;
    }

    private function _addModels ()
    {
        if (count($this->_models) > 0)
        {
          if ($this)
          $newQuery = "+(" . $this->_queryString . ")" . "+(";

          foreach ($this->_models as $model)
          {
            $newQuery .= "model:".$model . " ";
          }
            $newQuery .= ")";
            $this->_queryString = $newQuery;
          }


    }

    private function _parseQuery ()
    {
        if ($this->_fuzzy)
        {
          $this->_queryString = trim($this->_queryString);
          $parts = preg_split("/\s/",$this->_queryString);
          foreach ($parts as &$part)
          {
              if (strlen($part) > 0)
              {
                $part = $part .'~';
              }
          }
          $this->_queryString = join(" ",$parts);
        }
    }

}