#!/bin/bash
PROJECT_ROOT=$(dirname $0)

# echoes commands as they're executed
set -x

# exits on error
set -e

# Tries to infer the web user from a running Apache instance
WEB_USER=$(ps axho user,comm|grep -E "httpd|apache"|uniq|awk 'END {print $1}')
WEB_GROUP=$WEB_USER

# This will drop your database, your data, and recreate everything anew
# This file should only be used for development of Sahana Agasti, as you
# can see the models, forms and filters are specific to Agasti (prefix ag)

# Tries to prompt for sudo password early on
# so as not to interrupt the process later
sudo -u $WEB_USER echo

# reverses exit-on-error behavior, since some errors might be expected
set +e

# removes all old models, forms, and filters
rm -rf $PROJECT_ROOT/lib/model/doctrine/ag*
rm -rf $PROJECT_ROOT/lib/model/doctrine/base/Baseag*
rm -rf $PROJECT_ROOT/lib/form/doctrine/ag*
rm -rf $PROJECT_ROOT/lib/form/doctrine/base/Baseag*
rm -rf $PROJECT_ROOT/lib/filter/doctrine/base/Baseag*
rm -rf $PROJECT_ROOT/lib/filter/doctrine/ag*

# clears the cache
$PROJECT_ROOT/symfony cc

# removes search index files to avoid pollution from previous installs
sudo -u $WEB_USER rm -rf $PROJECT_ROOT/data/search/*

# resets file and directory perms (NOTE: chmod does NOT recurse in this case)
sudo chgrp -R $WEB_GROUP $PROJECT_ROOT/cache/ $PROJECT_ROOT/log/ $PROJECT_ROOT/config/ $PROJECT_ROOT/apps/*/config/ $PROJECT_ROOT/data/indexes/ \$PROJECT_ROOT/data/search/ $PROJECT_ROOT/data/sql/ $PROJECT_ROOT/web/wiki/conf/ $PROJECT_ROOT/web/wiki/data/
chmod -c g+wr $PROJECT_ROOT/config/ $PROJECT_ROOT/apps/*/config/ $PROJECT_ROOT/data/indexes/ $PROJECT_ROOT/data/search/ $PROJECT_ROOT/data/sql/ $PROJECT_ROOT/web/wiki/conf/ $PROJECT_ROOT/web/wiki/data/
#considered harmful?:
#sudo $PROJECT_ROOT/symfony project:permissions

# reindexes wiki pages
sudo -u $WEB_GROUP $PROJECT_ROOT/web/wiki/bin/indexer.php -c

# generates new models, forms, and filters from the yml files
$PROJECT_ROOT/symfony doctrine:clean --no-confirmation
$PROJECT_ROOT/symfony doctrine:build-model
$PROJECT_ROOT/symfony doctrine:drop-db --no-confirmation
$PROJECT_ROOT/symfony doctrine:create-db
sudo -u $WEB_USER $PROJECT_ROOT/symfony doctrine:build-sql
$PROJECT_ROOT/symfony doctrine:insert-sql
$PROJECT_ROOT/symfony doctrine:build-forms
$PROJECT_ROOT/symfony doctrine:build-filters

# loads sample data and fixtures from the yml files in the data directory
sudo -u $WEB_USER $PROJECT_ROOT/symfony doctrine:data-load 

