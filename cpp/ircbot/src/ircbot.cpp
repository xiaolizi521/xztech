#include <iostream>
#include <cstdlib>
#include <stdio.h>
#include <cstring>
#include <errno.h>
#include <arpa/inet.h>
#include <sys/utsname.h>
#include "ircbot.h"

int main(int argc, char *argv[]) {

    class ircbot * jenni = new ircbot;
    string buffer;
    buffer.reserve(MAX_MESSAGE_SIZE);
        
    while (1) {
        
        buffer.clear();
        buffer = jenni->Read();
        buffer.reserve();

        if (!(buffer.find("PING :") == std::string::npos)) jenni->Pong(buffer.substr(4));

        if (!(buffer.find(":!hello") == std::string::npos)) jenni->privmsg("I am jenni, IRCBOT extraodinnaire.");

        // Catch-All Quit
        if (!(buffer.find(":!quit") == std::string::npos)) break;
    }

    delete jenni;
    return 0;
}
