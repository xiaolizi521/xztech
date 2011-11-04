#include <iostream>
#include <map>
#include <vector>
#include <stdio.h>
#include <errno.h>
#include <time.h>
#include <stdlib.h>
#include "Socket.h"
#include "Crypto.h"
#include "define.h"
#include "Utilities.h"
#include "Player.h"
#include "packets.h"
#include "GameMap.h"
#include "User.h"
#include "Dialogue.h"
#include "MapFile.h"
#include <pthread.h>
#include <mysql++/mysql++.h>

int account_id;
mysqlpp::Connection connection( false );

typedef struct _GAMEOBJ {
    Crypto      *crypto;
    Socket      *a_socket;
    Socket      *g_socket;
    GameMap     *gameMap;
    Dialogue    *dialogue;
    MapFile     *mapFile;
    User        *user;
}GAMEOBJ;

char db_pass[32];

void process_livingblock( GAMEOBJ gObj, int id )
{
    // Establish a mysql conection
    
    if ( !connection.connect( DBNAME, DBHOST, DBUSER, db_pass ) )
        return;
    
    mysqlpp::Query query = connection.query();
    
    //open the npc
    gObj.user->open_npc( id );
    
    while ( !gObj.dialogue->m_open )
        sleep( .1 );
    
    int nOptions = gObj.dialogue->m_nOptions;

    for ( int i = 1; i < nOptions; i++ )
    {
    	int mapid = gObj.gameMap->m_id;
    	
    	printf( "Processing: %d\n", mapid );
    	
        // enter living block section
        gObj.user->click_dialogue( i );
        
        sleep( 2 );
        
        printf( "Getting a list of NPC's...\n" );
        
        // get a list of houses
        NpcList npclist = gObj.gameMap->npc_list();
        NpcList::iterator it;
        
        printf( "Done\nDeleting old records of this map...\n" );
        
        query << "DELETE FROM `Houses` WHERE `map_id`=" << mapid;
        query << " AND `block`=" << i;
        
        query.execute();
        
        printf( "Done\nBeginning to iterate through the houses...\n" );
        
        for ( it = npclist.begin(); it != npclist.end(); it++ )
        {
            printf( "name: %s; type: %d; look: %d\n", it->second.m_name,
                   it->second.m_type, it->second.m_look );
            
            query << "insert into `Houses` (`map_id`, `block`, `owner`, `type`, `rent`)";
            query << " VALUES( " << mapid << ", " << i << ", '";
            query << it->second.m_name << "', " << it->second.m_look;
            query << ", 0)";
            
            query.execute();
        }
        
        //leave the map
        gObj.user->jump( 74, 122 );
        
        sleep( 5 );
        
        // reopen the npc
        gObj.user->open_npc( id );
        
        while ( !gObj.dialogue->m_open )
            sleep( .1 );
    }
}

void *worker_thread( void *ptr )
{
    GAMEOBJ gObj = *( GAMEOBJ * )ptr;
    
    std::ifstream 	file;
    std::ifstream	file2;
    std::ofstream	last_map_file;
    std::string 	line;
    std::string		last_map;
    std::string 	filename = GAMEFILESPATH;
    
    bool			go = false;
    
    filename += "guildmaps.txt";
    
    char *home = getenv( "HOME" );
    char path[255];
    
    strcpy( path, home );
    strcat( path, "/.last_map" );
    
    file2.open( path );
    
    getline( file2, last_map );
    
    file2.close();
    
    last_map_file.open( path );
   	
    file.open( filename.c_str() );
    
    if ( !strcmp( last_map.c_str(), "" ) )
    	go = true;
    
    if ( file.is_open() )
    {
    	while ( !file.eof() )
    	{
    		getline( file, line );
    		
    		if ( !go )
    		{
    			if ( !strcmp( line.c_str(), last_map.c_str() ) )
    				go = true;
    		}
    		
    		if ( go )
    		{
				printf( "Moving to: %d\n", atoi( line.c_str() ) );
				
				gObj.user->move_map( atoi( line.c_str() ) );

				sleep( 1.5 );

				NpcList npc_list = gObj.gameMap->npc_list();
				NpcList::iterator it;

				for ( it = npc_list.begin(); it != npc_list.end(); it++ )
				{
					if ( !strcmp( it->second.m_name, "LivingBlock" ) )
					{
						printf( "Found a living block in: %s\n", gObj.gameMap->m_name );
						printf( "Attempting to process it...\n" );
				
						process_livingblock( gObj, it->first );
				
						break;
					}
				}
				
				last_map_file << line;
			}
    	}
    }
    
    last_map_file.close();
    file.close();
    
    return 0;
}

