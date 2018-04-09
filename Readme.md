
# FT-TXT : 

## Rootfs / Kernel / Apps

Erstellung mit Buildroot, Bootloader "Das U-Boot"

Voraussetzungen siehe
[BuildrootManual](https://buildroot.org/downloads/manual/manual.pdf) 

## Erstellen des Rootfs, Bootloader und Kernel

1. Erstellen eines Arbeitsverzeichnisses
  z.B 
 ```
 mkdir FT
 ```	
2. Wechseln in das Verzeichnis
 ```
 cd FT
 ```
3. Clone des Script und Konfigurationsverzeichnisses
 ```
 git clone https://gogs.psg-bgh.de/fischertechnik/FT-TXT.git
 ```

4. Verzeichnis für Toolchain erstellen
 ```
 sudo mkdir /opt/FT
 sudo chmod a+rw /opt/FT
 ```
5. In Verzeichnis wechseln
 ```
 cd FT-TXT
 ```	
6. Script Aufrufen
 ```
 ./Make-TXT-BSP.sh
 ```
 Damit wird *buildroot* gecloned und der richtige commit eingestellt,
 Patches eingespielt und Hilfsscripte kopiert.
 Buildroot ist nun Konfiguriert,
 ```
 cd ../buildroot
 make
 ```
 sollte das komplette System ohne Bootloader erstellen 
 Die Ausgaben sind in 
 ./output/images
 zu finden.
7. Bootloader
 ```
 cd ../FT-TXT
 ./Make-TXT-BOOTLOADER.sh
 ```
 erstellt die Bootloader Binaries in 
 ../u-boot/bin

## Zusatzscripte

