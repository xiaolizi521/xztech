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


Rack.widget.AccordionPanelStatusProxy=function(config){Ext.apply(this,config);this.id=this.id||Ext.id();this.el=new Ext.Layer({dh:{id:this.id,tag:"div",cls:"x-dd-drag-proxy "+this.dropNotAllowed,children:[{tag:"div",cls:"x-dd-drop-icon"},{tag:'div',cls:'x-dd-drag-ghost rack-accordion-panel-ghost',children:[{tag:"div",cls:"rack-accordion-panel-ghost-inner"}]}]},shadow:!config||config.shadow!==false});this.ghost=Ext.get(this.el.dom.childNodes[1].childNodes[0]);this.dropStatus=this.dropNotAllowed;};Ext.extend(Rack.widget.AccordionPanelStatusProxy,Ext.dd.StatusProxy);