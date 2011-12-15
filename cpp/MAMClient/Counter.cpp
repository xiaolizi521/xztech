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
  first(0)
, second(0)
{}

Counter::~Counter( void )
{
}

void Counter::reset( void )
{
    this->first     = 0;
    this->second    = 0;
}

const Counter Counter::operator++( int )
{
    Counter tmp(*this);
    
    this->first++;
	
    if ( this->first == 256 )
    {
        this->first = 0;
        this->second++;
    }
	
    if ( this->second == 256 )
        this->second = 0;
    
    return *this;
}
