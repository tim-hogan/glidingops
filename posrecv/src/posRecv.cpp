//posRecv.cpp
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netdb.h>
#include <pthread.h>
#include "posRecv.h"

/* Globals */
int			g_portno = 3001;
bool		g_verbose = false;
char		g_szLogHost[256];
char		g_szUrl[256];
int		    g_logHostPort = 80;
int			g_nextThread = 0;

THREAD_PARAMS * pThread_Params = (THREAD_PARAMS *) malloc(sizeof(THREAD_PARAMS) * MAX_THREADS);
pthread_t threads[MAX_THREADS];

void verbose_out(const char * m)
{
	if (g_verbose)
	{
		time_t rawtime;
		struct tm * timeinfo;
		char szT[64];

		time ( &rawtime );
		timeinfo = localtime ( &rawtime );
		sprintf (szT,"%s", asctime (timeinfo) );
		szT[strlen(szT)-1] = 0;  //Remove the CR
		strcat(szT," ");
		printf("%s",szT);
		printf("%s",m);
		fflush(stdout);
	}
}

inline void incrThread()
{
	g_nextThread =  (g_nextThread+1) % MAX_THREADS;
}

void jsonpairint(char * buff, const char * tag,int v,bool comma = true)
{
	sprintf(buff + strlen(buff),"\"%s\":%d",tag,v);
	if (comma) sprintf(buff + strlen(buff),",");
}

void jsonpairfloat(char * buff, const char * tag,float v,bool comma = true)
{
	sprintf(buff + strlen(buff),"\"%s\":%f",tag,v);
	if (comma) sprintf(buff + strlen(buff),",");
}

bool postData(char * pData)
{
    verbose_out("Start of Post Data\n");
	char message_fmt[256];
	if (strlen(g_szUrl) > 0)
	{
		strcpy(message_fmt,"POST /");
		strcat(message_fmt,g_szUrl);
		strcat(message_fmt," HTTP/1.1\r\n");
	}
	else
		strcpy(message_fmt,"POST /apiParticlejsonv1.php?r=udploc HTTP/1.1\r\n");

	char szTemp[256];

	struct hostent *server;
    struct sockaddr_in serv_addr;
    int sockfd, bytes, sent, received, total;
    char message[1024];

	strcpy(message,message_fmt);
	sprintf(message + strlen(message),"Content-Length: %ld\r\nContent-Type: application/json;charset=UTF-8\r\nHost: %s\r\n",strlen(pData),g_szLogHost);
	strcat(message,"User-Agent: deVT/1.2.0\r\n");
	strcat(message,"User-Agent: deVT/1.2.0\r\nAccept: */*\r\n\r\n");
	strcat(message,pData);

	sockfd = socket(AF_INET, SOCK_STREAM, 0);
    if (sockfd < 0) 
	{
		verbose_out("Error creating socket\n");
		return false;
	}
	server = gethostbyname(g_szLogHost);
    if (server == NULL) 
	{
		verbose_out("Error gethostbyname\n");
		return false;
	}


	memset(&serv_addr,0,sizeof(serv_addr));
    serv_addr.sin_family = AF_INET;
    serv_addr.sin_port = htons(g_logHostPort);
    memcpy(&serv_addr.sin_addr.s_addr,server->h_addr,server->h_length);

	if (connect(sockfd,(struct sockaddr *)&serv_addr,sizeof(serv_addr)) < 0)
 	{
		verbose_out("Error connecting to host\n");
		return false;
	}
	
	total = strlen(message);
    sent = 0;
    do {
        bytes = write(sockfd,message+sent,total-sent);
        if (bytes < 0)
		{
            verbose_out("ERROR writing message to socket\n");
			return false;
		}
        if (bytes == 0)
            break;
        sent+=bytes;
    } while (sent < total);

	verbose_out(" post data sent\n");
	//Dont need the response
	close(sockfd);

}

void * thread_Procees(void *p)
{
	verbose_out("Start of thread\n");
	THREAD_PARAMS * pr = (THREAD_PARAMS *) p;
	postData(pr->postMsg);
	verbose_out("End of thread\n");
	pthread_exit(NULL);
}

