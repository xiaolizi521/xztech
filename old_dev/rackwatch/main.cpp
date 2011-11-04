/***************************************************************************
 *   Copyright (C) 2003 by Gunther Piez                                    *
 *   gpiez@users.sourceforge.net                                                          *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 ***************************************************************************/

#ifdef HAVE_QT
#include "mainwindow.h"
#include <QApplication>
#endif

#ifdef __WIN32__
#include <windows.h>
#else
#include <pthread.h>
#endif
#include <cstdio>
#include <cstdlib>
#include <csignal>
#include <unistd.h>
#include <getopt.h>
#include <cerrno>
#include <cstring>
#include <sys/types.h>
#include <sys/stat.h>
#ifdef __unix__
#include <sys/select.h>
#include <wordexp.h>
#endif
#include <sys/time.h>
#include <cstdarg>
#include <cmath>

#ifdef HAVE_CURSES
#include <ncurses.h>
#endif

#include "main.h"
#include "mainboard.h"
#include "opening.h"
#include "searchr.h"
#include "parse.h"
#include "util.h"
#include "extension.h"

//time control variables. indices are 0=black, 1=analysis, 2=white.
//time is measured in s. 10^15 s = 30 yars, sufficient for
//a long analysis and anyway in 2^32 s unix systime wraps.
SharedData<uint64_t> timeRemaining[3]; //remaining time for movesRemaining[] moves.
SharedData<unsigned> movesRemaining[3];
SharedData<int> timeRunning(0); //indicates who's time is running
//timeRemaining[] and movesRemaining[] get initialized with this
uint64_t initTime[3] = { 300 S, 1000000000000000ULL, 300 S };
unsigned initMoves[3] = { 40, 1, 40 };
double lastTC; //when the last time control was
unsigned cpuclock;

bool pondering = false; //if searchm is pondering atm
bool pondermode = true; //wether it is allowed to ponder
bool computerTurn = false;

char responseBuffer[256];

WINDOW *lineWnd, *statWnd, *boardWnd, *inputWnd, *nodeWnd, *moveWnd;
MainWindow* mainWindow;

Stats statsx;

Mode mode = Normal;
Flags flags;

/** No descriptions */
void setTimeout(double t_0, double t_1) {
	t0 = lrint(t_0);
	t1 = lrint(t_1);
}

uint64_t searchTime() {
	struct timeval m;
	gettimeofday(&m, 0);
	return (uint64_t) m.tv_sec * 1000000 + m.tv_usec;
}

void output(const char *s, ...) {
	va_list ap;
	va_start(ap, s);
	if (flags.xboard) {
		setbuf(stdout, NULL);
		vprintf(s, ap);
	} else
		vsnprintf(responseBuffer, sizeof(responseBuffer), s, ap);
#ifdef MYDEBUG

	if (flags.debug && DEBUG_IO)
		debug("out: %s\n", responseBuffer);
#endif

	va_end(ap);
}

void unPonder() {
	if (pondering) {
		board.stop();
		board.undoMove();
		pondering = false;
	}
}

void inputMove(const char *s) {
	Move m;

	if (!board.str2move(s, &m)) {
		output("illegal move: %s\n", s);
	} else {
		if (!flags.xboard)
			output("move %s accepted\n", s);
		if (pondering) {
			if (ponderMoveOld.special == m.special && ponderMoveOld.src == m.src && ponderMoveOld.dst == m.dst) {
				setTC();
				computerTurn = true;
				pondering = false;
			} else {
				board.stop();
				board.undoMove();
				board.doMove(m);
				board.copy();
				setTC();
				computerTurn = true;
				pondering = false;
				board.start();
			}
		} else if (mode == Analyze || mode == Force || mode == Autoplay || mode == Bench) {
			board.stop();
			board.doMove(m);
			board.copy();
			setTC();
			computerTurn = true;
			if (mode != Force)
				board.start();
		}
	}
}

void initTC() {
	for (int col = 0; col <= 2; col++) {
		timeRemaining[col] = initTime[col];
		movesRemaining[col] = initMoves[col];
	}
	timeRunning = 0;
}

void setTC() {
	maxNodes = ~0ULL;
	double tc;
	tc = searchTime();
	if (!--movesRemaining[timeRunning + 1]) {
		movesRemaining[timeRunning + 1] += initMoves[timeRunning + 1];
		timeRemaining[timeRunning + 1] += initTime[timeRunning + 1];
	}
	timeRemaining[timeRunning + 1] -= lrint(tc - lastTC);

	if (mode == Analyze || mode == Force)
		timeRunning = 0;
	else
		timeRunning = board.getTurn();

	lastTC = tc;

	t0 = timeRemaining[timeRunning + 1] / (movesRemaining[timeRunning + 1] + 1) / 4;
	t1 = timeRemaining[timeRunning + 1] / (movesRemaining[timeRunning + 1] + 1) * 2;
}

