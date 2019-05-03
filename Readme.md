# FT-TXT
This repository contains the firmware for the TXT controller based on [BUILDROOT](https://buildroot.org/downloads/manual/manual.pdf).
For questions about the software, please contact *fischertechnik-technik@fischer.de*.

## Quick Start
With existing development environment you can use the following commands to build a firmware image for the TXT controller.
  ```
  cd FT/FT-TXT
  git pull
  ./Make-TXT-Bootloader.sh
  ./Make-TXT-Buildroot-Clean.sh
  sudo ./Make-TXT-Image.sh
  ```
The compressed generated image file you can find in `../ft-TXT_Build_XXX.img.zip`

## Setup Build Environment
To build the *BSB* and the *Bootloader* you will need a linux system with a development environment.
The making of *BSP* was tested with [Ubuntu Mate 16.04](http://cdimage.ubuntu.com/ubuntu-mate/releases/16.04.4/release/ubuntu-mate-16.04.4-desktop-amd64.iso).
Install the following linux packages first.
  ```
  cd ./FT-TXT
  sudo ./Linux-Pakete-Required.sh
  (sudo ./Linux-Pakete-Extra.sh)
  ```

## Making of Rootfs, Bootloader and Kernel
### Initial
**1. Create the working directory and change to it**
  ```
  mkdir FT
  cd FT
  ```

**2. Clone the FT-TXT repository**
  ```
  git clone https://github.com/fischertechnik/FT-TXT.git
  (git clone https://gitlab.com/fischertechnik/FT-TXT.git)
  ```

**3. Create directory for the toolchain**
  ```
  sudo mkdir /opt/FT
  sudo chmod a+rw /opt/FT
  ```
### Frequently
**4. Change to the FT-TXT directory**
  ```
  cd FT-TXT
  ```	

**5. Build Bootloader**
  ```
  ./Make-TXT-Bootloader.sh
  ```
  The generated bootloader binaries can be found in `../u-boot/bin`

**6. Clone, configure and build Buildroot**
  ```
  ./Make-TXT-Buildroot-Clean.sh
  ```
  This script clones *Buildroot*, setup the right commit, patch and copy auxiliary scripts. Afterwards the *Buildroot* will be built.
  
  An incremental reconfiguration with incremental build can be started via the script:
  ```
  ./Make-TXT-Buildroot-Incremental.sh
  ```
  The output can be found in `FT-TXT/../buildroot/output/images`.

### Optional
**7. Create Update scripts**
  ```
  ./Make-TXT-UpdateScripts.sh
  ./Sign-Connect-Reader.sh
  ./Sign-TXT-UpdateScripts.sh
  ```
  Update scripts can be used to update the firmware on a TXT without using a flash card.
  The update scripts and signatures can be found in `FT-TXT/../update`.

**8. Create Graphs**
  ```
  ./Make-TXT-Graphs.sh
  ```
  Graphing the dependencies between packages, build duration and filesystem size contribution of packages.
  Required packages: `sudo apt install python-matplotlib python-numpy`
  You will find the generated graphs in `FT-TXT/../buildroot/output/graphs/`.

## Additional Scripts
For description of additional scripts read [Additional-Scripts.md](/Additional-Scripts.md).