void process_debug(UDP_DEBUG_MESSAGE * pDebug)
{
	char szText[256];

	sprintf(szText,"Debug Message from %d Time: %d, Seq: %d Type: %d Long value: %d Float Val: %f\n",pDebug->hdr.id,pDebug->hdr.tm,pDebug->debug_seq,pDebug->debug_num,pDebug->debug_val,pDebug->debug_float);
	verbose_out(szText);
	fflush(stdout);
}

void process_battery(UDP_BATTERY_MESSAGE * pBatt)
{
	verbose_out("Start of process_battery\n");
	
	THREAD_PARAMS * pt = pThread_Params + g_nextThread;
	char * pMsg = pt->postMsg;

	/* Prepare JSON */
	strcpy(pMsg,"{\"b\":{");
	jsonpairint(pMsg, "v",pBatt->hdr.id);
	jsonpairfloat(pMsg, "l",pBatt->level,false);
	strcat(pMsg,"}}");

	verbose_out(" Built JSON About to start thread\n");

	pthread_create(&(threads[g_nextThread]), NULL, thread_Procees, (void *) pt);
	incrThread();

	verbose_out(" End of process_battery\n");

}

void process_nofix(UDP_HEADER * pHdr)
{
	verbose_out("Start of process_nofix\n");
	
	THREAD_PARAMS * pt = pThread_Params + g_nextThread;
	char * pMsg = pt->postMsg;

	/* Prepare JSON */
	strcpy(pMsg,"{\"n\":{");
	jsonpairint(pMsg, "v",pHdr->id,false);
	strcat(pMsg,"}}");

	verbose_out(" Built JSON About to start thread\n");

	pthread_create(&(threads[g_nextThread]), NULL, thread_Procees, (void *) pt);
	incrThread();

	verbose_out(" End of process_nofix\n");
}

void process_postition(UDP_POS_MESSAGE * pPos)
{
	verbose_out("Start of process_position\n");
	
	THREAD_PARAMS * pt = pThread_Params + g_nextThread;
	char * pMsg = pt->postMsg;

	/* Prepare JSON */
	strcpy(pMsg,"{\"p\":{");
	jsonpairint(pMsg, "v",pPos->hdr.id);
	strcat(pMsg,"\"p\":[{");
	jsonpairint(pMsg,"t",pPos->hdr.tm);
	jsonpairfloat(pMsg,"lat",pPos->lat);
	jsonpairfloat(pMsg,"lon",pPos->lon);
	jsonpairfloat(pMsg,"alt",pPos->alt,false);
	
	//Loop here for each position
	for (int i=0;i < NUM_POSISTIONS-1;i++)
	{
		if (pPos->rel_pos[i].tm > 0)
		{
			strcat(pMsg,"},{");
			jsonpairint(pMsg,"t",pPos->hdr.tm + pPos->rel_pos[i].tm);
			jsonpairfloat(pMsg,"lat",pPos->lat + ((float) (pPos->rel_pos[i].lat) / 1000000.0));
			jsonpairfloat(pMsg,"lon",pPos->lon + ((float) (pPos->rel_pos[i].lon) / 1000000.0))	;
			jsonpairfloat(pMsg,"alt",(float) pPos->rel_pos[i].alt,false);
		}
	}
	strcat(pMsg,"}]}}");

	verbose_out(" Built JSON About to start thread\n");

	pthread_create(&(threads[g_nextThread]), NULL, thread_Procees, (void *) pt);
	incrThread();

	verbose_out(" End of process_position\n");

}

void printHelp()
{
	printf("\033[1mposRecv\n\n");
	printf("NAME\033[0m\n");
	printf("\tposRecv - Receives UDP Packets from a particle.io IoT Device with Adafruit GSP Module.\n\n");
	printf("\033[1mUSAGE\033[0m\n");
	printf("\tposRecv [-h] [-p udp_portnumber] [-V] [-U url] [-P log_host_http_port] -H hostname\n\n");
	printf("\033[1mOPTIONS\033[0m\n");
	printf("\t-h\tPrints help (this page) and exits.\n\n");
	printf("\t-H\tSets the destination host name for the http JSON message\n\t\tparsed from the UDP packet received.\n\n");
	printf("\t-U\tSets the URL for the http JSON message\n\t\tparsed from the UDP packet received.\n\n");
	printf("\t-P\tSets the http port for the host -H. If not set defaults to \n\t\tport 80\n\n");
	printf("\t-p\tSets the receiving UDP port.  If not set defaults to \n\t\tport 3001\n\n");
	printf("\t-V\tSets verbose mode.\n\n");

}


