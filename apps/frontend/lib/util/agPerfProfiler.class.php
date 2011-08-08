<?php
/**
 * Provides some memory testing capabilities that can be used inside loops to track leaks.
 * Recommended usage is to instantiate the class immediately outside a loop and call the test()
 * method as the last line of the loop. This will ensure more accurate timing which takes the
 * class construction as the first time marker.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agPerfProfiler
{
  protected $currMem,
            $lastMem,
            $firstMem,
            $minMem,
            $maxMem,
            $currGrowth = 0,
            $minGrowth = 0,
            $maxGrowth = 0,
            $avgGrowth = 0,
            $totalGrowth = 0,
            $initTime,
            $currTime,
            $lastTime,
            $totalTime,
            $currDuration = 0,
            $minDuration = 0,
            $maxDuration = 0,
            $avgDuration = 0,
            $iterations = 0;

  public function __construct()
  {
    $this->initTime = microtime(TRUE);
    $this->currTime = $this->initTime;

    $this->currMem = memory_get_usage();
    $this->minMem = $this->currMem;
    $this->maxMem = $this->currMem;
    $this->firstMem = $this->currMem;
  }

  /**
   * Method to test memory and timing (usually called within a loop)
   */
  public function test()
  {
    $this->testMem();
    $this->testTime();

    $this->iterations++;
  }

  /**
   * Method to test memory usage
   */
  protected function testMem()
  {
    $this->lastMem = $this->currMem;
    $this->currMem = memory_get_peak_usage();
    
    if ($this->currMem > $this->maxMem) { $this->maxMem = $this->currMem; }
    if ($this->currMem < $this->minMem) { $this->minMem = $this->currMem; }

    $this->currGrowth = $this->currMem - $this->lastMem;
    $this->totalGrowth = $this->currMem - $this->firstMem;
    $this->avgGrowth = ((($this->avgGrowth * $this->iterations) + $this->currGrowth) / ($this->iterations + 1));

    if ($this->currGrowth > $this->maxGrowth) { $this->maxGrowth = $this->currGrowth; }
    if ($this->currGrowth < $this->minGrowth) { $this->minGrowth = $this->currGrowth; }
  }

  /**
   * Method to test operation timing
   */
  protected function testTime()
  {
    $this->lastTime = $this->currTime;
    $this->currTime = microtime(TRUE);
    $this->totalTime = $this->currTime - $this->initTime;

    $this->currDuration = $this->currTime - $this->lastTime;
    $this->avgDuration = ((($this->avgDuration * $this->iterations) + $this->currDuration) / ($this->iterations + 1));

    if ($this->currDuration > $this->maxDuration) { $this->maxDuration = $this->currDuration; }
    if ($this->currDuration < $this->minDuration) { $this->minDuration = $this->currDuration; }
  }

  /**
   * Method to return memory usage statistics as an array
   * @return array Returns memory usage statistics
   */
  public function getMemUsage()
  {
    $results = array();
    $results['firstMem'] = $this->firstMem;
    $results['lastMem'] = $this->lastMem;
    $results['currMem'] = $this->currMem;
    $results['minMem'] = $this->minMem;
    $results['maxMem'] = $this->maxMem;
    $results['currGrowth'] = $this->currGrowth;
    $results['minGrowth'] = $this->minGrowth;
    $results['maxGrowth'] = $this->maxGrowth;
    $results['avgGrowth'] = $this->avgGrowth;
    $results['totalGrowth'] = $this->totalGrowth;
    $results['iterations'] = $this->iterations;

    return $results;
  }

  /**
   * Returns timing results
   * @return array An array of timing results
   */
  public function getTiming()
  {
    $results = array();
    $results['initTime'] = $this->initTime;
    $results['currTime'] = $this->currTime;
    $results['lastTime'] = $this->lastTime;
    $results['totalTime'] = $this->totalTime;
    $results['avgDuration'] = $this->avgDuration;
    $results['currDuration'] = $this->currDuration;
    $results['minDuration'] = $this->minDuration;
    $results['maxDuration'] = $this->maxDuration;
    $results['iterations'] = $this->iterations;

    return $results;
  }

  /**
   * Method to return all results
   * @return array An array of results
   */
  public function getResults()
  {
    return $this->getMemUsage() + $this->getTiming();
  }
}