//for use in xboard mode (without ncurses).
//polls stdin for 0.1 sec, and builds a input line.
//calls parse() if it gets a \n. does no output besides parse()
static void readKeyX() {
#ifdef HAVE_CURSES
	if (!isendwin()) {
		endwin();
		setlinebuf(stdout);
	}
#endif

	fd_set rfds;
	struct timeval tv;
	int retval;
	static char buf[256];
	static char *pbuf = buf;

	/* Watch stdin (fd 0) to see when it has input. */
	FD_ZERO(&rfds);
	FD_SET(0, &rfds);
	tv.tv_sec = 0;
	tv.tv_usec = 100000;
	retval = select(1, &rfds, NULL, NULL, &tv);
	if (retval) {
		int c = 0;
		read(0, &c, 1);
		if (pbuf - buf >= (signed) sizeof(buf) - 3) {
			*pbuf++ = c;
			c = '\n';
		}
		if (c == '\n') {
			*responseBuffer = 0;
			parse(split<string> (buf));
			pbuf = buf;
		} else
			*pbuf++ = c;
		*pbuf = 0;
	}
}

#ifdef HAVE_CURSES
//for use in interactive mode.
//polls stdin for 0.1 sec and builds a input line,
//calls parse() when it gets a newline.
//updates all windows every 0.1 sec.
static void readKey() {
	static char buf[256];
	static char *pbuf = buf;
	int c;
	if ((c = getch()) != ERR) {
		if (pbuf - buf >= (signed) sizeof(buf) - 3) {
			*pbuf++ = c;
			c = '\n';
		}
		switch (c) {
		case KEY_BACKSPACE:
			if (pbuf > buf)
				*--pbuf = 0;
			break;
		case KEY_HOME:
			addPos();
			output("position added");
			break;
		case KEY_END:
			delPos();
			output("position deleted");
			break;
		case '\n':
			*responseBuffer = 0;
			parse(split<string> (buf));
			pbuf = buf;
			if (flags.xboard)
				return;
			break;
		default:
			*pbuf++ = c;
		}
		*pbuf = 0;
	}
	//            printNode(nodeWnd);
	board.display(boardWnd);
	attrset(COLOR_PAIR(1));
	printStats(statWnd);
	for (c = -1; c < 2; c += 2) {
		int64_t tr = timeRemaining[c + 1];
		if (c == timeRunning)
			tr -= lrint(searchTime() - lastTC);
		tr /= 1000000;
		mvwprintw(boardWnd, 10 + c, 2, "%c%4d:%02d:%02d", tr > 0 ? ' ' : '-', abs(tr) / 3600, (abs(tr) / 60) % 60, abs(
				tr) % 60);
	}

	werase(inputWnd);
	wattrset(inputWnd, COLOR_PAIR(2));
	mvwprintw(inputWnd, 0, 0, "%s", responseBuffer);
	wattrset(inputWnd, COLOR_PAIR(1));
	mvwprintw(inputWnd, 1, 0, "slibo: %s", buf);
	printMoveList();
	wrefresh(moveWnd);
	wrefresh(lineWnd);
	wrefresh(boardWnd);
	wrefresh(statWnd);
	wrefresh(inputWnd);
}
#endif

//gets called whenever the search is finished.
//in normal mode it actually does output a response move,
//in other modes it may do something different.
static void respond() {
	switch (mode) {
	case Normal:
		if (computerTurn) {
			if (statsx.bestMove.src | statsx.bestMove.dst) {
				output("move %s\n", moveToStr(statsx.bestMove));
				board.doMove(statsx.bestMove);
				setTC();
				board.copy();
				if (pondermode && ponderMove.src | ponderMove.dst) {
					board.doMove(ponderMove);
					ponderMoveOld = ponderMove;
					pondering = true;
					t0 = t1 = 0;
					board.start();
				}
				computerTurn = false;
			} else {
				output("game has ended.");
			}
		}
	case Force:
	case Analyze:
		break;
	case Autoplay:
		if (statsx.bestMove.src | statsx.bestMove.dst) {
			output("move %s\n", moveToStr(statsx.bestMove));
			setTC();
			board.copy();
		} else {
			output("game has ended.");
		}
	case Bench:
		break;
	}
}

