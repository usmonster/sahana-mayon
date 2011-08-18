<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');

/**
 * @see apps/frontend/lib/packages/agWebservicesPackage/config/routing.yml
 * @see apps/frontend/lib/packages/agWebservicesPackage/data/fixtures.yml
 */

$browser = new sfTestFunctional(new sfBrowser());

$browser->
  info(sprintf('Testing routes - without authentication'))->
  get('/webservices')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'index')->
    end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#web_service', 'Web Services')->
    checkElement('#staff_link', 'Staff')->
    checkElement('#organizations_link', 'Organizations')->
    end()->
  get('/webservices/index')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'index')->
    end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#web_service', 'Web Services')->
    checkElement('#staff_link', 'Staff')->
    checkElement('#organizations_link', 'Organizations')->
    end()->
    
  get('/webservices/list/staff')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'list')->
    isParameter('datapoint', 'staff')->
    end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#columns h2', 'Staff Listing')->
    checkElement('thead tr th', 'Id')->
    end()->
    
  get('/webservices/list/organizations')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'list')->
    isParameter('datapoint', 'organizations')->
    end()->    
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#columns h2', 'Organizations Listing')->
    checkElement('thead tr th', 'Id')->
    end()->
    
  info(sprintf('Accessing route /webservices/list with a wrong datapoint'))->
  get('/webservices/list/person')->
  with('response')->begin()->
    isStatusCode(404)->
    end()->
    
  info(sprintf('Staff in XML - valid token'))->
  get('/webservices/get/agasti/staff.xml')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'get')->
    isParameter('token', 'agasti')->
    isParameter('datapoint', 'staff')->
    isParameter('sf_format', 'xml')->
    isFormat('xml')->
    end()->
  with('response')->begin()->
    isStatusCode(200)->
    isValid()->
    isHeader('Content-type', 'text/xml; charset=utf-8')->
    checkElement('entity[type="staff"]', true)->
    end()->
    
  info(sprintf('Organizations in XML - valid token'))->
  get('/webservices/get/agasti/organizations.xml')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'get')->
    isParameter('token', 'agasti')->
    isParameter('datapoint', 'organizations')->
    isParameter('sf_format', 'xml')->
    isFormat('xml')->
    end()->
  with('response')->begin()->
    isStatusCode(200)->
    isValid()->
    isHeader('Content-type', 'text/xml; charset=utf-8')->
    checkElement('entity[type="organizations"]', true)->
    end()->
    
  info(sprintf('Staff in JSON - valid token'))->
  get('/webservices/get/agasti/staff.json')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'get')->
    isParameter('token', 'agasti')->
    isParameter('datapoint', 'staff')->
    isParameter('sf_format', 'json')->
    isFormat('json')->
    end()->
  with('response')->begin()->
    isStatusCode(200)->
    isHeader('Content-type', 'application/json')->
    matches('/"type"\:"staff"/')->
    matches('/"id"\:"\d+"/')->
    end()->
    
  info(sprintf('Organizations in JSON - valid token'))->
  get('/webservices/get/agasti/organizations.json')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'get')->
    isParameter('token', 'agasti')->
    isParameter('datapoint', 'organizations')->
    isParameter('sf_format', 'json')->
    isFormat('json')->
    end()->
  with('response')->begin()->
    isStatusCode(200)->
    isHeader('Content-type', 'application/json')->
    matches('/"type"\:"organizations"/')->
    matches('/"id"\:"\d+"/')->
  	matches('/"entity_id"\:"\d+"/')->
    end()->
    
  info(sprintf('Try to reach datapoint with invalid token'))->
  get('/webservices/get/invalid_token/organizations.json')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'get')->
    isParameter('token', 'invalid_token')->
    isParameter('datapoint', 'organizations')->
    isParameter('sf_format', 'json')->
    isFormat('json')->
    end()->
  with('response')->begin()->
    isStatusCode(404)->
    end()->
    
  info(sprintf('Try to reach datapoint with inactive client'))->
  get('/webservices/get/inactive_client/organizations.json')->
  with('request')->begin()->
    isParameter('module', 'agWebservices')->
    isParameter('action', 'get')->
    isParameter('token', 'inactive_client')->
    isParameter('datapoint', 'organizations')->
    isParameter('sf_format', 'json')->
    isFormat('json')->
    end()->
  with('response')->begin()->
    isStatusCode(404)->
    end()
;