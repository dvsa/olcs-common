#!/bin/sh

if [ $1 -eq 0 ]
	then
	$1 = "origin/develop"
fi

echo "{panel:title=TITLE|borderStyle=solid|borderColor=#000|titleBGColor=#75e069/dd4040/e29c22|bgColor=#efefef}"

echo "h2.Summary"

echo "INSERT HERE"

echo "h2.Check PHP syntax"

echo "{code}"

for file in $(git diff $1 --name-only);
do
	if [ -f $file ]
		then
		if [[ ${file: -4} == ".php" ]]
			then
			php -l $file;
		fi
	fi
done

echo "{code}"

echo "h2.Check Coding Standards"

echo "{code}"

for file in $(git diff $1 --name-only);
do
	if [ -f $file ]
		then
		if [[ ${file: -4} == ".php" ]]
			then
			phpcs --standard="/Users/rob/Development/Valtech/OLCS/sonar-configuration/Profiles/DVSA/CS/ruleset.xml" $file;
		fi
	fi
done

echo "{code}"

echo "h2.Run unit tests"

echo "{code}"

cd test && phpunit

echo "{code}"

echo "{panel}"

git diff $1
