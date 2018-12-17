#!/bin/bash
#************************************************
# posrecv Install
#***********************************************
. ./install.conf
echo "Install posrecv"

if [ "$EUID" -ne 0 ]; then
  echo "ERROR: Please run as root with sudo" >&2
  exit
fi

if [ -z $DESTDIR ]; then
	echo "ERROR: Please specify a destination directory on install.conf"
	exit -1
fi

if [ ! -d $DESTDIR ]; then
	echo "ERROR: $DESTDIR is not a valid directory please correct in install.conf"
	exit -1
fi

#terminate the service if it is already running

echo "Terminating any running service"
systemctl stop posrecv
systemctl disable posrecv

# copy in the default service file
cp posrecv.service /etc/systemd/system/
# edit the service file
sed -i "s:--DIR--:${DESTDIR}:" /etc/systemd/system/posrecv.service

OPTIONS=''
if [ ! -z ${LOGHOST+x} ] && [ -n $LOGHOST ] && [ ${#LOGHOST} -gt 0 ]; then
	OPTIONS="$OPTIONS -H $LOGHOST"
fi
if [ ! -z ${LOGHTTPPORT+x} ] && [ -n $LOGHTTPPORT ] && [ ${#LOGHTTPPORT} -gt 0 ]; then
	OPTIONS="$OPTIONS -P $LOGHTTPPORT"
fi
if [ ! -z ${LOGAPI+x} ] && [ -n $LOGAPI ] && [ ${#LOGAPI} -gt 0 ]; then
	OPTIONS="$OPTIONS -U $LOGAPI"
fi
if [ ! -z ${UDPPORT+x} ] && [ -n $UDPPORT ] && [ ${#UDPPORT} -gt 0 ]; then
	OPTIONS="$OPTIONS -p $UDPPORT"
fi
sed -i "s:--OPTIONS--:${OPTIONS}:" /etc/systemd/system/posrecv.service

#start the service
systemctl start posrecv
systemctl enable posrecv

echo "End of install"
