#!/bin/bash

cd svn
cp ../wish-to-go/* ./trunk
svn ci -m "commit ${1}"
svn cp trunk tags/${1}
svn ci -m "tagging version ${1}"