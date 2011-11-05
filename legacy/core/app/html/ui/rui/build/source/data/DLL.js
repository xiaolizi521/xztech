/*
 * Rackspace Extensions
 * Copyright(c) 2007 Rackspace Managed Hosting
 * Author: Steve Kollars
 * Version: 1.0
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */


Rack.data.DLL=function(){};Rack.data.DLL.prototype={firstNode:null,lastNode:null,insertAfter:function(node,oldNode){node.prev=oldNode;node.next=oldNode.next;if(oldNode.next===null){this.lastNode=node;}else{oldNode.next.prev=node;}
oldNode.next=node;},insertBefore:function(node,oldNode){node.prev=oldNode.prev;node.next=oldNode;if(oldNode.prev===null){this.firstNode=node;}else{oldNode.prev.next=node;}
oldNode.prev=node;},insertBeginning:function(node){if(this.firstNode===null){this.firstNode=node;this.lastNode=node;node.prev=null;node.next=null;}else{this.insertBefore(node,this.firstNode);}},insertEnd:function(node){if(this.lastNode===null){this.insertBeginning(node);}else{this.insertAfter(node,this.lastNode);}},remove:function(node){if(!node){return null;}
if(node.prev===null){this.firstNode=node.next;}else{node.prev.next=node.next;}
if(node.next===null){this.lastNode=node.prev;}else{node.next.prev=node.prev;}
return node;},swap:function(node1,node2){var t=node1.data;node1.data=node2.data;node2.data=t;},listOrdered:function(){var item=this.firstNode;var list=[];if(item){do{list.push(Rack.copy(item));item=item.next;}while(item);}
return list;}};Rack.data.DLLNode=function(data){this.next=null;this.prev=null;this.data=data;};