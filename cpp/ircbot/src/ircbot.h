using namespace std;

string CRLF = "\r\n";
pid_t PID;

/* Preprocessors set according to RFC */

#ifndef MAX_MESSAGE_SIZE
#define MAX_MESSAGE_SIZE 512
#endif

#ifndef MAX_NICK_SIZE
#define MAX_NICK_SIZE 12
#endif


static void bail(const char *on_what); 
void fork(void);

class ircbot
    {
    private:
        string nickName;
        string realName;
        string nickPass;
        string serverPass;
        string hostName;
        char prefix;
        int sockDesc;
        int port;
        int rBytes;
        int wBytes;
        struct sockaddr_in serverAddress; 
        char readbuf[MAX_MESSAGE_SIZE];
        char writebuf[MAX_MESSAGE_SIZE];
        string channel;
    protected:
        void Disconnect(void);
        void Pass(void);
        void User(void);
        void Join(string);
        void Nick(void);
        void Quit(string);
        void Send(string);
    public:
        void Connect(void);
        ircbot(void);
        ~ircbot(void);
        string Read(void);
        void privmsg(string);
        void Pong(string);
    };

void ircbot::Send(string _message) {

    printf("Sending %s\n", _message.c_str());

    memset(&writebuf, 0, sizeof writebuf);
    _message.copy(writebuf, sizeof writebuf);

    }

void ircbot::Pong(string ping = "") {
    
    string pong = "PONG" + ping + CRLF;
    Send(pong);
    }

void ircbot::privmsg(string mesg) {
    
    mesg = "PRIVMSG " + channel + " :" + mesg + CRLF;
    Send(mesg);
    }

void ircbot::Quit(string qmesg) {
    
    qmesg = "QUIT :" + qmesg + CRLF;
    Send(qmesg);
    }

void ircbot::Nick(void) {
    
    nickName = "NICK " + nickName + CRLF;
    Send(nickName);
    }

void ircbot::Join(string chn) {
    
    chn = "JOIN " + chn + CRLF;
    Send(chn);
    }

void ircbot::User(void) {
    
    realName = "USER jenni irc.x-zen.cx phobos : " + realName + CRLF;
    Send(realName);
    }

void ircbot::Pass(void) {
    
    nickPass = "PASS " + nickName + CRLF;
    Send(nickPass);
    }

string ircbot::Read(void) {
    
    memset(&readbuf, 0, sizeof readbuf);
    rBytes = read(sockDesc, readbuf, sizeof readbuf - 1);
    if (rBytes == -1) bail("read()");
    
    string messmanip = readbuf;
    return messmanip;
    }

void ircbot::Connect(void) {

    int success = 0;
    
    serverAddress.sin_family = AF_INET;
    serverAddress.sin_addr.s_addr = inet_addr(hostName.c_str());
    serverAddress.sin_port = htons(port);
    
    sockDesc = socket(PF_INET, SOCK_STREAM, IPPROTO_TCP);
    
    if (sockDesc == -1) bail("socket()");

    success = connect(sockDesc, (const sockaddr *)&serverAddress, sizeof serverAddress);
    
    if (success == -1) bail("connect()");
    string work;

    while(1){

        work.clear();
        work = Read();
        
        printf("BUFFER: %s\n", work.c_str());
        if(!(work.find("NOTICE AUTH :") == std::string::npos)){
            
            Pass();
            Nick();
        }
        if (!(work.find("PING :") == std::string::npos)){
            Pong(work.substr(4));
            sleep(1);
            User();
            sleep(1);
            break;
        }

    }

    Join("#blah");
}

void ircbot::Disconnect(void){
    Quit("All those moments... lost, in time... like tears, in the rain.");
    sleep(1);
    close(sockDesc);
    }

ircbot::ircbot() {
    port = 6667;
    hostName = "66.135.41.236";
    channel = "#blah";
    nickName = "jenni";
    realName = "jenni";
    }

ircbot::~ircbot() {
    Disconnect();
    }
