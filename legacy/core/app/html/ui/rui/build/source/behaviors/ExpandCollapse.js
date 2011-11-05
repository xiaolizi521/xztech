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


Rack.behavior.ExpandCollapse=function(config){this.p_expanding=false;this.p_expandCallback=null;this.p_collapsing=false;this.p_collapseCallback=null;this.collapsed=false;this.alwaysExpanded=config.alwaysExpanded;this.alwaysCollapsed=config.alwaysCollapsed;this.expandDuration=config.expandDuration;this.expandTransition=config.expandTransition;this.collapseDuration=config.collapseDuration;this.collapseTransition=config.collapseTransition;this.hideContentOnExpandCollapse=config.hideContentOnExpandCollapse;this.addEvents({'beforeexpand':true,'expand':true,'beforecollapse':true,'collapse':true});if(this.alwaysExpanded){this.expand(null,true);}else if(this.alwaysCollapsed){this.collapse(null,true);}else if(config.collapsed){this.collapse(null,true);}else{this.expand(null,true);}};Rack.behavior.ExpandCollapse.prototype={getParamDefaults:function(){return{collapsed:false,alwaysExpanded:false,alwaysCollapsed:false,expandDuration:0.35,expandTransition:'easeOut',collapseDuration:0.35,collapseTransition:'easeOut',hideContentOnExpandCollapse:false};},getAlwaysExpanded:function(){return this.alwaysExpanded;},getAlwaysCollapsed:function(){return this.alwaysCollapsed;},expand:function(cb,now){var bc=this.getBodyContainer();if(this.p_expanding||!this.collapsed){return this;}
var e={};this.fireEvent('beforeexpand',this,e);if(e.cancel!==true){this.p_expanding=true;this.p_expandCallback=cb;bc.removeClass('rack-hide');if(now||!this.expandDuration){bc.setHeight(this.getBody().getHeight());this.afterExpand();}else{bc.setHeight(this.bodyHeight||this.getBody().getHeight(),{duration:this.expandDuration,callback:Rack.scope([this.afterExpand,this]),method:this.expandTransition});}}
return this;},afterExpand:function(){if(this.hideContentOnExpandCollapse){this.getBody().removeClass('rack-hide');}
this.getBodyContainer().removeClass('rack-panel-body-clip');this.syncContentHeight();this.p_expanding=false;this.collapsed=false;if(this.p_expandCallback){this.p_expandCallback();}
this.fireEvent('expand',this);},collapse:function(cb,now){var bc=this.getBodyContainer();if(this.p_collapsing||this.collapsed){return this;}
var e={};this.fireEvent('beforecollapse',this,e);if(e.cancel!==true){this.p_collapsing=true;this.p_collapseCallback=cb;if(this.hideContentOnExpandCollapse){this.getBody().addClass('rack-hide');}
bc.addClass('rack-panel-body-clip');if(now||!this.collapseDuration){bc.setHeight(1);this.afterCollapse();}else{bc.setHeight(1,{duration:this.collapseDuration,callback:Rack.scope([this.afterCollapse,this]),method:this.collapseTransition});}}},afterCollapse:function(){this.getBodyContainer().addClass('rack-hide');this.p_collapsing=false;this.collapsed=true;if(this.p_collapseCallback){this.p_collapseCallback();}
this.fireEvent('collapse',this);},isCollapsed:function(){return this.collapsed;}};