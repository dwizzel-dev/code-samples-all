REM EXIT /B
REM echo off
SET MYSQLDBSCRIPT=C:\wamp\www\visou.com\install\db
SET SCRIPTPATH=C:\wamp\www\visou.com\script
SET MYSQLPATH=C:\wamp\bin\mysql\mysql5.6.17\bin
@echo "START"
%MYSQLPATH%\mysql -udwizzel -pleschiens666 visou < "%MYSQLDBSCRIPT%\visou.sql"
%MYSQLPATH%\mysql -udwizzel -pleschiens666 visou < "%MYSQLDBSCRIPT%\xls_categories.sql"
%MYSQLPATH%\mysql -udwizzel -pleschiens666 visou < "%MYSQLDBSCRIPT%\xls_filters.sql"
REM %MYSQLPATH%\mysql -udwizzel -pleschiens666 physiotec < "%MYSQLDBSCRIPT%\physiotec.sql"
php %SCRIPTPATH%\0.start.php
php %SCRIPTPATH%\1.exercises.php
php %SCRIPTPATH%\2.categories.php
php %SCRIPTPATH%\3.filters.php
php %SCRIPTPATH%\4.keywords.php
php %SCRIPTPATH%\5.exercises-categories.php
php %SCRIPTPATH%\6.exercises-filters.php
php %SCRIPTPATH%\7.exercises-keywords.php
php %SCRIPTPATH%\8.data-uniformization.php
php %SCRIPTPATH%\9.duplicate-finder.php
php %SCRIPTPATH%\10.categories-filters-exercises.php
php %SCRIPTPATH%\11.categories-filters.php
php %SCRIPTPATH%\12.create-title.php
php %SCRIPTPATH%\14.create-metadescription.php
php %SCRIPTPATH%\15.keyword-ranking.php
%MYSQLPATH%\mysql -udwizzel -pleschiens666 visou < "%MYSQLDBSCRIPT%\db_finalyse.sql"
@echo "END"