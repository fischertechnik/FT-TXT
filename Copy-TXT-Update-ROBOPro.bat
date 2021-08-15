dir "D:\fischertechnik\Update"

mkdir "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_7_0_0"
mkdir "D:\fischertechnik\ROBOPro\ftPro\Firmware\TXTController_4_7_0_0"

del "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_7_0_0\update*.sh
del "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_7_0_0\update*.sig"
del "D:\fischertechnik\ROBOPro\ftPro\Firmware\TXTController_4_7_0_0\update*.sh"
del "D:\fischertechnik\ROBOPro\ftPro\Firmware\TXTController_4_7_0_0\update*.sig"

copy "D:\fischertechnik\Update\update*.sh" "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_7_0_0"
copy "D:\fischertechnik\Update\update*.sig" "C:\Program Files (x86)\ROBOPro\ROBO Interface Firmware\TXTController_4_7_0_0"
copy "D:\fischertechnik\Update\update*.sh" "D:\fischertechnik\ROBOPro\ftPro\Firmware\TXTController_4_7_0_0"
copy "D:\fischertechnik\Update\update*.sig" "D:\fischertechnik\ROBOPro\ftPro\Firmware\TXTController_4_7_0_0"

pause
