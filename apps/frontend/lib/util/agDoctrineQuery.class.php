<?php

/**
 * Extended Doctrine_Query to provide safe and additional methods
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agDoctrineQuery extends Doctrine_Query
{

  /**
   * Extends the existing whereIn method so that a negative clause (WHERE FALSE) is returned if
   * the the $params array is empty.
   * <code>
   * $q->whereIn('u.id', array(10, 23, 44));
   * </code>
   *
   * @param string $expr      The operand of the IN
   * @param mixed $params     An array of parameters or a simple scalar
   * @param boolean $not      Whether or not to use NOT in front of IN. Defaults to false (simple IN clause)
   * @return Doctrine_Query   this object.
   */
  public function andWhereIn($expr, $params = array(), $not = false)
  {
      // if there's no params, return a negative match
      if (isset($params) and (count($params) == 0)) {
          return $this->andWhere('?', FALSE) ;
      }

      return parent::andWhereIn($expr, $params, $not) ;
  }
}