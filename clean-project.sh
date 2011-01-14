#!/bin/bash
PROJECT_ROOT=`dirname $0`

# echoes commands as they're executed
set -x

# exits on error
set -e

# Tries to infer the web user from a running Apache instance
WEB_USER=`ps axho user,comm|grep -E "httpd|apache"|uniq -d|awk '{print $1}'`

# This will drop your database, your data, and recreate everything anew
# This file should only be used for development of Sahana Agasti, as you
# can see the models, forms and filters are specific to Agasti (prefix ag)

# Tries to prompt for sudo password early on
# so as not to interrupt the process later
sudo -u $WEB_USER echo

# reverses exit-on-error behavior, since some errors might be expected
set +e

# This section removes all old models, forms and 
rm -rf $PROJECT_ROOT/lib/model/doctrine/ag*
rm -rf $PROJECT_ROOT/lib/model/doctrine/base/Baseag*
rm -rf $PROJECT_ROOT/lib/form/doctrine/ag*
rm -rf $PROJECT_ROOT/lib/form/doctrine/base/Baseag*
rm -rf $PROJECT_ROOT/lib/filter/doctrine/base/Baseag*
rm -rf $PROJECT_ROOT/lib/filter/doctrine/ag*

# The following lines run symfony and doctrine commands to generate new models
# forms and filters from the yml files

$PROJECT_ROOT/symfony cc
$PROJECT_ROOT/symfony doctrine:clean --no-confirmation
$PROJECT_ROOT/symfony doctrine:build-model
$PROJECT_ROOT/symfony doctrine:drop-db --no-confirmation
$PROJECT_ROOT/symfony doctrine:create-db
sudo -u $WEB_USER $PROJECT_ROOT/symfony doctrine:build-sql
$PROJECT_ROOT/symfony doctrine:insert-sql
$PROJECT_ROOT/symfony doctrine:build-forms
$PROJECT_ROOT/symfony doctrine:build-filters
# This loads all sample data and fixtures in from yml files existing in
# the data directory
sudo -u $WEB_USER $PROJECT_ROOT/symfony doctrine:data-load -t
