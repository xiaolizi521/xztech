/*
 *  ConfigFile.cpp
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/6/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include "ConfigFile.h"

ConfigFile::ConfigFile( const std::string &configFile ) : content()
{
	this->parse( configFile );
}

ConfigFile::ConfigFile( void ) : content() {}

ConfigFile& ConfigFile::operator= ( const std::string &configFile )
{
	this->parse( configFile );
    
    return *this;
}

void ConfigFile::parse( const std::string &configFile )
{
	std::ifstream file( configFile.c_str() );
	
	std::string line;
	std::string section;
	std::string key;
	std::string value;
	size_t found;
    
	while ( std::getline( file, line ) )
	{
		if ( !line.length() )
			continue;
        
		if ( line[0] == ';' )
			continue;
        
		if ( line[0] == '[' )
		{
			section.clear();
            
			for ( int32_t i = 1; line[i] != ']'; i++ )
				section += line[i];
            
			continue;
		}
        
		found = line.find('=');
        
		if ( found != std::string::npos )
		{
			key.clear();
			value.clear();
            
			for ( size_t i = 0; line[i] != '='; i++ )
				key += line[i];
            
			for ( size_t i = found + 1; i < (size_t)line.length(); i++ )
				value += line[i];
            
			std::string path = section;
			path += '/';
			path += key;
            
			this->content[path] = value;
		}
	}
}

std::string ConfigFile::operator[] ( const std::string &path )
{
	return this->content[path];
}

std::string ConfigFile::Value( const std::string  &section, const std::string &key )
{
	std::map< std::string, std::string >::iterator it;
	std::string path = section + "/" + key;
    
	std::string value = this->content[path];
    
	if ( !value.empty() )
		return value;
    
	this->content.erase( path );
    
	return "NOTFOUND";
}
