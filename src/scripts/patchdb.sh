#!/bin/bash

if [ -z $1 ]
then
	echo "Please give a DB name"
	exit 1
fi
DBNAME=$1
PATCH_LEVEL=$(mysql $DBNAME -e 'select max(patch_number) as num from patch_history' | grep -v num)

if [ $? -gt 0 ]
then
	echo Fail
	exit 1
fi

PATCH_DIR=/var/www/joind.in/www/doc/db
MAX_PATCH_LEVEL=$(ls $PATCH_DIR/patch*.sql | egrep -o 'patch[0-9]*.sql' | egrep -o '[0-9]*' | sort -n | tail -n 1)

for i in $(seq $(($PATCH_LEVEL + 1)) $MAX_PATCH_LEVEL)
do
	echo "Applying patch $i"
	mysql $DBNAME < $PATCH_DIR/patch$i.sql
	if [ $? -gt 0 ]
	then
		echo Fail
		exit 1
	fi
done

echo Success

