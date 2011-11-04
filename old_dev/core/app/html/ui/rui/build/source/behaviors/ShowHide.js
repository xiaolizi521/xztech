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


Rack.behavior.ShowHide=function(config){this.p_showing=false;this.p_showCallback=null;this.p_hiding=false;this.p_hideCallback=null;this.hidden=false;this.showDuration=config.showDuration;this.hideDuration=config.hideDuration;this.addEvents({'beforeshow':true,'show':true,'beforehide':true,'hide':true});if(config.hidden){this.hide(null,true);}else{this.show(null,true);}};Rack.behavior.ShowHide.prototype={getParamDefaults:function(){return{hidden:false,showDuration:0.30,hideDuration:0.20};},show:function(cb,now){if(this.p_showing||!this.hidden){return this;}
var e={};this.fireEvent('beforeshow',this,e);if(e.cancel!==true){this.p_showing=true;this.p_showCallback=cb;if(now||!this.showDuration){this.getContainer().setVisible(true);this.afterShow();}else{this.getContainer().fadeIn({endOpacity:1,duration:this.showDuration,callback:Rack.scope([this.afterShow,this])});}}
return this;},afterShow:function(){this.p_showing=false;this.hidden=false;if(this.p_showCallback){this.p_showCallback();}
this.fireEvent('show',this);},hide:function(cb,now){if(this.p_hiding||this.hidden){return this;}
var e={};this.fireEvent('beforehide',this,e);if(e.cancel!==true){this.p_hiding=true;this.p_hideCallback=cb;if(now||!this.hideDuration){this.getContainer().setVisible(false);this.afterHide();}else{this.getContainer().fadeOut({endOpacity:0,duration:this.hideDuration,callback:Rack.scope([this.afterHide,this])});}}
return this;},afterHide:function(){this.p_hiding=false;this.hidden=true;if(this.p_hideCallback){this.p_hideCallback();}
this.fireEvent('hide',this);},isHidden:function(){return this.hidden;}};