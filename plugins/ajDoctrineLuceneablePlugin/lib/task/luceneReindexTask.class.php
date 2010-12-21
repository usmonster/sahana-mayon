<?php

class luceneReindexTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
        new sfCommandArgument('model', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'Models to update'),
    ));

    $this->addOptions(array(
      new sfCommandOption('reset', null, sfCommandOption::PARAMETER_OPTIONAL, 'Completely recreate the search folder (usually faster)', true),
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));


    $this->namespace        = 'lucene';
    $this->name             = 'reindex';
    $this->briefDescription = 'Reindex Lucene Models';
    $this->detailedDescription = <<<EOF
The [lucene:reindex|INFO] Reindexes lucene models

If no models are given, the sf_lucene_models configuration parameter is used.

Call it with:
  [php symfony lucene:reindex Model1 Model2 Model3 .. |INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    sfContext::createInstance($this->configuration);


    if ($options['reset'] == true)
    {
        $this->log('Removing index and creating a new index..');
        LuceneHandler::recreateIndex();
    }
    
    if (sizeof($arguments['model']) == 0)
    {
        $models = sfConfig::get('sf_lucene_models',array());
    } else {
        $models = $arguments['model'];
    }

    $timer = new sfTimer('timer');
    foreach ($models as $model)
    {
        $modeltimer = new sfTimer('Model');
        $this->log('Updating model: '. $model);
        LuceneRecord::updateLuceneTable($model);
        $this->log('Updating ' . $model . ' took ' . round($modeltimer->getElapsedTime(),2) . ' seconds');
    }
    // add your code here


    $loadDb = new luceneOptimizeTask ($this->dispatcher,$this->formatter);
    $loadDb->setCommandApplication($this->commandApplication);
    $loadDbOptions = array();
    $loadDbOptions[] = '--env='.$options['env'];
    if (isset($options['no-confirmation']) && $options['no-confirmation'])
    {
      $loadDbOptions[] = '--no-confirmation';
    }
    if (isset($options['application']) && $options['application'])
    {
      $loadDbOptions[] = '--application=' . $options['application'];
    }
    $ret = $loadDb->run(array(), $loadDbOptions);
  }
}
