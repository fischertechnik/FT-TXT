
# FT-TXT : 

## Rootfs / Kernel / Apps

Erstellung mit Buildroot, Bootloader "Das U-Boot"

Voraussetzungen:

Sie benötigen zum Erstellen des BSB und des Bootlodaers ein Linux System mit Entwicklungsumgebung. (mit einem Intelk i7 und 16 GB Ram dauert das Übersetzen ca. eine Stunde !)

Das BSP wurde unter ubuntu mate 16.04 erstellt und getestet:

im Script ./LinuxPakete.sh sind alle zusätzlichen Pakte die verwendet wurden enthalten.

[Download ubuntu mate 16.04](http://cdimage.ubuntu.com/ubuntu-mate/releases/16.04.4/release/ubuntu-mate-16.04.4-desktop-amd64.iso) 

zum Installieren der Zusatzpakete:

 ```
 cd ./FT-TXT
 sudo ./LinuxPakte.sh
 ```
durchführen

danach sollten Sie ein System zur Verfügung haben mit dem das BSP und der Bootloader wie angegeben übersetzt/erstellt werden kann.

Rootfilesystem
[BuildrootManual](https://buildroot.org/downloads/manual/manual.pdf) 

Allgemeines

Imageerstellung
[kpartx](https://robert.penz.name/73/kpartx-a-tool-for-mounting-partitions-within-an-image-file/) 


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

###MakeImage.sh

Erstellt ein SD Kartenimage mit Bootsektion und Rootfilesystem

