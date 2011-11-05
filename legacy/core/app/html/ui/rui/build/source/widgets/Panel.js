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


Rack.widget.PanelEssence=function(o){this.setTarget(Rack.widget.Panel);this.addParams({parent:null,header:null,toolbar:null,body:null,footer:null,content:null,loadScripts:false,grid:null,bodyHeight:null});Rack.widget.PanelEssence.superclass.constructor.call(this,o);};Ext.extend(Rack.widget.PanelEssence,Rack.util.BehavioralEssence,{validate:function(){var p=this.params;if(!p.parent){throw new TypeError('Invalid PanelEssence.  The parent parameter is required and must be an id, DOM element or Ext.element object.');}
p.parent=Ext.get(p.parent,true);if(!p.parent){throw new TypeError('Invalid PanelEssence.  The parent parameter is not a valid id, DOM element or Ext.element object.');}
return true;}});Rack.widget.Panel=function(config){this.parent=config.parent;this.container=Ext.get(this.containerTemplate.append(this.parent,{}));this.headerContainer=this.container.child('.rack-panel-header');this.header=null;if(config.header){this.header=(new Rack.widget.TitlebarEssence(config.header)).setParent(this.headerContainer).create();}
this.toolbarContainer=this.container.child('.rack-panel-toolbar');this.toolbar=null;if(Array.si(config.toolbar)){this.toolbar=new Ext.Toolbar(this.toolbarContainer,config.toolbar);}
this.bodyContainer=this.container.child('.rack-panel-body');this.body=this.bodyContainer.child('.rack-panel-body-widget');if(config.body&&!config.grid){this.body=Ext.get(config.body).replace(this.body);}
this.grid=null;if(config.grid){this.grid=this.createGrid(config.grid);if(this.grid){this.body.appendChild(this.grid.container);this.body.removeClass('rack-panel-body-widget');}}
this.bodyHeight=null;if(this.bodyHeight){this.setHeight(config.bodyHeight);}
this.bodyWidth=null;if(this.bodyWidth){this.setWidth(config.bodyWidth);}
this.content=null;this.loadScripts=null;if(this.content){this.setContent(config.content,config.loadScripts);}
this.footerContainer=this.container.child('.rack-panel-footer');this.footer=null;if(Array.si(config.footer)){this.footer=new Ext.Toolbar(this.footerContainer,config.footer);}
this.id=(this.body)?this.body.id:Ext.id();};Ext.extend(Rack.widget.Panel,Ext.util.Observable,{getId:function(){return this.id;},getParent:function(){return this.parent;},getContainer:function(){return this.container;},getHeaderContainer:function(){return this.headerContainer;},getHeader:function(){return this.header;},getToolbarContainer:function(){return this.toolbarContainer;},getToolbar:function(){return this.toolbar;},getBodyContainer:function(){return this.bodyContainer;},getBody:function(){return this.body;},getFooterContainer:function(){return this.footerContainer;},getFooter:function(){return this.footer;},setContent:function(content,loadScripts){this.content=content;this.loadScripts=loadScripts;this.body.update(content,loadScripts);this.syncContentHeight();return this;},syncContentHeight:function(){this.bodyContainer.setHeight(this.bodyHeight||this.body.getHeight());return this;},getHeight:function(){return this.bodyHeight;},setHeight:function(x){if(x<0){this.bodyHeight=null;this.bodyContainer.setStyle('height','').removeClass('rack-panel-body-setheight');}else{this.bodyHeight=x;this.bodyContainer.setHeight(x).addClass('rack-panel-body-setheight');}
return this;},getWidth:function(){return this.bodyWidth;},setWidth:function(x){this.bodyWidth=x;this.bodyContainer.setWidth(x);return this;},createGrid:function(grid){return(Function.si(grid))?grid():grid;},getGrid:function(){return this.grid;},destroy:function(remove){if(this.header&&this.header.destroy){this.header.destroy(true);}
this.header=null;this.headerContainer.removeAllListeners().remove();this.headerContainer=null;if(this.toolbar&&this.toolbar.destroy){this.toolbar.destroy(true);}
this.toolbar=null;this.toolbarContainer.removeAllListeners().remove();this.toolbarContainer=null;if(this.footer&&this.footer.destroy){this.footer.destroy(true);}
this.footer=null;this.footerContainer.removeAllListeners().remove();this.footerContainer=null;if(this.grid&&this.grid.destroy){this.grid.destroy(true);}
this.grid=null;this.body.removeAllListeners().remove();this.body=null;this.bodyContainer.removeAllListeners().remove();this.bodyContainer=null;this.container.removeAllListeners().remove();this.container=null;this.purgeListeners();}});Rack.widget.Panel.prototype.containerTemplate=function(){var tpl=Ext.DomHelper.createTemplate({tag:'div',cls:'rack-panel',children:[{tag:'div',cls:'rack-panel-header'},{tag:'div',cls:'rack-panel-toolbar'},{tag:'div',cls:'rack-panel-body',children:[{tag:'div',cls:'rack-panel-body-widget'}]},{tag:'div',cls:'rack-panel-footer'}]});tpl.compile();return tpl;}();