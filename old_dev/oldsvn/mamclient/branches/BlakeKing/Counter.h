/*
 *  Counter.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 10/4/08.
 *  Copyright 2008 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _COUNTER_H
#define _COUNTER_H

#include <stdlib.h>

class Counter
{
public:
	
    Counter( void );
    ~Counter( void );
    
    void reset( void );
	
    void operator ++( int );
    
public:
    
	int first;
	int second;
    
};

#endif