@echo "...START CRONJOB"
SET SCRIPTPATH=C:\wamp\www\www.blank-site.com\admin\scripts
CALL %SCRIPTPATH%\dump_mysql.bat
@echo "END CRONJOB"