#ifdef HAVE_CURSES
//looks if stdin is connected to a pipe.
//in this case we assume we are started with a chess board program
//and activate "xboard mode" and skip all the curses stuff.
void initNcurses() {
	if (flags.xboard == FLAGS_XBOARD_UNDEFINED) {
		struct stat st;
		fstat(0, &st);
		if (S_ISFIFO(st.st_mode)|| S_ISSOCK(st.st_mode) || !getenv("TERM")) {
			debug("%d %d\n", S_ISFIFO(st.st_mode), S_ISSOCK(st.st_mode));
			debug("Connected to a pipe, not starting interactive mode\n");
			flags.xboard = true;
		} else {
			debug("ncurses\n");
			flags.xboard = false;
		}
	}

	if (flags.xboard) {
		signal(SIGINT, SIG_IGN);
		//        signal(SIGTERM, SIG_IGN);
				setbuf(stdout, NULL);
				setbuf(stdin, NULL);
			} else {
				initscr();
				start_color();
				init_pair(1, COLOR_WHITE, COLOR_BLACK);
				init_pair(2, COLOR_RED, COLOR_WHITE);
				init_pair(3, COLOR_BLACK, COLOR_RED);
				init_pair(4, COLOR_BLACK, COLOR_YELLOW);
				init_pair(5, COLOR_WHITE, COLOR_RED);
				init_pair(6, COLOR_WHITE, COLOR_YELLOW);
				bkgd(COLOR_PAIR(1) | ' ');

				lineWnd = newwin(LINES - 31, COLS - 20, 29, 0);
				wbkgd(lineWnd, COLOR_PAIR(1) | ' ');
				scrollok(lineWnd, true);

				statWnd = newwin(28, 59, 0, 0);
				wbkgd(statWnd, COLOR_PAIR(1) | ' ');

				inputWnd = newwin(2, COLS, LINES - 2, 0);
				wbkgd(inputWnd, COLOR_PAIR(1) | ' ');

				boardWnd = newwin(16, 16, 0, COLS - 16);
				wbkgd(boardWnd, COLOR_PAIR(1) | ' ');

				moveWnd = newwin(LINES - 18, 20, 18, COLS - 20);
				wbkgd(moveWnd, COLOR_PAIR(1) | ' ');

				nodeWnd = newwin(18, COLS - 60 - 16, 0, 60);
				wbkgd(nodeWnd, COLOR_PAIR(1) | ' ');

				noecho();
				keypad(stdscr, TRUE);
				halfdelay(1);
				flags.isNcursesInitialized = true;
			}
		}
#endif

static void initGlobals() {
	char buf[256];
#ifdef MYDEBUG

	flags.minPos = 0;
#else

	flags.minPos = 100000;
#endif
	flags.plyNull = 1;
	flags.resign = true;
	flags.post = true;
	flags.isNcursesInitialized = false;

	cpuclock = 1000;
	FILE *f = fopen("/proc/cpuinfo", "r");
	if (f)
		while (fgets(buf, 255, f)) {
			if (sscanf(buf, "cpu MHz         : %d", &cpuclock))
				break;
		}
	//	invcpuclk = 0xffffffffffffffff/cpuclock;
	fclose(f);

	strcpy(permInfoFileName, "/var/lib/slibo/perminfo.bin");
	strcpy(permHashFileName, "/var/lib/slibo/permhash.bin");
	strcpy(permTempFileName, "/tmp/permtemp.bin");

#ifdef __unix__
	wordexp_t result;
	wordexp("~/.sliboenginerc", &result, 0);

	/* Expand the string for the program to run.  */
	if ((f = fopen(result.we_wordv[0], "r")) || (f = fopen("/etc/sliboenginerc", "r")))
		while (fgets(buf, 255, f)) {
			sscanf(buf, "permInfoFileName %s", permInfoFileName);
			sscanf(buf, "permHashFileName %s", permHashFileName);
			sscanf(buf, "permTempFileName %s", permTempFileName);
			sscanf(buf, "mainHashSize %zd", &mainHash.size);
			sscanf(buf, "pawnHashSize %zd", &pawnHash.size);
			// sscanf(buf, "pieceHashSize %zd", &whiteHash.size);
		}
	wordfree(&result);
#endif
	// blackHash.size = whiteHash.size;
}

static void processIn(char c) {
	static char buf[256];
	static char *pbuf = buf;
	if (pbuf - buf >= (signed) sizeof(buf) - 3) {
		*pbuf++ = c;
		c = '\n';
	}
	if (c == '\n') {
		*responseBuffer = 0;
		parse(split<string> (buf));
		pbuf = buf;
	} else
		*pbuf++ = c;
	*pbuf = 0;
}

static void processOut(char /*c*/) {
}

int main(int argc, char *argv[]) {
#ifdef MYDEBUG
	debuglog = fopen("debug.log", "a");
#ifdef __unix__
	setlinebuf(debuglog);
#endif
#endif

	initGlobals();
	initTables();
	initExtensions();
	initHash();
	board.startPostion();
	board.initThread();
#ifdef SMP
	SearchBoard::initBoardPool();
#endif

#ifdef HAVE_QT
	QApplication app(argc, argv);
	mainWindow = new MainWindow(processIn, processOut);
	QString strtot;
	for (int i = 1; i < argc; i++) {
		QString str(argv[i]);
		if (str[0] == '-')
			continue;
		strtot += str + " ";
	}
	mainWindow->lineEdit->setText(strtot);
	mainWindow->show();
	return app.exec();
#endif

	vector<string> args;
	for (int i = 1; i < argc; i++)
		args.push_back(argv[i]);
	flags.xboard = FLAGS_XBOARD_UNDEFINED;
	parse(args);
#ifdef HAVE_CURSES
	initNcurses();
#endif

	flags.log = false;
	debug("Entering main loop\n");
	for (;;) {
#ifdef HAVE_CURSES
		if (flags.xboard)
			readKeyX();
		else
			readKey();
#else
		readKeyX();
#endif
		if (board.isResultAvailable.test()) {
			respond();
			board.isResultProcessed.set();
		}
	}
}

