//
//  main.cpp
//  IRC Bot
//
//  Created by Adam Hubscher on 12/20/11.
//  Copyright 2011 Enova Financial. All rights reserved.
//

#include "irc.h"

int main(int argc, char *argv[]) {
    
	printf("JENNI IRCBOT INITIATE\n");
    
	IRC * jenni = new IRC((char *)"jenni", (char *)"66.135.41.236", 6667);
	jenni->Connect();
	jenni->theLoop();
	return 0;
}
