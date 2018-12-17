/* posRecv.h */

/* UDP Message types */
#define MESSAGE_TYPE_POSITION	255
#define MESSAGE_TYPE_HELLO		254
#define MESSAGE_TYPE_DEBUG		253
#define MESSAGE_TYPE_NO_FIX		252
#define MESSAGE_TYPE_BATTERY	251

/* The total number of positions in one UDP_POS_MESSAGE */
#define NUM_POSISTIONS 3

typedef struct _udphdr {
	uint8_t			flag;
	uint8_t			id;
	uint32_t		tm;
}  __attribute__((packed)) UDP_HEADER;

typedef struct _relative_pos {
	short tm;
	short lat;
	short lon;
	short alt;
} RELATIVE_POS;

typedef struct _udp_pos_msg {
	UDP_HEADER		hdr;
	float			lat;
	float			lon;
	float			alt;
	RELATIVE_POS	rel_pos[NUM_POSISTIONS-1];
	uint8_t			checksum;
}  __attribute__((packed)) UDP_POS_MESSAGE;

#define PACKETSIZE sizeof(UDP_POS_MESSAGE)

typedef struct _udp_nofix_msg {
	UDP_HEADER		hdr;
	uint8_t			debug_seq;
	uint8_t			debug_num;
	uint32_t		debug_val;
} __attribute__((packed)) UDP_NO_FIX_MESSAGE;

typedef struct _udp_debug_msg {
	UDP_HEADER		hdr;
	uint8_t			debug_seq;	
	uint8_t			debug_num;
	uint32_t		debug_val;
	float			debug_float;
} __attribute__((packed)) UDP_DEBUG_MESSAGE;

typedef struct _udp_battery_msg {
	UDP_HEADER		hdr;
	float			level;
} __attribute__((packed)) UDP_BATTERY_MESSAGE;
#define MAX_THREADS			30

typedef struct _thread_Params {
	char	postMsg[1024];
} THREAD_PARAMS;
