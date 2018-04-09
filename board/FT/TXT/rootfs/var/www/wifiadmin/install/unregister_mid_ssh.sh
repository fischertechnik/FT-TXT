#!/bin/bash
### Loged in as apache user
### UnRegister ssh router at local server

[ -z "$1" -o "$1" != "uninstall" -o -z "$2" -o -z "$3" ] && { echo "Not to be used as a standalone script... Exiting" ; exit 1 ;}
user="$2"
host="$3"

APAU="$USER"
[ -z "$APAU" ] && { echo "USER env variable not set. Who are you?? Exiting.." ; exit 1 ;}


HOM=$(grep $APAU: /etc/passwd | awk 'BEGIN{FS=":"}; {print $6}')

if [ ! -r "$HOM" ] ; then
	echo "$APAU home directory $HOM not readable. Fix Manually. Exiting."
	exit 1
fi

cd "$HOM"


if [ -r .ssh/id_rsa.pub ] ;then
	echo "Found id RSA keys. Using them."
else
	echo "No RSA keys found on the server. Did you update anything? We can not remove the correct id from remote host. Fix Manualy. Exiting.."
	exit 1
fi;

echo -e "\nUnRegister user ${user}@${host} from $APAU@localhost."
key=$(cat .ssh/id_rsa.pub)
if [ -z "$key" ] ; then echo "Error: RSA key not found .Someting went wrong. Exiting..." ; exit 1 ; fi
if ssh -o BatchMode=yes $user@${host} echo "" ; then
	ssh ${user}@${host} "cat ~/.ssh/authorized_keys | grep -v \"$(echo $key)\" > ~/new_keys ; mv ~/new_keys ~/.ssh/authorized_keys"

	if [ $? -ne 0 ] ; then
		echo "* Error unregistering. Read SSH_NOPASSWD and try to find a solution.I'm giving up";
		exit 1;
	fi

	if ssh -o BatchMode=yes $user@${host} echo "" ; then
		echo "* Error. Supposed to have unregistered but we are not. Read SSH_NOPASSWD and try to fix manualy. I'm giving up";
		exit 1
	else
		echo "Successfully UnRegistered"
	fi
else	
	echo "Current RSA key Already Unregistered"
fi

exit 0

