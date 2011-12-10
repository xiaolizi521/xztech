/*
 *  ConfigFile.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/6/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#ifndef _CONFIG_H
#define _CONFIG_H

#include <string>
#include <map>
#include <fstream>

class ConfigFile 
{
protected:
    std::map< std::string, std::string > content;
    
public:
    ConfigFile( const std::string & ); 
    ConfigFile( void );
    
    void parse( const std::string & );
    void operator= ( const std::string & );
    
    std::string operator[] ( const std::string & );
    std::string Value( const std::string  &, const std::string & );
};

#endif