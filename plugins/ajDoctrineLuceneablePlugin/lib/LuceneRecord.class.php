<?php
/**
 * Description of LuceneRecord
 *
 * All Record related lucene operations. (Modifying etc.)
 *
 * @author Arend
 */
class LuceneRecord {

    /**
     * Updates a whole table, by adding it to the lucene index.
     * @param <string> $sTable Doctrine Model name
     */
    static public function updateLuceneTable($sTable)
    {
        $oTable = Doctrine::getTable($sTable);
        $aRecords = $oTable->findAll();

        self::deleteLuceneTable($sTable);
        foreach ($aRecords as $oRecord)
        {
            self::updateLuceneRecord($oRecord,false);
        }
    }

    /**
     * Deletes a doctrine record.
     * I expect the model to have been saved by 'pkmodel' with the syntax of ClassName-id
     * 
     * @param <string> $sTable Doctrine Model name
     */
    static public function deleteLuceneRecord ($model) {
      $index = LuceneHandler::getLuceneIndex();
      foreach ($index->find('pkmodel:'.get_class($model) . '-' . $model->getId()) as $hit)
      {
        $index->delete($hit->id);
      }
    }

    /**
     * Deletes a doctrine table.
     * I expect the model to have been saved by 'pkmodel' with the syntax of ClassName-id
     *
     * @param <string> $sTable Doctrine Model name
     */
    static public function deleteLuceneTable($model) {
      $index = LuceneHandler::getLuceneIndex();
      foreach ($index->find('model:'.$model) as $hit)
      {
        $index->delete($hit->id);
      }
    }

    /**
     * Updates or creates a lucene record.
     *
     * There are several places to look for configuration:
     *   1. The plugin options
     *   2. The model (looks for the property
     *   3. the updateLucene() method on the model can return a valid Zend_Search_Lucene_Document
     *
     * The method automagickly add the pk, pkmodel and model fields to the lucene document
     * 
     * @param <string> $sTable Doctrine Model name
     * @param <boolean> $bDelete Don't delete the old model, just add a new one.
     */
    static public function updateLuceneRecord(Doctrine_Record $model, $bDelete = true)
    {
      $options = $model->getTable()->getTemplate('Luceneable')->getOptions();
      if ($bDelete)
      {
        self::deleteLuceneRecord($model);
      }
      $index = LuceneHandler::getLuceneIndex();
      // store job primary key to identify it in the search results

      // See of ModelName->updateLucene() exists and returns a valid Zend_Search_Lucene_Document
      $doc = new Zend_Search_Lucene_Document();
      if (method_exists($model,'updateLucene'))
      {
          $doc = call_user_func(array($model,'updateLucene'),$doc);
          if (!$doc instanceof Zend_Search_Lucene_Document)
          {
            return false;
          }
      }

      // add primary key fields
      $doc->addField(Zend_Search_Lucene_Field::Keyword('pk', $model->getId()));
      $doc->addField(Zend_Search_Lucene_Field::Keyword('model', get_class($model)));
      $doc->addField(Zend_Search_Lucene_Field::Keyword('pkmodel', get_class($model) . '-' . $model->getId()));

      $fields = array();
      if (isset($options['fields']))
      {
            $fields = $options['fields'];
      }

      // look in the model for luceneSearchFields
      // this overrules the yaml config
      if (property_exists($model, 'luceneSearchFields'))
      {
          $fields = $model->luceneSearchFields;

      }

      // add the fields individually
      foreach ($fields as $name => $type)
      {
        switch ($type)
        {
          case "keyword":
            $doc->addField(Zend_Search_Lucene_Field::Keyword($name, $model[$name]), 'utf-8');
            break;
          case "text":
            $doc->addField(Zend_Search_Lucene_Field::Text($name, $model[$name]), 'utf-8');
          case "unindexed":
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed($name, $model[$name]), 'utf-8');
          case "unstored":
          default:
              $doc->addField(Zend_Search_Lucene_Field::Unstored($name, $model[$name]), 'utf-8');
          break;
        }
      }
      // finally, add the document to the index.
      $index->addDocument($doc);
    }
}
?>
