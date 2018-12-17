#!/bin/bash
#
# posrecv make file
#
#
echo "Start of make"
gcc -pthread -o ./bin/posrecv ./src/posRecv.cpp
echo "End of make"
