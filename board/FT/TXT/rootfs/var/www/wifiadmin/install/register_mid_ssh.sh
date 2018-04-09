#!/bin/bash
### Loged in as apache user
### Register ssh reouter at local server

[ -z "$1" -o "$1" != "install" -o -z "$2" -o -z "$3" ] && { echo "Not to be used as a standalone script... Exiting" ; exit 1 ;}
user="$2"
host="$3"

APAU="$USER"
[ -z "$APAU" ] && { echo "USER env variable not set. Who are you?? Exiting.." ; exit 1 ;}


HOM=$(grep $APAU: /etc/passwd | awk 'BEGIN{FS=":"}; {print $6}')

if [ ! -w "$HOM" ] ; then
	echo "$APAU home directory $HOM not writable. Fix Manually. Exiting."
	exit 1
fi

cd "$HOM"

echo -e "\n   [Step 1] Server RSA key generation. Leave passphrase **EMPTY** (press enter when asked)";

#echo "Proceed [y] ? "
#if (read)
NOGEN=0
if [ -r .ssh/id_rsa.pub ] ;then
	echo "Found id RSA keys. Make sure passphrase is empty or else it will be needed on every login."
	echo "If you are not sure don't use them. New keys will be generated"
	echo "Do you want to use existing rsa keys?(y/n)"
	read -n 1 rep
	echo
	if [ "$rep" = "y" ] ; then
		NOGEN=1
	fi
fi
if [ "$NOGEN" = "0" ]; then
	ssh-keygen -t rsa
fi;

echo -e "\n   [Step 2] Register user ${user}@${host} to $APAU@localhost. You will be asked for first and last time for ${user}'s password."
key=$(cat .ssh/id_rsa.pub)
if [ -z "$key" ] ; then echo "Error: RSA key not found .Someting went wrong. Exiting..." ; exit 1 ; fi
if ! ssh -o BatchMode=yes $user@${host} echo "" ; then
	ssh ${user}@${host} 'mkdir -p .ssh; echo '$key' >> .ssh/authorized_keys' 
	if [ $? -ne 0 ] ; then
		echo "* Error registering. Read SSH_NOPASSWD and try to find a solution.I'm giving up";
		exit 1;
	fi
	if ! ssh -o BatchMode=yes $user@${host} echo "" ; then
		echo "* Error. Supposed to have registered but we are not. Read SSH_NOPASSWD and try to fix manualy. I'm giving up";
		exit 1
	else
		echo "Successfully Registered"
	fi
else	
	echo "Already registered"
fi

exit 0

