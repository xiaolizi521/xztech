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


Rack.behavior.Remote=function(config){this.p_loading=false;this.p_loaded=false;if(config.url){this.setUrl(config.url,config.params,config.urlOptions);}};Rack.behavior.Remote.prototype={getParamDefaults:function(){return{url:null,params:{},urlOptions:{timeout:10}};},getUpdateManager:function(){return this.getBody().getUpdateManager();},setUrl:function(url,params,urlOptions){this.p_currentRefresher=this.p_refresher.createDelegate(this,[url,params,urlOptions]);this.refresh();return this.getUpdateManager();},p_refresher:function(url,params,urlOptions){var update=Rack.copy(urlOptions);if(!this.p_loading){this.getBodyContainer().addClass('rack-panel-body-clip');update.url=url;update.params=params;update.callback=update.callback?Rack.sequence([this.p_contentReady,this],[update.callback,update.scope]):Rack.scope([this.p_contentReady,this]);this.p_loading=true;this.getUpdateManager().update(update);}},refresh:function(){if(this.p_currentRefresher){this.p_loaded=false;this.p_currentRefresher();}
return this;},p_contentReady:function(el,s,r){if(!s){this.setContent('Failed to load content from server.');}
this.syncContentHeight();this.getBodyContainer().removeClass('rack-panel-body-clip');this.p_loading=false;this.p_loaded=true;}};