#!/bin/bash
PROJECT_ROOT=$(dirname $0)

usage(){
  echo "This script can be used by developers to effectively re-build a post-install Mayon system. It is not intended as a command-line installation/configuration option as several important steps taken by the web-based installer / configurator are missed.
  
  Example #1: Clean out the project and only load default data
  user@pc:/project/root$ ./clean-project.sh
  
  Example #2: Cleans out the project and also load sample data
  user@pc:/project/root$ ./clean-project.sh -s

  Parameters (Required):
      -s    Load sample data in addition to default data
      -h    Prints this help statement"
  exit 1
}

# accept the sample-data parameter
SAMPLEDATA=false
while getopts "s" option
  do
    case "$option" in
      "s")
        SAMPLEDATA=true
        ;;
      "h")
        usage
        exit 1
        ;;
      "?")
        echo "Invalid option: -$OPTARG" >&2
        exit 1
       ;;
      *)
        echo "Unkown error processing options" >&2
        exit 1
        ;;
    esac
  done
  
# echoes commands as they're executed
set -x

# exits on error
set -e

# Tries to infer the web user from a running Apache instance
WEB_USER=$(ps axho user,comm|grep -E "httpd|apache"|uniq|grep -v "root"|awk 'END {print $1}')
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

# purges the logs
#$PROJECT_ROOT/symfony log:clear
# instead:

# rotates the logs
$PROJECT_ROOT/symfony log:rotate --period=1 --history=8 frontend all

# removes search index files to avoid pollution from previous installs
sudo rm -rf $PROJECT_ROOT/data/search/*

# removes any leftover uploaded files
sudo rm -rf $PROJECT_ROOT/data/uploads/* $PROJECT_ROOT/data/downloads/*

# resets file and directory perms
sudo chgrp -R $WEB_GROUP $PROJECT_ROOT/cache/ $PROJECT_ROOT/log/ $PROJECT_ROOT/config/ $PROJECT_ROOT/apps/*/config/ $PROJECT_ROOT/data/ $PROJECT_ROOT/web/wiki/conf/ $PROJECT_ROOT/web/wiki/data/ $PROJECT_ROOT/web/wiki/lib/plugins/dw2pdf/mpdf/tmp/
sudo chmod -cR g+wr $PROJECT_ROOT/data/ $PROJECT_ROOT/web/wiki/data/
# NOTE: chmod should NOT recurse for these, for security's sake:
chmod -c g+wr $PROJECT_ROOT/config/ $PROJECT_ROOT/apps/*/config/ $PROJECT_ROOT/web/wiki/conf/ $PROJECT_ROOT/web/wiki/lib/plugins/dw2pdf/mpdf/tmp/
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
if $SAMPLEDATA
then
  sudo -u $WEB_USER $PROJECT_ROOT/symfony doctrine:data-load data/fixtures data/samples
else
  sudo -u $WEB_USER $PROJECT_ROOT/symfony doctrine:data-load data/fixtures
fi

