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


Rack.util.Essence=function(o){if(o){this.applyParams(o);}};Rack.util.Essence.prototype={target:null,setTarget:function(t){if(!this.target){this.target=t;}
return this;},applyParams:function(o){if(!this.params){this.params={};this.defaultParams={};}
return this.p_applyParams(this.params,o);},p_applyParams:function(c,o){if(o){Ext.apply(c,o);}
return this;},addParams:function(o){if(!this.params){this.params={};this.defaultParams={};}
this.p_applyParams(this.params,o);this.p_applyParams(this.defaultParams,o);this.p_createParamSetters(o);return this;},clearParams:function(){this.params=Rack.copy(this.defaultParams);return this;},paramSetterName:function(x){return'set'+x.substr(0,1).toUpperCase()+x.substr(1);},paramSetterFunction:function(p){return function(v){this.params[p]=v;return this;};},p_createParamSetters:function(o){var x,p;for(x in o){p=this.paramSetterName(x);if(!this[p]){this[p]=this.paramSetterFunction(x);}}
return this;},validate:function(){return true;},create:function(){var Target=this.target;if(!Target){throw new TypeError('Invalid Essence.  Target has not been defined.');}
if(this.validate()){return new Target(this.params);}}};