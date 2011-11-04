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


Rack.behavior.AccordionPanelRemote=function(config){this.p_loading=false;this.p_loaded=false;if(config.url){this.setUrl(config.url,config.params,config.loadOnce,config.urlOptions);}};Ext.extend(Rack.behavior.AccordionPanelRemote,Rack.behavior.Remote,{getParamDefaults:function(){return{url:null,params:{},loadOnce:false,urlOptions:{timeout:10}};},setUrl:function(url,params,loadOnce,urlOptions){if(this.p_currentRefresher){this.un('beforeexpand',this.p_currentRefresher);}
if(loadOnce){this.p_loaded=false;this.showReloadButton();}else{this.hideReloadButton();}
this.p_currentRefresher=this.p_refresher.createDelegate(this,[url,params,loadOnce,urlOptions]);this.on('beforeexpand',this.p_currentRefresher);if(!this.isCollapsed()){this.p_currentRefresher();}
return this.getUpdateManager();},p_refresher:function(url,params,loadOnce,urlOptions){if(loadOnce&&this.p_loaded){return;}
Rack.behavior.AccordionPanelRemote.superclass.p_refresher.call(this,url,params,urlOptions);}});