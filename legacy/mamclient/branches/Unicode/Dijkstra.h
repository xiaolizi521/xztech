/*
 *  Dijkstra.h
 *  MAMClient
 *
 *  Created by Marc Addeo on 5/11/09.
 *  Copyright 2009 __MyCompanyName__. All rights reserved.
 *
 */

/* Copyright (c) 2008 the authors listed at the following URL, and/or
 the authors of referenced articles or incorporated external code:
 http://en.literateprograms.org/Dijkstra's_algorithm_(C_Plus_Plus)?action=history&offset=20080520195640
 
 Permission is hereby granted, free of charge, to any person obtaining
 a copy of this software and associated documentation files (the
 "Software"), to deal in the Software without restriction, including
 without limitation the rights to use, copy, modify, merge, publish,
 distribute, sublicense, and/or sell copies of the Software, and to
 permit persons to whom the Software is furnished to do so, subject to
 the following conditions:
 
 The above copyright notice and this permission notice shall be
 included in all copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 
 Retrieved from: http://en.literateprograms.org/Dijkstra's_algorithm_(C_Plus_Plus)?oldid=13422
 */
#ifndef _DIJKSTRA_H
#define _DIJKSTRA_H

#include <vector>
#include <string>
#include <map>
#include <list>
#include <set>
#include <limits>

typedef int vertex_t;
typedef double weight_t;

struct edge {
    vertex_t target;
    weight_t weight;
    edge( vertex_t arg_target ) : target( arg_target ), weight( 1 ) { }
};

typedef std::map< vertex_t, std::list< edge > > adjacency_map_t;

template < typename T1, typename T2 >
struct pair_first_less
{
    bool operator()( std::pair< T1,T2 > p1, std::pair< T1,T2 > p2) const 
    {
        if( p1.first == p2.first ) {
            //Otherwise the initial vertex_queue will have the size 2 { 0,source ; inf;n }
            return p1.second < p2.second;
        }
        return p1.first < p2.first;
    }
};

void DijkstraComputePaths( vertex_t source, adjacency_map_t& adjacency_map, 
                            std::map< vertex_t, weight_t >& min_distance,
                            std::map< vertex_t, vertex_t >& previous );

std::list< vertex_t > DijkstraGetShortestPathTo( vertex_t target, std::map< vertex_t, vertex_t >& previous );

#endif