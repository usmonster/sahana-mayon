<?php
/**
 * This is the class to make sure that the records and Zend will stay in sync.
 */
class LuceneableListener extends Doctrine_Record_Listener
{

      protected $_options;

      public function __construct(array $options)
      {
        $this->_options = $options;
      }

    public function postDelete(Doctrine_Event $event)
    {
        LuceneRecord::deleteLuceneRecord($event->getInvoker());
    }

    public function postSave(Doctrine_Event $event)
    {
        LuceneRecord::updateLuceneRecord($event->getInvoker());
    }
}

?>
