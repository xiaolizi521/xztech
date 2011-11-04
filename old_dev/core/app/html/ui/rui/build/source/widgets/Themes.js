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


Rack.widget.Themes={DEFAULT:[],AERO:['/ui/ext/resources/css/ytheme-aero.css','/ui/rui/resources/css/theme-aero.css'],GRAY:['/ui/ext/resources/css/ytheme-gray.css','/ui/rui/resources/css/theme-gray.css'],VISTA:['/ui/ext/resources/css/ytheme-vista.css','/ui/rui/resources/css/theme-vista.css'],state:{theme:[]},show:function(theme,hide){var addCSS=this.addExternalCSS;Ext.select('link.rack-themes-include').set({disabled:'true'}).remove();Ext.each(theme,function(e){addCSS(e);});this.state.theme=theme;this.storeState();if(!this.dialogHidden){this.hideDialog();}},addExternalCSS:function(url){var l=document.createElement('link');l.rel='stylesheet';l.type='text/css';l.href=url;l.className='rack-themes-include';document.getElementsByTagName('head')[0].appendChild(l);},dialog:null,dialogHidden:true,showDialog:function(from){var dialog,layout,dh;if(!this.dialog){dh=Ext.DomHelper;dialog=new Ext.BasicDialog(Ext.id(),{autoCreate:true,title:'Theme Picker',modal:true,width:300,height:230,shadow:true,proxyDrag:true,resizeHandles:'none',syncHeightBeforeShow:true,autoScroll:false});dialog.addKeyListener(27,this.hideDialog,this);Ext.fly(dh.append(dialog.body,{tag:'a',href:'#',cls:'rack-themes-btn rack-themes-default-btn',html:'Default'})).on('click',function(e){e.stopEvent();this.show(this.DEFAULT);},this);Ext.fly(dh.append(dialog.body,{tag:'a',href:'#',cls:'rack-themes-btn rack-themes-aero-btn',html:'Aero'})).on('click',function(e){e.stopEvent();this.show(this.AERO);},this);Ext.fly(dh.append(dialog.body,{tag:'a',href:'#',cls:'rack-themes-btn rack-themes-gray-btn',html:'Gray'})).on('click',function(e){e.stopEvent();this.show(this.GRAY);},this);Ext.fly(dh.append(dialog.body,{tag:'a',href:'#',cls:'rack-themes-btn rack-themes-vista-btn',html:'Vista'})).on('click',function(e){e.stopEvent();this.show(this.VISTA);},this);this.dialog=dialog;}
this.dialogHidden=false;this.dialog.show(from);},hideDialog:function(callback){this.dialog.hide(callback);this.dialogHidden=true;},restoreState:function(provider){this.provider=provider||Ext.state.Manager;var state=this.provider.get("rack-themes-state");if(state&&state.theme){this.show(state.theme);this.state=state;}
return this;},storeState:function(){this.provider.set("rack-themes-state",this.state);return this;}};