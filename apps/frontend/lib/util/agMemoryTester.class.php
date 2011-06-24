<?php
/**
 * Provides some memory testing capabilities that can be used inside loops
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
class agMemoryTester
{
  protected $currMem,
            $lastMem,
            $minMem,
            $maxMem,
            $currGrowth,
            $minGrowth,
            $maxGrowth,
            $avgGrowth,
            $iterations = 0,
            $isFirst = TRUE;

  public function testMem()
  {
    $this->currMem = memory_get_usage();
    
    if ($this->isFirst) {
      $this->minMem = $this->currMem;
      $this->maxMem = $this->currMem;
      $this->lastMem = $this->currMem;
    }

    if ($this->currMem > $this->maxMem) { $this->maxMem = $this->currMem; }
    if ($this->currMem < $this->minMem) { $this->minMem = $this->currMem; }

    $this->currGrowth = $this->currMem - $this->lastMem;

    if ($this->isFirst) {
      $this->maxGrowth = $this->currGrowth;
      $this->avgGrowth = $this->currGrowth;
    } else {
      $this->avgGrowth = ((($this->avgGrowth * $this->iterations) + $this->currGrowth) / ($this->iterations + 1));
    }

    if ($this->currGrowth > $this->maxGrowth) { $this->maxGrowth = $this->currGrowth; }
    if ($this->currGrowth < $this->minGrowth) { $this->minGrowth = $this->currGrowth; }

    $this->iterations++;
    $this->lastMem = $this->currMem;

    if ($this->isFirst) {
      $this->isFirst = FALSE;
    }
  }


  public function getMemUsage()
  {
    $results = array();
    $results['currMem'] = $this->currMem;
    $results['lastMem'] = $this->lastMem;
    $results['minMem'] = $this->minMem;
    $results['maxMem'] = $this->maxMem;
    $results['currGrowth'] = $this->currGrowth;
    $results['minGrowth'] = $this->minGrowth;
    $results['maxGrowth'] = $this->maxGrowth;
    $results['avgGrowth'] = $this->avgGrowth;
    $results['iterations'] = $this->iterations;
  }
}