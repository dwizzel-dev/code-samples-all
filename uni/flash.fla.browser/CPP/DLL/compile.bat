@ECHO OFF
TITLE UNI COMPILER
CLS
SET PATH_TO_UNI=C:\flash.fla.browser\CPP\DLL
REM compiler for test.dll for zinc extensions
ECHO(
ECHO -- DLL COMPILE -----------------------------------------------------------------
erase flashArray.lib flashArray.obj flashArray.tds flashArray.dll test.tds test.obj
bcc32 +config.cfg -P -WD flashArray.cpp
ECHO(
ECHO -- IMPLIB      -----------------------------------------------------------------
implib flashArray.lib flashArray.dll
ECHO(
ECHO -- EXE COMPILE -----------------------------------------------------------------
bcc32 +config.cfg -c -tW test.cpp
ECHO(
ECHO -- LINKER      -----------------------------------------------------------------
ilink32 /aa /c /x /Gn test.obj c0w32.obj,test.exe,,flashArray.lib import32.lib cw32.lib,,
ECHO(
ECHO -- RUN TEST    -----------------------------------------------------------------
test.exe