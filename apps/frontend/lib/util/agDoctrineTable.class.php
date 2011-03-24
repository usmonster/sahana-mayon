<?php

/**
 * Extended Doctrine_Table to provide safe and additional methods
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agDoctrineTable extends Doctrine_Table
{
    /**
     * Creates a query on this table. Modified based on below-referenced patch in 1.2.4.
     *
     * This method returns a new Doctrine_Query object and adds the component
     * name of this table as the query 'from' part.
     * <code>
     * $table = Doctrine_Core::getTable('User');
     * $table->createQuery('myuser')
     *       ->where('myuser.Phonenumber = ?', '5551234');
     * </code>
     *
     * @param string $alias     name for component aliasing
     * @see http://www.doctrine-project.org/jira/browse/DC-421
     * @return Doctrine_Query
     */
    public function createQuery($alias = '')
    {
        if ( ! empty($alias)) {
            $alias = ' ' . trim($alias);
        }

        $class = $this->getAttribute(Doctrine_Core::ATTR_QUERY_CLASS);

        return Doctrine_Query::create(null, $class)
            ->from($this->getComponentName() . $alias);
    }

}