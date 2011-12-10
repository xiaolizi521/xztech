/*
 *  Utilities.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 10/9/08.
 *  Copyright 2008 __MyCompanyName__. All rights reserved.
 *
 */

#include "Utilities.h"

unsigned char rc5_cipher[] = {
    0xBC, 0x54, 0xE8, 0xEB, 0xF7, 0x98, 0x49, 0xB0, 0x8C, 0xA8, 0xFA, 0xFF, 0xBB, 0x54, 0xE8, 0x96,
    0x56, 0x55, 0x91, 0xA9, 0x10, 0x41, 0xE4, 0x48, 0x8F, 0x30, 0x32, 0x9F, 0x3E, 0x1D, 0xF4, 0x27,
    0x23, 0x35, 0x4F, 0xCF, 0xB4, 0xC6, 0xC3, 0xEA, 0x03, 0x5E, 0xEA, 0xE9, 0xBA, 0x4B, 0x97, 0xE5,
    0x92, 0x76, 0x4D, 0x33, 0x2E, 0xCF, 0x6B, 0x2C, 0x74, 0x3B, 0xC5, 0x0D, 0xA6, 0x92, 0x5C, 0x99,
    0x77, 0x6D, 0x4F, 0x7E, 0x9F, 0xB7, 0xB2, 0x1E, 0x89, 0x8D, 0x34, 0x1D, 0x54, 0x13, 0x64, 0xED,
    0x9D, 0x4A, 0xE0, 0x15, 0x59, 0xA1, 0x8D, 0x48, 0xD3, 0x17, 0x78, 0x64, 0x20, 0xBC, 0xA0, 0x8C,
    0xFE, 0xF7, 0x64, 0x92, 0x6C, 0x8C, 0xE7, 0x91, 0xFB, 0x07, 0x9A, 0x5C, 0xCE, 0xDC, 0xD4, 0xAB,
    0x8D, 0xF9, 0x16, 0x64, 0x5B, 0xAB, 0x42, 0x66
};

unsigned long long server_start_time = 0;

unsigned long timeGetTime( void )
{
    timeval tv;
	
    gettimeofday( &tv, NULL );
	
    unsigned long time = tv.tv_sec;// * ( unsigned long long )TICKSPERSEC + TICKS_1601_to_1970;	
    //time += tv.tv_usec * 10;
	
    return ( time - server_start_time );// / 10000;
}

void initTime( void )
{
    server_start_time = timeGetTime();
}

uint64_t _rotr( uint64_t a1, uint64_t a2 )
{
    return (a1 << a2 % 0x20u) | ((unsigned int)a1 >> (char)(32 - a2 % 0x20u));
}

// _DWORD now defined as uint64_t

int _encrypt_password( unsigned char * a1, unsigned char * a2, signed int a3 )
{
    uint64_t  v10; // [sp+10h] [bp-8h]@3
    uint64_t         result; // eax@1
    uint64_t         v4; // ebx@3
    uint64_t         v5; // edi@3
    uint64_t         v6; // esi@3
    uint64_t         v7; // eax@4
    uint64_t         v8; // [sp+14h] [bp-4h]@2
    uint64_t         v9; // [sp+Ch] [bp-Ch]@2
	
    result = 8 * a3 / 8;
    a3 = 8 * a3 / 8;
    if ( result > 0 )
    {
        result = (uint64_t) a2; //140734799764088
        v8 = 0;
        v9 = (uint64_t) a2; //140734799764088
        printf("v9 as *(_DWORD *) is %llu:\n", * (_DWORD *)v9); // 7310868740288963962
        printf("( v9 + 4 ) as *(_DWORD *) is %llu:\n", * (_DWORD *)(v9 + 4)); // 8188951726778575970
        printf("( a1 + 16 ) as *(_DWORD *) is %llu:\n", * (_DWORD *)((uint64_t) a1 + 16)); // 5252394605240997206
        printf("( a1 + 20 ) as *(_DWORD *) is %llu:\n", * (_DWORD *)((uint64_t) a1 + 20)); // 11471284592827449616
        // (greasy) 12563263345529961168 
        // (greasy) 1010111001011001101011010111001100100001111111111011101011010000 
        printf("v4 is %llu: \n", (*(_DWORD *)v9 + *(_DWORD *)(a1 + 16)));
        printf("v6 is %llu: \n", (*(_DWORD *)(v9 + 4) + *(_DWORD *)((uint64_t) a1 + 20)));
        // 1213492245896473970
        // 1000011010111001100001100010010101110010110011010110101110010
        
        while ( a3 / 8 > v8 ) // a3/8 == 2, v8 == 0 at beginning... therefore this runs twice.
        {
            v4 = *(_DWORD *)v9 + *(_DWORD *)((uint64_t) a1 + 16); // 7310868740288963962 + 5252394605240997206
            // 12563263345529961168 
            // 1010111001011001101011010111001100100001111111111011101011010000
            v6 = *(_DWORD *)(v9 + 4) + *(_DWORD *)((uint64_t) a1 + 20); // 7310868740288963966 + 5252394605240997206
            // 19660236319606025586
            // 10001000011010111001100001100010010101110010110011010110101110010
            
            v10 = 1;
            v5 = (uint64_t) a1 + 24;
            
            // runs 12 times
            do
            {
                v7 = *(_DWORD *)v5 + _rotr(v6 ^ v4, v6);
                v4 = v7;
                v6 = *(_DWORD *)(v5 + 4) + _rotr(v7 ^ v6, v7);
                printf("v4 at end of mid-do is %llu \n", v4);
                
                ++v10;
                v5 += 8;
            }
            while ( v10 <= 12 );
            result = v9;
            *(_DWORD *)v9 = v4;
            *(_DWORD *)((unsigned char *) v9 + 4) = v6;
            ++v8;
            v9 += 8;
            
            printf("(End of while...) v4 is %llu: \n", v4);
            printf("(End of while...) v6 is %llu: \n", v6);

        }
    }
    return result;
}

