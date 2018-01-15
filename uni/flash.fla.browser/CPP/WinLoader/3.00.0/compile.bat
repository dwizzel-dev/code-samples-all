erase WinLoader.obj WinLoader.RES WinLoader.tds WinVersion.RES UNI3.exe
bcc32 -c -tW WinLoader.cpp
brcc32 -v WinLoader.rc 
brcc32 -v WinVersion.rc
ilink32 -aa -c -x -Gn WinLoader.obj c0w32.obj,UNI3.exe,,import32.lib cw32.lib psapi.lib,,WinLoader.res WinVersion.res