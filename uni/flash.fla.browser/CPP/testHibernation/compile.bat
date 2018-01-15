erase WinLoader.obj WinLoader.RES WinLoader.tds WinVersion.RES testHibernation.exe
bcc32 -w-par -c -tW WinLoader.cpp
ilink32 -aa -c -x -Gn WinLoader.obj c0w32.obj,testHibernation.exe,,import32.lib cw32.lib psapi.lib,,
testHibernation.exe
