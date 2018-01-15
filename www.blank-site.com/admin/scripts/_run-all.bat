REM EXIT /B
REM echo off
SET MYSQLPATH=C:\wamp\bin\mysql\mysql5.6.17\bin
SET SCRIPTPATH=C:\wamp\www\www.blank-site.com\admin\scripts
@echo "START"
php %SCRIPTPATH%\1.create-exercises.php
php %SCRIPTPATH%\2.create-keywords.php
php %SCRIPTPATH%\3.create-categories.php
php %SCRIPTPATH%\5.create-javascript-db-kw.php
php %SCRIPTPATH%\6.create-php-db-kw.php
@echo "END"
