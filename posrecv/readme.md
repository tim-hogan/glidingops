# posrecv readme
**posrecv** is a Linux daemon that receives UDP packets direct from the particle trackers.
The basic operation is to receive a UDP packet, translate it and post it via http to the gliding server tracking api.  The program is a simple C++ program which can be found in the **src** directory.

## Compile
The executable is called posrecv and can be found in the **bin** directory.
To compile:
1. Run the make.sh file by entering `./make.sh`

## Installation
You will need to have created or use an existing directory for the executable.  
You will also need to know:
1. The host name of the logging host:  e.g. **glidingops.com**
1. The api name on that host for position information.
1. The port number if different from 80 for the http api posts.
1. The udp port number that the daemon listens on.

### Installation Instructions
1. Create a directory (if it does not exist) for the daemon executable.  Example `sudo mkdir /usr/bin/posrecv`
1. Open install.conf file
1. Edit **DESTDIR** with the directory name specified above
1. Edit **LOGHOST** with the name of the tracking host
1. Edit **LOGHTTPPORT** with api http port isf other than 80
1. Edit **LOGAPI** with
1. Edit **UDPPORT** with the UDP port the daemon is listening on.
1. Run the install script by entering `sudo ./install.sh`

This will install the posrecv daemon, and run it.  By default, the daemon will start
automatically on boot and restart if it fails.

## Star/Stop and checking the service
To **Start** service enter:  
&nbsp;&nbsp;&nbsp;`sudo service posrecv start`  
To **Stop** service enter:  
&nbsp;&nbsp;&nbsp;`sudo service posrecv stop`   
To check service **Status** enter:  
&nbsp;&nbsp;&nbsp;`sudo service posrecv status`  