Socket login( Socket aSocket, Crypto *crypto, AccountInfo acct_info )
{
    Socket  g_socket( crypto );    
    CPacket loginPacket = { 
        { 52, 1051 },
        {
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
            0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00
        }
    };
    CPacket packet;
    char    g_address[16];
    
    strncpy( loginPacket.data, acct_info.username, 16 );
    encrypt_password( &loginPacket.data[16], acct_info.password );
    strncpy( ( loginPacket.data + 32 ), acct_info.servername, 16 );
	
    /* Send the login Packet */
    aSocket.send_packet( loginPacket );
	
    /* Read the incoming packet's header */
    packet = aSocket.read_packet();
	
    account_id = *( int * )packet.data;

    /* Copy the game server's IP into the ipBuffer */
    strcpy( g_address, ( const char * )( packet.data + 0x08 ) );
    
    /* Generate keys three and four */
    crypto->generate_keys( packet );
	
    /* Connect to the game server */
    if ( !g_socket.connect( g_address, GAMESERVERPORT ) )
    {
        std::cout << "Error connecting to game server..." << std::endl;
        
        return g_socket;
    }
	
    /* Clear the address from the packet we recieved from the account server */
    memset( ( void * )( packet.data + 0x08 ), 0, ( ( packet.header.size - sizeof( CPacketHeader ) ) - 8 ) );
	
    /* Copy the blacknull string into the packet */
    strcpy( ( packet.data + 0x08 ), "blacknull" );
	
    /* Reset the counters */
    crypto->reset_counters();
	
    /* Send the connecting packet to the game server */
    g_socket.send_packet( packet );
    
    /* Use new keys */
    crypto->use_new_keys();
    
    printf( "Connected...\n");
	
    /* Return the game server socket */
    return g_socket;
}

