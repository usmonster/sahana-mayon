<?php

/**
 * Extended Doctrine_Query to provide safe and additional methods
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agDoctrineQuery extends Doctrine_Query
{
  // these constants map to the custom hydration methods provides in this project
  const     HYDRATE_ASSOC_THREE_DIM = 'assoc_three_dim';
  const     HYDRATE_ASSOC_TWO_DIM = 'assoc_two_dim';
  const     HYDRATE_ASSOC_ONE_DIM = 'assoc_one_dim';
  const     HYDRATE_KEY_VALUE_PAIR = 'key_value_pair';
  const     HYDRATE_KEY_VALUE_ARRAY = 'key_value_array';
  const     HYDRATE_SINGLE_VALUE_ARRAY = 'single_value_array';

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

 /**
  * Custom addition to Doctrine, to allow wrapping a set of OR clauses
  * in parentheses, so that they can be combined with AND clauses.
  *
  * @see http://danielfamily.com/techblog/?p=37
  * I modified it slightly to use an explicit reference.
  *
  * @return Doctrine_Query this object
  */
  public function whereParenWrap()
  {
    $where = &$this->_dqlParts['where'];
    if (count($where) > 0) {
      array_unshift($where, '(');
      array_push($where, ')');
      }
    return $this;
  }
}