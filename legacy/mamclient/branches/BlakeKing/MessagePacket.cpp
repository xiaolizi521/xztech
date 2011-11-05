/*
 *  MessagePacket.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/3/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "MessagePacket.h"

MessagePacket::MessagePacket( CPacket packet )
{
    MESSAGE_PACKET_HEADER header;
    
    BYTE sLength = *( BYTE * )( packet.data + 0x1D );
    BYTE tLength = *( BYTE * )( packet.data + 0x1E + sLength );
    BYTE mLength = *( BYTE * )( packet.data + 0x1F + sLength + tLength );
    
    memcpy( ( void * )&header, ( void * )packet.data, sizeof( MESSAGE_PACKET_HEADER ) );
    
    this->m_color   = header.color;
    this->m_channel = header.channel;
    this->m_effect  = header.effect;
    this->m_time    = header.time;
    
    strcpy( this->m_emotion, header.emotion );
    
    memcpy( ( void * )this->m_sender, ( void * )( packet.data + 0x1E ), sLength );
    memcpy( ( void * )this->m_target, ( void * )( packet.data + 0x1F + sLength ), tLength );
    memcpy( ( void * )this->m_message, ( void * )( packet.data + 0x20 + sLength + tLength ), mLength );
    
    this->m_sender[sLength]     = '\0';
    this->m_target[tLength]     = '\0';
    this->m_message[mLength]    = '\0';
    
    /* Fix players who have new lines in their names.. So it doesn't fuck up the terminal
     * Changes all \n to ' ' which may not be a good idea, because it then changes their names
     * and you can no longer reply to them and such ... Need to find a better way around this,
     * maybe ncurses will be the answer.. Luckily, TQ no longer allows new characters to have
     * newlines in their names, so don't have to worry about too many people having newlines.
     */
    for ( int i = 0; i < (int)strlen( this->m_sender ); i++ )
        ( this->m_sender[i] == '\n' ) ? this->m_sender[i] = ' ' : NULL;
    
    /* Do the same thing for the message, because sometimes players names are in it. */
    for ( int i = 0; i < (int)strlen( this->m_message ); i++ )
        ( this->m_message[i] == '\n' ) ? this->m_message[i] = ' ' : NULL;
    
    /* Sigh, do the same for target */
    for ( int i = 0; i < (int)strlen( this->m_target ); i++ )
        ( this->m_target[i] == '\n' ) ? this->m_target[i] = ' ' : NULL;
    
    switch ( this->m_channel )
    {
        case CH_NONE:
            strcpy( this->m_channelName, "None" );
            break;
            
        case CH_UNKNOWN:
            strcpy( this->m_channelName, "Unknown" );
            break;
            
        case CH_PRIVATE:
            strcpy( this->m_channelName, "Private" );
            break;
            
        case CH_ACTION:
            strcpy( this->m_channelName, "Action" );
            break;
            
        case CH_TEAM:
            strcpy( this->m_channelName, "Team" );
            break;
            
        case CH_GUILD:
            strcpy( this->m_channelName, "Guild" );
            break;
            
        case CH_SYSTEM:
            strcpy( this->m_channelName, "System" );
            break;
            
        case CH_SPOUSE:
            strcpy( this->m_channelName, "Spouse" );
            break;
            
        case CH_NORMAL:
            strcpy( this->m_channelName, "Normal" );
            break;
            
        case CH_SHOUT:
            strcpy( this->m_channelName, "Shout" );
            break;
            
        case CH_FRIEND:
            strcpy( this->m_channelName, "Friend" );
            break;
            
        case CH_BROADCAST:
            strcpy( this->m_channelName, "Broadcast" );
            break;
            
        case CH_GM:
            strcpy( this->m_channelName, "GM" );
            break;
            
        case CH_VENDOR:
            strcpy( this->m_channelName, "Vendor" );
            break;
            
        default:
            strcpy( this->m_channelName, "Unknown" );
            break;
    }
}

MessagePacket::MessagePacket( const char *sender, const char *target, 
                             const char *message, short channel, int color, 
                             short effect, const char *emotion )
{
    strcpy( this->m_sender, sender );
    strcpy( this->m_target, target );
    strcpy( this->m_message, message );
    strcpy( this->m_emotion, emotion );
    
    this->m_channel = channel;
    this->m_color   = color;
    this->m_effect  = effect;
    this->m_time    = NULL;
}

CPacket MessagePacket::pack( void )
{
    MESSAGE_PACKET_HEADER header;
    CPacket packet;

    header.color    = this->m_color;
    header.channel  = this->m_channel;
    header.effect   = this->m_effect;
    header.time     = this->m_time;
    
    strcpy( header.emotion, this->m_emotion );
    
    memcpy( ( void * )&packet.data, ( void * )&header, sizeof( header ) );
    memcpy( ( void * )( packet.data + 0x1E ), ( void * )this->m_sender, strlen( this->m_sender ) );
    memcpy( ( void * )( packet.data + 0x1F + strlen( this->m_sender ) ), ( void * )this->m_target, strlen( this->m_target ) );
    memcpy( ( void * )( packet.data + 0x20 + strlen( this->m_sender ) + strlen( this->m_target ) ), ( void * )this->m_message, 
           strlen( this->m_message ) );
    
    packet.data[28] = 0x03;
    packet.data[29] = (unsigned char)strlen( this->m_sender );
    packet.data[30 + strlen( this->m_sender )] = (unsigned char)strlen( this->m_target );
    packet.data[31 + strlen( this->m_sender ) + strlen( this->m_target )] = (unsigned char)strlen( this->m_message );
    
    for ( int i = 0; i < 3; i++ )
        packet.data[(sizeof( header ) + strlen( this->m_sender ) + strlen( this->m_target ) + strlen( this->m_message ) + 4 ) + i] = 0x00;
    
    packet.header.size  = ( sizeof( header ) + strlen( this->m_sender ) + strlen( this->m_target ) + strlen( this->m_message ) + 7 );
    packet.header.size  = packet.header.size + sizeof( CPacketHeader );
    packet.header.id    = 1004;
    
    return packet;
}