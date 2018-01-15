@echo "...START DUMP"
SET DTIME=%Time%
SET DDATE=%Date%
SET YYYY=%DDATE:~0,4%
SET MTH=%DDATE:~5,2%
SET DD=%DDATE:~8,2%
SET HH=%DTIME:~0,2%
SET MM=%DTIME:~3,2%
SET SS=%DTIME:~6,2%
ECHO DTIME
SET MYSQLPATH="C:\wamp\bin\mysql\mysql5.6.17\bin"
SET FILEEXPORT="C:\wamp\www\www.blank-site.com\temp\dump\dump_blanksite.sql"
%MYSQLPATH%\mysqldump -udwizzel -pleschiens666 blanksite> %FILEEXPORT%
@echo "END DUMP BLANKSITE..."
SET FILEEXPORT="C:\wamp\www\www.blank-site.com\temp\dump\dump_visou.sql"
%MYSQLPATH%\mysqldump -udwizzel -pleschiens666 visou> %FILEEXPORT%
@echo "END DUMP VISOU..."
