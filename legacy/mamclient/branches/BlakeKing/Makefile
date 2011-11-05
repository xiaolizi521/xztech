CC=g++
CFLAGS=-Wall
OBJECTS=Counter.o Crypto.o Socket.o Utilities.o ConfigFile.o Dijkstra.o MapFile.o \
IPacket.o CharInfoPacket.o MessagePacket.o JumpPacket.o ActionPacket.o \
MapInfoPacket.o WalkPacket.o NpcInfoPacket.o NpcUpdatePacket.o NpcDialoguePacket.o \
Player.o Npc.o Dialogue.o GameMap.o User.o main.o
LIBS=-lconfuse -lpthread

MAMClient: $(OBJECTS)
	@echo Compiling MAMClient
	@$(CC) $(CFLAGS) $(LIBS) $(OBJECTS) -o MAMClient

#UTILS & MISC

Counter.o: Counter.h Counter.cpp
	@echo Compiling Counter
	@$(CC) $(CFLAGS) -c Counter.cpp

Crypto.o: Crypto.h Crypto.cpp
	@echo Compiling Crypto
	@$(CC) $(CFLAGS) -c Crypto.cpp

Socket.o: Socket.h Socket.cpp
	@echo Compiling Socket
	@$(CC) $(CFLAGS) -c Socket.cpp

Utilities.o: Utilities.h Utilities.cpp
	@echo Compiling Utilities
	@$(CC) $(CFLAGS) -c Utilities.cpp
	
ConfigFile.o: ConfigFile.h ConfigFile.cpp
	@echo Compiling ConfigFile
	@$(CC) $(CFLAGS) -c ConfigFile.cpp
	
Dijkstra.o: Dijkstra.h Dijkstra.cpp
	@echo Compiling Dijkstra
	@$(CC) $(CFLAGS) -c Dijkstra.cpp
	
MapFile.o: MapFile.h MapFile.cpp
	@echo Compiling MapFile
	@$(CC) $(CFLAGS) -c MapFile.cpp

#PACKETS

IPacket.o: IPacket.h IPacket.cpp
	@echo Compiling IPacket
	@$(CC) $(CFLAGS) -c IPacket.cpp

CharInfoPacket.o: CharInfoPacket.h CharInfoPacket.cpp
	@echo Compiling CharInfoPacket
	@$(CC) $(CFLAGS) -c CharInfoPacket.cpp

MessagePacket.o: MessagePacket.h MessagePacket.cpp
	@echo Compiling MessagePacket
	@$(CC) $(CFLAGS) -c MessagePacket.cpp

JumpPacket.o: JumpPacket.h JumpPacket.cpp
	@echo Compiling JumpPacket
	@$(CC) $(CFLAGS) -c JumpPacket.cpp
    
ActionPacket.o: ActionPacket.h ActionPacket.cpp
	@echo Compiling ActionPacket
	@$(CC) $(CFLAGS) -c ActionPacket.cpp

MapInfoPacket.o: MapInfoPacket.h MapInfoPacket.cpp
	@echo Compiling MapInfoPacket
	@$(CC) $(CFLAGS) -c MapInfoPacket.cpp
    
WalkPacket.o: WalkPacket.h WalkPacket.cpp
	@echo Compiling WalkPacket
	@$(CC) $(CFLAGS) -c WalkPacket.cpp

NpcInfoPacket.o: NpcInfoPacket.h NpcInfoPacket.cpp
	@echo Compiling NpcInfoPacket
	@$(CC) $(CFLAGS) -c NpcInfoPacket.cpp
	
NpcUpdatePacket.o: NpcUpdatePacket.h NpcUpdatePacket.cpp
	@echo Compiling NpcUpdatePacket
	@$(CC) $(CFLAGS) -c NpcUpdatePacket.cpp
	
NpcDialoguePacket.o: NpcDialoguePacket.h NpcDialoguePacket.cpp
	@echo Compiling NpcDialoguePacket
	@$(CC) $(CFLAGS) -c NpcDialoguePacket.cpp

#GAME OBJECTS

Player.o: Player.h Player.cpp
	@echo Compiling Player
	@$(CC) $(CFLAGS) -c Player.cpp

Npc.o: Npc.h Npc.cpp
	@echo Compiling Npc
	@$(CC) $(CFLAGS) -c Npc.cpp
	
Dialogue.o: Dialogue.h Dialogue.cpp
	@echo Compiling Dialogue
	@$(CC) $(CFLAGS) -c Dialogue.cpp
	
GameMap.o: GameMap.h GameMap.cpp
	@echo Compiling GameMap
	@$(CC) $(CFLAGS) -c GameMap.cpp

User.o: User.h User.cpp
	@echo Compiling User
	@$(CC) $(CFLAGS) -c User.cpp

#MAIN

main.o: Socket.h Crypto.h define.h Utilities.h Player.h packets.h GameMap.h User.h Dialogue.h MapFile.h main.cpp
	@echo Compiling main
	@$(CC) $(CFLAGS) -c main.cpp

install:
	@echo Installing MAMClient to /usr/bin
	@mv -f MAMClient /usr/bin/
	@mkdir -p /etc/MAMClient
	@cp -r ini /etc/MAMClient/
	@cp -r map /etc/MAMClient/
	
uninstall:
	@echo Uninstalling MAMClient from /usr/bin
	@rm -f /usr/bin/MAMClient
	@rm -rf /etc/MAMClient

clean:
	rm -f *.o
	rm -f MAMClient