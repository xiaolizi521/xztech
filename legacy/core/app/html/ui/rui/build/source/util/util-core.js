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


Rack.util.removeContextMenu=function(el){Ext.fly(el).on('contextmenu',function(e){e.stopEvent();});return this;};Rack.util.addContextMenu=function(el,menu){var show=function(e){menu.showAt([e.getPageX(),e.getPageY()]);};if(Ext.isOpera){Ext.fly(el).on('mousedown',function(e){if(e.ctrlKey){show(arguments);}});}else{Ext.fly(el).on('contextmenu',show);}
return this;};(function(){var parsers=[];Rack.util.startParsers=function(){this.runParsers(true);};Rack.util.runParsers=function(all){parsers.forEach(function(e){e[0].apply(e[1]||window,[(all)?null:this.dom]);});return this;};Rack.util.registerParser=function(p,s){parsers.push([p,s]);return this;};Ext.Element.prototype.update=Rack.sequence(Ext.Element.prototype.update,Rack.util.runParsers);Ext.onReady(Rack.util.startParsers,Rack.util);Rack.util.registerParser(function(el){Ext.DomQuery.select('table.rack-grid',el).map(function(e,i,a){Ext.fly(e).removeClass('rack-grid');return e;}).forEach(function(e,i,a){var g=new Ext.grid.TableGrid(e);g.render();g.getSelectionModel().lock();});});Rack.util.registerParser(function(el){Ext.DomQuery.select('a.rack-button',el).map(function(e,i,a){Ext.fly(e).removeClass('rack-button');return e;}).forEach(function(e,i,a){if(e&&e.parentNode){var b=new Ext.Button(e.parentNode,{text:e.innerHTML,handler:e.onclick});b.el.insertBefore(e);Ext.fly(e).remove();}});});})();