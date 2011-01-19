<?php

class agQueryHelper
{
  /**
   * @param string $returnType count or results
   */
  public static function singleScalarReturns($returnType, $query)
  {
    try
    {
      switch ($returnType) {
        case 'results':
          $resultSet = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
          return $resultSet;
        case 'count':
          $resultSet = $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
          $rowCount = $resultSet->count();
          return $rowCount;
        default:
          throw new sfException('An error occurred. Please pass in an accepted parameter.');
      }
    }catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }
}