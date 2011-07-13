<?php

/*
Example DQL Query:

->select('p.*, ps.*, s.*, pn.*, n.*, e.*, lang.*, religion.*, namejoin.*, name.*, nametype.*')
->from('agPerson p, p.agPersonSex ps, ps.agSex s, p.agPersonMjAgNationality pn, pn.agNationality n, p.agEthnicity e, p.agLanguage lang, p.agReligion religion, p.agPersonMjAgPersonName namejoin, namejoin.agPersonName name, name.agPersonNameType nametype')
;

Example RawSql Query:

->select('{e.id}, {s.id}')
->from('ag_entity e INNER JOIN ag_site s ON e.id = s.entity_id')
->addComponent('e', 'agEntity')
->addComponent('s', 'agEntity.agSite')
;
 
*/

require_once(dirname(__FILE__) . '/../config/ProjectConfiguration.class.php');

$resultsLimit = 100 ;
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);

if ($_POST['dqlQueryText']) {
  $queryText = $_POST['dqlQueryText'];
} elseif ($_GET['dqlQueryText']) {
  $queryText = $_GET['dqlQueryText'];
}

if ($_POST['submit']) {
  $submit = $_POST['submit'];
} elseif ($_GET['submit']) {
  $submit = $_GET['submit'];
}

if ($_POST['hydration']) {
  $hydrate = $_POST['hydration'];
} elseif ($_GET['hydration']) {
  $hydrate = $_GET['hydration'];
}

switch($hydrate) {
  case 'Doctrine::HYDRATE_SCALAR':
    $hydrate = Doctrine_Core::HYDRATE_SCALAR;
    break;
  case 'Doctrine::HYDRATE_RECORD':
    $hydrate = Doctrine::HYDRATE_RECORD;
    break;
  case 'Doctrine::HYDRATE_ARRAY':
    $hydrate = Doctrine::HYDRATE_ARRAY;
    break;
  case 'Doctrine::HYDRATE_SINGLE_SCALAR':
    $hydrate = Doctrine::HYDRATE_SINGLE_SCALAR;
    break;
  case 'Doctrine::HYDRATE_ON_DEMAND':
    $hydrate = Doctrine::HYDRATE_ON_DEMAND;
    break;
  case 'Doctrine::HYDRATE_RECORD_HIERARCHY':
    $hydrate = Doctrine::HYDRATE_RECORD_HIERARCHY;
    break;
  case 'Doctrine::HYDRATE_ARRAY_HIERARCHY':
    $hydrate = Doctrine::HYDRATE_ARRAY_HIERARCHY;
    break;
}

if ($_POST['querytype']) {
  $querytype = $_POST['querytype'];
} elseif ($_GET['querytype']) {
  $querytype = $_GET['querytype'];
}

if ($queryText) {
  $databaseManager = new sfDatabaseManager($configuration);
  eval('$dqlQuery = Doctrine_Query::create()->' . $queryText . ';');
  $dqlQueryText = htmlspecialchars($queryText);

  switch($querytype) {
    case 'DQL':
      switch($submit) {
        case 'Get SQL':
          eval('$dqlQuery = Doctrine_Query::create()' . $queryText . ';');
          $queryOutput = htmlspecialchars($dqlQuery->getSqlQuery());
          break;
        case 'Run SQL':
          eval('$dqlQuery = Doctrine_Query::create()' . $queryText . ';');
          $queryOutput = htmlspecialchars($dqlQuery->getSqlQuery());
          $queryResults = $dqlQuery->execute(array(), $hydrate);
          break;
      }
      break;
    case 'RawSQL':
      switch($submit) {
        case 'Get SQL':
          $dqlQuery = new Doctrine_RawSql() ;
          eval('$dqlQuery ' . $queryText . ';');
          $queryOutput = htmlspecialchars($dqlQuery->getSqlQuery());
          break;
        case 'Run SQL':
          $dqlQuery = new Doctrine_RawSql() ;
          eval('$dqlQuery ' . $queryText . ';');
          $queryResults = $dqlQuery->execute(array(), $hydrate);
          break;
      }
      break;
  }
}

