#!/bin/sh
# set DNS

VarIp=""

function Check_Ip() 
{
  ip=$VarIp
	if expr "$ip" : '[0-9][0-9]*\.[0-9][0-9]*\.[0-9][0-9]*\.[0-9][0-9]*$' >/dev/null; then
	  IFS=.
	  set $ip
	  for quad in 1 2 3 4; do
	    if eval [ \$$quad -gt 255 ]; then
#	      echo "falsch - [$ip]"
	      return 1
	    fi
	  done
#	  echo "ok [$ip]"
	  return 0
	else
#	  echo "falsch [$ip]"
	  return 1
	fi
  return 0;
}


  VarIp=$1
  if Check_Ip
  then
    echo "IP [$VarIp] OK - Now set DNS:$1"
    /bin/grep -q  nameserver /etc/resolv.conf && sudo /bin/sed -i "s/\(nameserver\) .*/\1 $1/" /etc/resolv.conf || echo "nameserver $1" >> /etc/resolv.conf    
  fi

  exit $?