void encrypt_password( char *outbuffer, char *password )
{
    unsigned char buffer[] = {
        0x00, 0x5D, 0x1E, 0xEE, 0xFF, 0xFF, 0xFF, 0xFF, 0xE4, 0x55, 0xA5, 0x71, 0x00, 0x00, 0x00, 0x00
    };
	
    memcpy( ( void * )buffer, ( void * )password, ( strlen( password ) + 1 ) );
	
    _encrypt_password( rc5_cipher, buffer, 16 );
	
    memcpy( ( void * )outbuffer, ( void * )buffer, 16 );
}

void hexdump( void *ptr, int length )
{
    unsigned char *buffer = ( unsigned char * )ptr;
    
    for ( int i = 0; i < length; i += 16 )
    {
        printf( "%06X: ", i );
        
        for ( int n = 0; n < 16; n++ )
            if ( ( i + n ) < length )
                printf( "%02X ", buffer[i+n] );
            else
                printf( "   " );
        
        printf( " " );
        
        for ( int n = 0; n < 16; n++ )
            if ( ( i + n ) < length )
                printf( "%c", isprint( buffer[i+n] ) ? buffer[i+n] : '.' );
        
        printf( "\n" );
    }
}

AccountInfo account_info( const char *name )
{
    AccountInfo accountInfo;
    cfg_opt_t character_opts[] = {
        CFG_STR( "serverip",    "64.151.106.220",   CFGF_NONE ),
        CFG_STR( "servername",  "MythOfOrient",     CFGF_NONE ),
        CFG_STR( "username",    "",                 CFGF_NONE ),
        CFG_STR( "password",    "",                 CFGF_NONE ),
        CFG_STR( "pcode",       "",                 CFGF_NONE ),
        CFG_END()
    };
    cfg_opt_t opts[] = {
        CFG_SEC( "character", character_opts, CFGF_TITLE | CFGF_MULTI ),
        CFG_END()
    };
    cfg_t *cfg, *cfg_character;
    
    cfg = cfg_init( opts, CFGF_NONE );
    
    char *path = getenv( "HOME" );
    char filename[255];
    sprintf( filename, "%s/%s", path, ".account_info.conf" );
    
    if ( cfg_parse( cfg, filename ) != CFG_PARSE_ERROR )
    {
        for ( int i = 0; i < cfg_size( cfg, "character" ); i++ )
        {
            cfg_character = cfg_getnsec( cfg, "character", i );
            
            if ( !strcmp( cfg_title( cfg_character ), name ) )
            {
                strcpy( accountInfo.serverip, cfg_getstr( cfg_character, "serverip" ) );
                strcpy( accountInfo.servername, cfg_getstr( cfg_character, "servername" ) );
                strcpy( accountInfo.username, cfg_getstr( cfg_character, "username" ) );
                strcpy( accountInfo.password, cfg_getstr( cfg_character, "password" ) );
                strcpy( accountInfo.pcode, cfg_getstr( cfg_character, "pcode" ) );
                
                cfg_free( cfg );
                
                return accountInfo;
            }
        }
    }
    
    strcpy( accountInfo.username, "NOTFOUND" );
    
    cfg_free( cfg );
    
    return accountInfo;
}