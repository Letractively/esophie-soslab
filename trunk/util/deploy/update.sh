#!/bin/bash

SVN_REPO=$1

echo '***************** UPDATING REPOSITORY ****************'
cd $SVN_REPO
sudo -u sos svn update
