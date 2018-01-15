#!/bin/sh

#base dir
baseDir="/var/www/mobile....@physiotec.ca/logs"
#date
dDate=$(date +%Y%m%d)
#new directory
newDir="$baseDir/$dDate"
#create de directory
mkdir $newDir
#on move les fichiers
mv $baseDir/*.txt $newDir/
#zip les fichiers de log dans le new dir
exec `zip -rq9 $newDir/logs.zip $newDir/ "*.txt"`
#on remove les fichiers de log une fois compresse
rm $newDir/*.txt





