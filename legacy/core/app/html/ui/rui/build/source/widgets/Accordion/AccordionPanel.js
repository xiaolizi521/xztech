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


Rack.widget.AccordionPanel=function(config){this.addEvents({'activate':true,'beforeexpand':true});this.accordion=config.accordion;this.stateId=config.stateId;this.collapsed=config.collapsed;this.loadOnce=config.loadOnce;this.moveable=config.moveable;this.alwaysOpen=config.alwaysOpen;this.alwaysClosed=config.alwaysClosed;this.expandDuration=config.expandDuration;this.expandTransition=config.expandTransition;this.collapseDuration=config.collapseDuration;this.collapseTransition=config.collapseTransition;this.fadeInDuration=config.fadeInDuration;this.fadeOutDuration=config.fadeOutDuration;this.enableDD=this.accordion.config.enableDD;config.header=config.header||{text:config.title||'Accordion Panel'};Rack.widget.AccordionPanel.superclass.constructor.call(this,config);this.header.el.on('click',function(){this.activate();},this);this.header.el.addClass('rack-accordion-panel-title');this.loading=false;this.loaded=false;this.reloadEl=this.header.addButton('Reload','rack-tbicon-reload-dlg',function(e){e.stopPropagation();this.refresh();},this);if(config.loadOnce){this.showReloadButton();}else{this.hideReloadButton();}
if(this.enableDD&&this.moveable){this.elDS=new Rack.widget.AccordionPanelDragSource(this.getContainer(),{ddGroup:this.accordion.el.id,dragData:{panel:this},scroll:false});this.elDS.setHandleElId(this.header.id);this.elDT=new Rack.widget.AccordionPanelDropTarget(this,{ddGroup:this.accordion.el.id});}};Ext.extend(Rack.widget.AccordionPanel,Rack.widget.Panel,{getStateId:function(){return this.stateId;},remove:function(){this.accordion.removePanel(this.id);},showReloadButton:function(){this.reloadEl.removeClass('rack-display-hide');},hideReloadButton:function(){this.reloadEl.addClass('rack-display-hide');},activate:function(now){this.accordion.activate(this.id,now);this.fireEvent('activate',this);return this;},insertBefore:function(id){this.hide(this.accordion.insertPanelBefore.createDelegate(this.accordion,[this.getId(),id]).createSequence(this.show.createDelegate(this,[null]),this));},createGrid:function(grid){if(Function.si(grid)){this.on('beforeexpand',function(){if(!this.grid){this.grid=this.createGrid(grid());this.body.appendChild(this.grid.container);this.body.removeClass('rack-panel-body-widget');}},this);return null;}
return grid;}});