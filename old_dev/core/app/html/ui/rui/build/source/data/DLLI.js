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


Rack.data.DLLI=function(){this.superclass=Rack.data.DLLI.superclass;};Ext.extend(Rack.data.DLLI,Rack.data.DLL,{index:{},item:function(id){return this.index[id];},insertAfter:function(node,oldNode){this.index[node.id]=node;this.superclass.insertAfter.apply(this,arguments);},insertBefore:function(node,oldNode){this.index[node.id]=node;this.superclass.insertBefore.apply(this,arguments);},insertBeginning:function(node){this.index[node.id]=node;this.superclass.insertBeginning.apply(this,arguments);},insertEnd:function(node){this.index[node.id]=node;this.superclass.insertEnd.apply(this,arguments);},swap:function(node1,node2){var t=node1.id;node1.id=node2.id;node2.id=t;this.index[node1.id]=node1;this.index[node2.id]=node2;this.superclass.swap.apply(this,arguments);}});Rack.data.DLLINode=function(id,data){this.id=id;Rack.data.DLLINode.superclass.constructor.call(this,data);};Ext.extend(Rack.data.DLLINode,Rack.data.DLLNode);