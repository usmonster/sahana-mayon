#!/bin/bash
PROJECT_ROOT=`dirname $0`

# echoes commands as they're executed
set -x

# exits on error
set -e

# Tries to infer the web user from a running Apache instance
WEB_USER=`ps axho user,comm|grep -E "httpd|apache"|uniq|awk 'END {print $1}'`
WEB_GROUP=$WEB_USER

# Tries to prompt for sudo password early on
# so as not to interrupt the process later
sudo -u $WEB_USER echo

# reverses exit-on-error behavior, since some errors might be expected
set +e

# This will drop your database, your data, and recreate everything anew


$PROJECT_ROOT/symfony doctrine:drop-db --no-confirmation
$PROJECT_ROOT/symfony doctrine:create-db
sudo -u $WEB_USER $PROJECT_ROOT/symfony doctrine:build-sql
$PROJECT_ROOT/symfony doctrine:insert-sql
# loads sample data and fixtures from the yml files in the data directory
sudo -u $WEB_USER $PROJECT_ROOT/symfony doctrine:data-load -t


#alternate ways:
#  [./symfony doctrine:data-load data/fixtures/dev data/fixtures/users.yml|INFO]

#  If you don't want the task to remove existing data in the database,
#  use the [--append|COMMENT] option:

#  [./symfony doctrine:data-load --append|INFO]
