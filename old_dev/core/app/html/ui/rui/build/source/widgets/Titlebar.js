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


Rack.widget.TitlebarEssence=function(o){this.setTarget(Rack.widget.Titlebar);this.addParams({parent:null,id:Ext.id(),text:'&#160;',buttons:[],style:'dlg'});Rack.widget.TitlebarEssence.superclass.constructor.call(this,o);};Ext.extend(Rack.widget.TitlebarEssence,Rack.util.Essence,{validate:function(){var p=this.params;if(!p.parent){throw new TypeError('Invalid TitlebarEssence.  The parent parameter is required and must be an id, DOM element or Ext.Element object.');}
p.parent=Ext.get(p.parent,true);if(!p.parent){throw new TypeError('Invalid TitlebarEssence.  The parent parameter is not a valid id, DOM element or Ext.Element object.');}
return true;}});Rack.widget.Titlebar=function(config){this.parent=config.parent;this.id=config.id;this.style=config.style;this.el=Ext.get(this.baseTemplate.append(this.parent,{id:this.id,style:this.style}));this.text=this.el.child('.rack-titlebar-text');this.tools=this.el.child('.rack-titlebar-tools');this.setText(config.text);this.addButtons(config.buttons);};Rack.widget.Titlebar.prototype={setText:function(text){this.title=text;this.text.update(text);this.text.dom.title=text;return this;},addButton:function(title,cls,fn,scope){var el=Ext.get(this.buttonTemplate.append(this.tools,{title:title,cls:cls}));el.addClassOnOver('rack-titlebar-icon-on-'+this.style);if(fn){el.on('click',fn,scope);}
return el;},addButtons:function(buttons){buttons.forEach(function(e,i,a){this.addButton(e.title,e.cls,e.click,e.scope);}.createDelegate(this));},getHeight:function(){return this.el.getHeight();},destroy:function(removeEl){YAHOO.util.Event.purgeElement(this.el.dom,true);this.el.remove();}};Rack.widget.Titlebar.prototype.baseTemplate=function(){var tpl=Ext.DomHelper.createTemplate({tag:'div',id:'{id}',cls:'rack-titlebar rack-titlebar-{style}',children:[{tag:'span',cls:'rack-titlebar-text rack-titlebar-text-{style}'},{tag:'div',cls:'rack-titlebar-tools'}]});tpl.compile();return tpl;}();Rack.widget.Titlebar.prototype.buttonTemplate=function(){var tpl=Ext.DomHelper.createTemplate({tag:'div',cls:'rack-titlebar-icon',title:'{title}',children:[{tag:'div',cls:'rack-titlebar-icon-inner {cls}',title:'{title}',html:'&#160;'}]});tpl.compile();return tpl;}();