/*
 *  Player.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 2/18/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "Player.h"

Player::Player( void )
{
}

Player::Player( CPacket packet )
{
    BYTE nLength    = *( BYTE * )( packet.data + 0x3C );
    //BYTE mLength  = *( BYTE * )( packet.data + 0x3D + nLength );
    //BYTE sLength  = *( BYTE * )( packet.data + 0x3F + nLength + mLength );
    //BYTE gLength  = *( BYTE * )( packet.data + 0x40 + nLength + mLength + sLength );
    //BYTE pLength  = *( BYTE * )( packet.data + 0x42 + nLength + mLength + sLength + gLength );
    
    this->m_id      = *( int * )packet.data;
    this->m_x       = *( short * )( packet.data + 0x04 );
    this->m_y       = *( short * )( packet.data + 0x06 );
    this->m_level   = *( short * )( packet.data + 0x10 );
    this->m_reborn  = *( BYTE * )( packet.data + 0x0E );
    
    if ( *( int * )( packet.data + 0x14 ) >= 1 ) 
        this->m_pkEnable = true;
    else
        this->m_pkEnable = false;

    memcpy( ( void * )this->m_name, ( void * )( packet.data + 0x3D ), nLength );
    //memcpy( ( void * )this->m_nickname, ( void * )( packet.data + 0x3D + nLength ), mLength + 1 );
    //memcpy( ( void * )this->m_spouse, ( void * )( packet.data + 0x3F + nLength + mLength ), sLength + 1 );
    //memcpy( ( void * )this->m_guild, ( void * )( packet.data + 0x40 + nLength + mLength + sLength + 2 ), gLength );
    //memcpy( ( void * )this->m_position, ( void * )( packet.data + 0x43 + nLength + mLength + sLength + gLength ), pLength );
    
    this->m_name[nLength]           = '\0';
	//this->m_nickname[mLength+1]	= '\0';
	//this->m_spouse[sLength+1]     = '\0';
	//this->m_guild[gLength]        = '\0';
	//this->m_position[pLength]     = '\0';
    
    for ( int i = 0; i < (int)strlen( this->m_name ); i++ )
        ( this->m_name[i] == '\n' ) ? this->m_name[i] = '.' : NULL ;
}

Player::~Player( void )
{
}

void Player::Display( void )
{
    printf( "Name: %s\n", this->m_name );
    printf( "Nickname: %s\n", this->m_nickname );
    printf( "Spouse: %s\n", this->m_spouse );
    printf( "Guild: %s\n", this->m_guild );
    printf( "Position: %s\n", this->m_position );
    printf( "Level: %d\n", this->m_level );
    printf( "Reborns: %d\n", this->m_reborn );
    
    if ( this->m_pkEnable )
        printf( "In Circle: True\n" );
    else
        printf( "In Circle: False\n" );
	
    printf( "X: %d Y: %d\n", this->m_x, this->m_y );
    printf( "Id: %d\n", this->m_id );
}