if (!isset($dqlQueryText)) {
  $dqlQueryText = "//DQL\n->select('p.*')\n->from('agPerson p');\n\n//RawSQL\n->addComponent('p', 'agPerson')\n->select('{p.*}')\n->from('ag_person p');";
}

?>

<html>
<head>
  <title>dqlme</title>
</head>
<body>

<h3>DQL Reflector</h3>
This application is used to reflect Doctrine queries and their results back at the user and should assist in Doctrine query construction activities. Please visit the following resources for more information:
<table border=0>
  <tr>
    <td valign="top">
      <ul>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#select-queries">SELECT</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#update-queries">UPDATE</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#delete-queries">DELETE</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#group-by,-having-clauses">GROUP BY/HAVING</a></li>
      </ul>
    </td>
    <td valign="top">
      <ul>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#from-clause">FROM</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#indexby-keyword">INDEXBY</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#join-syntax">JOIN</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#join-syntax:on-keyword">ON</a></li>      </ul>
    </td>
    <td valign="top">
      <ul>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#join-syntax:with-keyword">WITH</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#where-clause">WHERE</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#conditional-expressions:in-expressions">IN</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#conditional-expressions:like-expressions">LIKE</a></li>
      </ul>
    </td>
    <td valign="top">
      <ul>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#conditional-expressions:exists-expressions">EXISTS</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#conditional-expressions:literals">Literals</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#conditional-expressions:input-parameters">Input Params</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#conditional-expressions:operators-and-operator-precedence">Operators</a></li>
      </ul>
    </td>
    <td valign="top">
      <ul>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#conditional-expressions:all-and-any-expressions">ALL/ANY</a></li>
        <li>Subqueries<a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#conditional-expressions:subqueries">[1]</a> <a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#subqueries">[2]</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#functional-expressions">Functions</a></li>
        <li><a href="http://www.doctrine-project.org/projects/orm/1.2/docs/manual/dql-doctrine-query-language/en#conditional-expressions:operators-and-operator-precedence">Operators</a></li>
      </ul>
    </td>
  </tr>
</table>

<form action="dqlme.php" method="post">
<table border=0>
  <tr>
    <td><textarea rows=20 cols=80 name="dqlQueryText" wrap="off"><?php echo $dqlQueryText ?></textarea></td>
    <td><textarea rows=20 cols=80 name="queryOutput"><?php echo $queryOutput ?></textarea></td>
  </tr>
  <tr>
    <td align="right">
    <select name="hydration">
      <option value="Doctrine::HYDRATE_SCALAR" selected>Scalar</option>
      <option value="Doctrine::HYDRATE_RECORD">Record</option>
      <option value="Doctrine::HYDRATE_ARRAY">Array</option>
      <option value="Doctrine::HYDRATE_SINGLE_SCALAR">Single Scalar</option>
      <option value="Doctrine::HYDRATE_ON_DEMAND">On Demand</option>
      <option value="Doctrine::HYDRATE_RECORD_HIERARCHY">Record Hierarchy</option>
      <option value="Doctrine::HYDRATE_ARRAY_HIERARCHY">Array Hierarchy</option>
    </select>
    <select name="querytype">
      <option value="DQL" selected>Doctrine Query</option>
      <option value="RawSQL">Doctrine RawSQL</option>
    </select>
    <input type="submit" name="submit" value="Get SQL">
    <input type="submit" name="submit" value="Run SQL">
    <input type="checkbox" name="printr"> printr?
    </td>
  </tr>
</table>
</form>

<?php

if (isset($queryResults)) {
  echo 'Total Records Returned: ' . count($queryResults) . ' (First '. $resultsLimit .' returned below)';
  echo '<table border=1>';
  $headers=0;
  foreach($queryResults as $row){
    if(++$headers<=1){
      echo '<tr>';
      foreach($row as $key => $cell){
        echo '<th>';
        echo $key;
        echo '</th>';
      }
      echo '</tr>';
    }
    echo '<tr>';
    foreach($row as $cell){
      echo '<td>';
      echo $cell;
      echo '</td>';
    }
    echo '</tr>';
  } 
  echo '</table>';

  if($_POST['printr']){
    echo '<pre>';
    print_r($queryResults);
    echo '\</pre>';
  }
}

?>

</body>
</html>
