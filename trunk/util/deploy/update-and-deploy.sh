#!/bin/bash

SVN_REPO=/home/sos/svn

$SVN_REPO/trunk/util/deploy/update.sh $SVN_REPO/trunk
$SVN_REPO/trunk/util/deploy/deploy.sh $SVN_REPO/trunk 
