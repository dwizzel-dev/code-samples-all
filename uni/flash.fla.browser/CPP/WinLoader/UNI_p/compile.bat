erase UNI3_p.obj UNI3_p.RES UNI3_p.tds WinVersion.RES UNI3_p.exe
bcc32 -c -tW uni.cpp
brcc32 -v uni.rc 
brcc32 -v WinVersion.rc
ilink32 -aa -c -x -Gn uni.obj c0w32.obj,UNI3_p.exe,,import32.lib cw32.lib psapi.lib,,uni.res WinVersion.res