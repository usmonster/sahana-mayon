<?php

class luceneOptimizeTask extends sfBaseTask
{
  protected function configure()
  {

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'lucene';
    $this->name             = 'optimize';
    $this->briefDescription = 'Optimize a lucene index';
    $this->detailedDescription = <<<EOF
The [lucene:optimize|INFO] optimizes the lucene index
Call it with:
symfony lucene:optimize

  [php symfony lucene:optimize|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    sfContext::createInstance($this->configuration);
    $timer = new sfTimer('timer');
     $this->log('Optimizing ' . LuceneHandler::getLuceneIndexFile() );
    $index = LuceneHandler::getLuceneIndex();
    $index->optimize();
    $this->log('Optimizing took '. round($timer->getElapsedTime(),2) . ' seconds');
  }
}
