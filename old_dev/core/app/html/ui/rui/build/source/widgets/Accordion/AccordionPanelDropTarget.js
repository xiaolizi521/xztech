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


Rack.widget.AccordionPanelDropTarget=function(panel,config){this.panel=panel;this.el=panel.getContainer();Ext.apply(this,config);Ext.dd.ScrollManager.register(this.el);Ext.dd.DropTarget.superclass.constructor.call(this,this.el.dom,this.ddGroup||this.group,{isTarget:true});};Ext.extend(Rack.widget.AccordionPanelDropTarget,Ext.dd.DDTarget,{isTarget:true,isNotifyTarget:true,dropAllowed:"x-dd-drop-ok",dropNotAllowed:"x-dd-drop-nodrop",notifyEnter:function(dd,e,data){if(data.panel.getId()!==this.panel.getId()){this.showOver();return this.dropAllowed;}
return this.dropNotAllowed;},notifyOver:function(dd,e,data){if(data.panel.getId()!==this.panel.getId()){return this.dropAllowed;}
return this.dropNotAllowed;},notifyOut:function(dd,e,data){if(data.panel.getId()!==this.panel.getId()){this.hideOver();}},notifyDrop:function(dd,e,data){if(data.panel.getId()!==this.panel.getId()){this.hideOver();data.panel.insertBefore(this.panel.getId());return true;}
return false;},overClass:'rack-accordion-panel-on',showOver:function(){this.el.addClass(this.overClass);},hideOver:function(){this.el.removeClass(this.overClass);}});