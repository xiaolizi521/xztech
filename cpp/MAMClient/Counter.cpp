/*
 *  Counter.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 10/4/08.
 *  Copyright 2008 __MyCompanyName__. All rights reserved.
 *
 */

#include "Counter.h"

Counter::Counter( void ) :
  first(NULL)
, second(NULL)
{}

Counter::~Counter( void )
{
}

void Counter::reset( void )
{
    this->first     = NULL;
    this->second    = NULL;
}

const Counter Counter::operator++( int )
{
    Counter tmp(*this);
    
    this->first++;
	
    if ( this->first == 256 )
    {
        this->first = NULL;
        this->second++;
    }
	
    if ( this->second == 256 )
        this->second = NULL;
    
    return *this;
}