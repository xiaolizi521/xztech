/*
 *  Splitter.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 7/4/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

#include <vector>
#include <string>

class Splitter 
{
    std::vector<std::string> _tokens;
public:
    typedef std::vector<std::string>::size_type size_type;
public:
    Splitter ( const std::string& src, const std::string& delim );
    
    std::string& operator[] ( size_type i );
    
    size_type size() const;
    
    void reset ( const std::string& src, const std::string& delim );
};