#!/bin/bash
TARGET=/var/www/joind.in/${BUILD_NUMBER}
LAUNCHREF=remotes/magicmonkey/ci-tools

sg web -c "mkdir -p $TARGET ; git archive $LAUNCHREF | tar xC $TARGET"