void parseOptions(int argc, char *argv[])
{
	int c;
	while ((c = getopt (argc, argv, "hH:P:p:U:V?")) != -1)
	{
		switch(c)
		{
		case 'H': //Host
			if (optarg)
				strcpy(g_szLogHost,optarg);
			break;
		case 'p':
			g_portno = atoi(optarg);
			break;
		case 'P':
			g_logHostPort = atoi(optarg);
			break;
		case 'U':
			if (optarg)
				strcpy(g_szUrl,optarg);
			break;
		case 'V':
			g_verbose = true;
			break;
		case 'h':  //Help
			printHelp();
			exit(0);
			break;
		}
	}
	//Check thta mandatory have been set
	if (strlen(g_szLogHost) == 0)
	{
		printf("ERROR: You must set a remote logging host with the -H options\nFor help run again with -h\n\n");
		exit(0);
	}
}

void dump_message(unsigned char * buff)
{
	if (g_verbose)
	{
		printf("Dump of UDP Message: Buffer = ");
		for (int i = 0; i < PACKETSIZE; i++)
			printf("%02X",buff[i]);
		printf("\n");
	}
}

int main (int argc, char *argv[])
{
	int sockfd;
	int optval;
	int i;
	int n;
	socklen_t clientlen;
	struct sockaddr_in serveraddr;
	struct sockaddr_in clientaddr;
	unsigned char buf[PACKETSIZE]; 

	/* Get the options */
	parseOptions(argc,argv);
	
	/* Start of main */
	verbose_out("Start\n");
	

	/* Create udp socket */
	sockfd = socket(AF_INET, SOCK_DGRAM, 0);
	if (sockfd < 0)
	{
		verbose_out("Error creating socket\n");
		exit(0);
	}

	/* Set scoket options */
	optval = 1;
	setsockopt(sockfd, SOL_SOCKET, SO_REUSEADDR, (const void *)&optval , sizeof(int));

	bzero((char *) &serveraddr, sizeof(serveraddr));
	serveraddr.sin_family = AF_INET;
	serveraddr.sin_addr.s_addr = htonl(INADDR_ANY);
	serveraddr.sin_port = htons((unsigned short)g_portno);

	/* Bind scoket */
	if (bind(sockfd, (struct sockaddr *) &serveraddr, sizeof(serveraddr)) < 0)
	{
		verbose_out("Error on binding\n");
		exit(0);
	}

	/* Main loop */
	while (1)
	{
		verbose_out("Start of receive loop\n");
		bzero(buf, 16);
		n = recvfrom(sockfd, buf, PACKETSIZE, 0, (struct sockaddr *) &clientaddr, &clientlen);
		if (n < 0)
		{
			printf("Error in receiving %d\n",n);
			//exit(0);
		}

		/* WE have some data */
		
		verbose_out("Received UDP Packet\n");
		
		/* Check the checksum */
		if (n == PACKETSIZE)
		{
			unsigned char c = 0;
			for (i = 0; i < (PACKETSIZE -1);c ^= buf[i++]);
			if (c == buf[(PACKETSIZE -1)])
			{
				UDP_HEADER *pHdr = (UDP_HEADER *) buf;
		
				if (g_verbose)
				{
					printf("Message type %d received\n",pHdr->flag);
					dump_message(buf);
				}

				switch (pHdr->flag)
				{
				case MESSAGE_TYPE_BATTERY: //Battery status
					process_battery((UDP_BATTERY_MESSAGE *) buf);
					break;
				case MESSAGE_TYPE_NO_FIX:  //No fix
					verbose_out("Received a no fix packet\n");
					process_nofix((UDP_HEADER * ) buf);
					break;
				case MESSAGE_TYPE_DEBUG:  //Debug
					process_debug( (UDP_DEBUG_MESSAGE *) buf);
					break;
				case MESSAGE_TYPE_HELLO: //Hello
					break;
				case MESSAGE_TYPE_POSITION:  //Position report
					process_postition( (UDP_POS_MESSAGE *) buf);
					break;
				default:
					dump_message(buf);
					break;
				}
			}
			else
			{
				if (g_verbose)
					printf("Checksum failed %d vs %d in UDP message\n",c,buf[(PACKETSIZE -1)]);
			}
			
		}
		verbose_out("End of receive loop\n");
	}
}