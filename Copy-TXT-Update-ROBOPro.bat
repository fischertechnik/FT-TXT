dir "F:\TX2\buildroot-2018\update"

mkdir "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_4_4_0"
mkdir "F:\ROBOPro\ftPro\Firmware\TXTController_4_4_4_0"

del "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_4_4_0\update*.sh
del "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_4_4_0\update*.sig"
del "F:\ROBOPro\ftPro\Firmware\TXTController_4_4_4_0\update*.sh"
del "F:\ROBOPro\ftPro\Firmware\TXTController_4_4_4_0\update*.sig"

copy "F:\TX2\buildroot-2018\update\update*.sh" "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_4_4_0"
copy "F:\TX2\buildroot-2018\update\update*.sig" "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_4_4_0"
copy "F:\TX2\buildroot-2018\update\update*.sh" "F:\ROBOPro\ftPro\Firmware\TXTController_4_4_4_0"
copy "F:\TX2\buildroot-2018\update\update*.sig" "F:\ROBOPro\ftPro\Firmware\TXTController_4_4_4_0"

pause
