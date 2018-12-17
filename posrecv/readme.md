# posrecv readme
**posrecv** is a Linux daemon that receives UDP packets direct from the particle trackers.
The basic operation is to receive a UDP packet, translate it and post it via http to the gliding server tracking api.  The program is a simple C++ program which can be found in the **src** directory.
##Compile
The executable is called posrecv and can be found in the **bin** directory.
To compile:
1. Run the make.sh file by entering './make.sh'

## Installation
1. Edit the 