int main ( int argc, char * const argv[] ) 
{
    Crypto  *crypto         = new Crypto();
    bool    loggedIn        = false;
    time_t  last_ping       = time( NULL );
    
    Socket  a_socket( crypto );
    Socket  g_socket( crypto );
    
    GameMap     *gameMap        = new GameMap( &g_socket );
    Dialogue    *dialogue       = new Dialogue();
    MapFile     *mapFile        = new MapFile();
    User        *user           = new User( &g_socket, gameMap, dialogue, mapFile );
    GAMEOBJ     gObj;
    
    gObj.crypto        = crypto;
    gObj.a_socket      = &a_socket;
    gObj.g_socket      = &g_socket;
    gObj.gameMap       = gameMap;
    gObj.dialogue      = dialogue;
    gObj.mapFile       = mapFile;
    gObj.user          = user;
    
    AccountInfo                     acct_info;
    std::vector< PET_PACKET >       PetList;
    
    char path[255];
    strcpy( path, GAMEFILESPATH );
    strcat( path, "ini/map.ini" );
    
    mapFile->process( path );
    
    initTime();
    srand( time( NULL ) );
    
    if ( argc != 2 )
        acct_info = account_info( "Whitey" );
    else
        acct_info = account_info( argv[1] );
        
    strcpy( db_pass, argv[2] );
    
    if ( !strcmp( acct_info.username, "NOTFOUND" ) || 
        !strcmp( acct_info.username, "" ) || 
        !strcmp( acct_info.password, "" ) ||
        !strcmp( acct_info.serverip, "" ) ||
        !strcmp( acct_info.servername, "" ) )
    {
        std::cout   << "Error: .account_info.conf must include a username and password, "
                    << "and optionally a server and protection code." << std::endl;
    }
    
    if ( !a_socket.connect( acct_info.serverip, ACCOUNTPORT ) )
        return 0;
    
    g_socket = login( a_socket, crypto, acct_info );
	
    while ( true )
    {
        g_socket.select();
		
        if ( g_socket.is_readable() )
        {
            CPacket packet;
			
            packet = g_socket.read_packet();
			
            if ( packet.header.size == 0 && packet.header.id == 0 )
            {
                std::cout << "Error while reading packet... time: " << timeGetTime() << std::endl;
                return 0;
            }
            
            if ( difftime( last_ping, time( NULL ) ) >  300 )
                return 0;
            
            switch ( packet.header.id )
            {
                case 1002:
                {
                    memset( ( void * )( packet.data + 0x04 ), 0, packet.header.size - sizeof( CPacketHeader ) );
                    packet.data[8] = 0xC5;
					
                    g_socket.send_packet( packet );
				}
                    
                    break;
					
                case 1004:
                {
                    MessagePacket *msg = new MessagePacket( packet );
                    
                    if ( msg->m_channel == CH_SYSTEM )
                        printf( "[%s]%s\n", msg->m_channelName, msg->m_message );
                    else if ( msg->m_channel == CH_VENDOR )
                        printf( "[%s](%s): %s\n", msg->m_channelName, 
                               msg->m_sender, msg->m_message );
                    else
                        printf( "[%s]%s %s speaks to %s: %s\n", msg->m_channelName, msg->m_sender, msg->m_emotion, 
                               msg->m_target, msg->m_message );
                    
                    char cmd[32];
                    char args[255];
                    
                    sscanf( msg->m_message, "@%s %s", cmd, args );
                    
                    if ( !strcmp( cmd, "jump" ) && !strcmp( msg->m_sender, OWNER ) )
                    {
                        int x, y;
                        
                        if ( sscanf( args, "%d,%d", &x, &y ) == 2 )
                            user->jump( x, y );
                    }
                    
                    if ( !strcmp( cmd, "start" ) && !strcmp( msg->m_sender, OWNER ) )
                    {
                        pthread_t thread;
                        
                        pthread_create( &thread, NULL, worker_thread, ( void * )&gObj );
                    }
                    
                    if ( msg->m_channel == CH_PRIVATE && !strcmp( msg->m_message, "@join" ) )
                    {
                        Player *sender = gameMap->find_player( msg->m_sender );
                        
                        if ( sender->m_id != -1 )
                        {
                            CPacket accpet_packet = {
                                { 32, 2052 },
                                {
                                    0x0F, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00,
                                    0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00
                                }
                            };
                            memcpy( ( void * )( accpet_packet.data + 0x04 ), ( void * )&sender->m_id, 4 );
                            
                            g_socket.send_packet( accpet_packet );
                        }
                    }
                    
                    delete msg;
                }
					
                    break;
					
                case 1005:
                {
                    WalkPacket  *walk   = new WalkPacket( packet );
                    Player      *player = gameMap->find_player( walk->m_playerID );
                    
                    if ( player->m_id == -1 )
                    {
                        delete player;
                        break;
                    }
                    
                    player->m_x = walk->m_destX;
                    player->m_y = walk->m_destY;

                    //printf( "%s has moved to [%d, %d]\n", PlayerList[id].m_name, x, y );
                    
                    delete walk;
                }
                    break;
				
                case 1006:
                {
                    ActionPacket    *action = new ActionPacket( packet );
                    Player          *player = gameMap->find_player( action->m_playerID );
                    
                    if ( player->m_id == -1 )
                    {
                        delete player;
                        break;
                    }
                    
                    break;
                    
                    switch ( action->m_actionID )
                    {
                        case 4:
                            printf( "* %s faints.\n", player->m_name );
                            break;
							
                        case 6:
                            printf( "* %s waves.\n", player->m_name );
                            break;
							
                        case 7:
                            printf( "* %s kneels.\n", player->m_name );
                            break;
							
                        case 8:
                            printf( "* %s is crying.\n", player->m_name );
                            break;
							
                        case 9:
                            printf( "* %s is angry.\n", player->m_name );
                            break;
							
                        case 10:
                            printf( "* %s sits.\n", player->m_name );
                            break;
							
                        case 12:
                            printf( "* %s is happy.\n", player->m_name );
                            break;
							
                        case 13:
                            printf( "* %s bows.\n", player->m_name );
                            break;
						
                        case 15:
                            printf( "* %s throws.\n", player->m_name );
                            break;
							
                        default:
                            printf( "* Unknown action preformed by %s [%d]\n", player->m_name, action->m_actionID );
                            break;
                    }
                    
                    delete action;
                }
					
                    break;
					
                case 1007:
                {
                    JumpPacket  *jump   = new JumpPacket( packet );
                    Player      *player = gameMap->find_player( jump->m_playerID );
                    
                    if ( player->m_id == -1 )
                    {
                        delete player;
                        break;
                    }
                    
                    if ( jump->m_mode == 2 )
                    {
                        player->m_x = jump->m_x;
                        player->m_y = jump->m_y;
                        
                        //printf( "%s has moved to [%d, %d]\n", PlayerList[id].m_name, x, y );
                    }
                    else if ( jump->m_mode == 8 )
                    {
                        //printf( "%s has left the map.\n", PlayerList[id].m_name );
						
                        gameMap->del_player( jump->m_playerID );
                    }
                    
                    delete jump;
                }
					
                    break;
					
                case 1008:
                {
                    CharInfoPacket *info = new CharInfoPacket( packet );
                    
                    user->process( info );
                    user->m_accountID = account_id;
                    
                    delete info;
                }
                    
                    break;
					
                case 1020:
                {
                    int response = timeGetTime() ^ ( ( user->m_characterID * user->m_characterID ) + 0x2537 );
					
                    memcpy( ( void * )( packet.data + 0x04 ), ( void * )&response, sizeof( response ) );
					
                    g_socket.send_packet( packet );
                }
					
                    break;
                
                case 1031:
                {
                    MapInfoPacket *info = new MapInfoPacket( packet );
                    gameMap->process( info );
                    
                    dialogue->m_open = false;
                    
                    user->m_x = info->m_x;
                    user->m_y = info->m_y;
                    
                    printf( "Entered: %s\n", gameMap->m_name );
					
                    if ( !loggedIn )
                    {
                        user->jump( 0, 0, 19, 0 );
                        
                        if ( strcmp( acct_info.pcode, "" ) )
                        {
                            char unlock[255];
                            
                            strcpy( unlock, "/unlock " );
                            strcat( unlock, acct_info.pcode );
                            
                            MessagePacket *umsg = new MessagePacket( user->m_name, user->m_name, unlock, CH_PRIVATE );
                            g_socket.send_packet( umsg->pack() );
                            delete umsg;
                        }
                    }
                    
                    delete info;
                }
					
                    break;
					
                case 1032:
                {
                    Player *player = new Player( packet );
                    
                    gameMap->add_player( player );
                    
                    //printf( "%s has entered the map (%d, %d)\n", player.m_name, player.m_x, player.m_y );
                    
                    delete player;
                }
					
                    break;
                    
                case 1033:
                {
                    /*int pet_id = *( int * )packet.data;
                    std::vector< PET_PACKET >::iterator it;
                    
                    for ( it = PetList.begin(); it != PetList.end(); it++ )
                        if ( it->id == pet_id ) 
                            printf( "Marching Pet: %s\n\n", it->name );*/
                }

                    break;
                    
                case 1034:
                {
                    PET_PACKET pp;
                
                    memcpy( ( void * )&pp, ( void * )packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );

                    PetList.push_back( pp );
                }
                    
                    break;
                    
                case 1038:
                {
                    last_ping = time( 0 );
                    
                    if ( !loggedIn )
                        loggedIn = true;
                }
                    
                    break;
                    
                case 1043: 
                {
                    // Has more data that needs to be reversed
                    /* Don't want to write a class for this quite yet, because
                     * I want to see if this packet is used when a player changes
                     * their actual body in the reborn room, or if it's just
                     * for disguising as a pet.
                     */
                    int     id      = *( int * )packet.data;
                    Player  *player = gameMap->find_player( id );
                    
                    if ( player->m_id == -1 )
                    {
                        delete player;
                        break;
                    }
                    
                    //printf( "* %s has disguised as a pet.\n", player->m_name );
                }
                    break;
					
                case 1050:
                    /* Why don't i have anything for this packet? It's the first
                     * Received packet from the game server...
                     */
                    break;
                    
                case 2030:
                {
                    NpcInfoPacket   *info   = new NpcInfoPacket( packet );
                    Npc             *npc    = new Npc( info );
                    
                    gameMap->add_npc( npc );
                    
                    //printf( "* Added NPC: %s (id=%d)(type=%d)\n", npc->m_name, npc->m_id, npc->m_type );
                    
                    delete info;
                    delete npc;
                }
                    
                    break;
                    
                case 2031:
                {
                    NpcUpdatePacket *update = new NpcUpdatePacket( packet );
                    
                    if ( update->m_action == 3 )
                        gameMap->del_npc( update->m_npcID );
                    
                    delete update;
                }
                    
                    break;
                    
                case 2033:
                {
                    NpcDialoguePacket *dialog = new NpcDialoguePacket( packet );
                    dialogue->process( dialog );
                    
                    delete dialog;
                }
                    
                    break;
					
                default:
                    /*printf( "ID: %d Length: %d Data:\n", packet.header.id, packet.header.size );
                    hexdump( ( void * )packet.data, ( packet.header.size - sizeof( CPacketHeader ) ) );
                    printf( "\n");*/
                    break;
            }
        }
    }
    
    return 1;
}
