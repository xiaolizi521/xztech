#include "irc.cpp"

int main(int argc, char *argv[]) {
	
	printf("JENNI IRCBOT INITIATE\n");
	
	IRC * jenni = new IRC((char *)"jenni", (char *)"irc.x-zen.cx", 6697);
	jenni->Connect();
	jenni->theLoop();
	return 0;
}
