// Standard Lib Includes
#include <iostream>
#include <stdlib.h>
#include <stdio.h>
#include <errno.h>
#include <time.h>
#include <stdint.h>

// Additional "Extra" Includes
#include <map>
#include <vector>

// Private Includes
#include "socket.h"
#include "define.h"
#include "irc.h"

int main(int argc, char *argv[]) {

    irc_host jenni;
    int32_t status;
    
    jenni.nick = (char *)"jenni";
    jenni.server = (char *)"xztech.org";
    jenni.server_name = (char *)"xztech";
    jenni.port = 6667;
    jenni.pass = (char *) "";
    jenni.user = (char *) "jenni";
    
    
    IRCBot bot(jenni);
    
    while(1) {
        
        status = bot.mRecv();
        
        if (status > 0) {
            
            bot.pingPong();
        }
        
        if (status < 0) {
            break;
        }
    }
	return 0;
}
