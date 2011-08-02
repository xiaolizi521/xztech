#include <stdio.h>
#include <stdlib.h>
#include <errno.h>
#include <string.h>
#include <stdarg.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>

#define SOCK_PATH "/tmp/kslog"

char * make_message(const char *fmt, ...);
int kslog (const char *message);
int kslogerr (const char *error);

char * make_message(const char *fmt, ...) {
    /* Guess we need no more than 100 bytes. */
    int n, size = 100;
    char *p;
    va_list ap;
    if ((p = malloc (size)) == NULL) {
        return NULL;
    }
    while (1) {
        /* Try to print in the allocated space. */
        va_start(ap, fmt);
        n = vsnprintf (p, size, fmt, ap);
        va_end(ap);
        /* If that worked, return the string. */
        if (n > -1 && n < size) {
            return p;
        }
        /* Else try again with more space. */
        if (n > -1)  {  /* glibc 2.1 */
            size = n+1; /* precisely what is needed */
        } else {           /* glibc 2.0 */
            size *= 2;  /* twice the old size */
        }
        if ((p = realloc (p, size)) == NULL) {
            return NULL;
        }
    }
}

int kslog (const char *message) {
	int sockfd;
	struct sockaddr_in servaddr;

	if ((sockfd = socket(AF_INET, SOCK_STREAM, 0)) == -1) {
	/*	kslogerr("socket\n");	*/
		return(1);
	}

	bzero(&servaddr, sizeof(servaddr));
	servaddr.sin_family = AF_INET;
	servaddr.sin_port = htons(226);
	inet_pton(AF_INET, "127.0.0.1", &servaddr.sin_addr);

	if (connect(sockfd, (struct sockaddr *) &servaddr,
		sizeof(servaddr)) < 0) {
	/*	kslogerr("connect\n");	*/
	}

	if (send(sockfd, message, strlen(message), 0) == -1) {
	/*	kslogerr("send\n");	*/
		return(4);
	}

	close(sockfd);

	return 0;
}

int kslogerr (const char *error)
{
	FILE *fp;
	fp = fopen("/tmp/kslog.err", "a");
	fputs(error, fp);
	fclose(fp);
	return 0;
}
