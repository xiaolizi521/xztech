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


Rack.util.BehavioralEssence=function(o){Rack.util.BehavioralEssence.superclass.constructor.call(this,o);};Ext.extend(Rack.util.BehavioralEssence,Rack.util.Essence,{using:function(c){if(!this.behaviors){this.behaviors=[];}
this.behaviors.push(c);this.addParams(c.prototype.getParamDefaults());return this;},create:function(){var Target=this.target;if(!Target){throw new TypeError('Invalid Essence.  Target has not been defined.');}
if(this.behaviors){Target=Rack.mix(this.target,this.behaviors);}
if(this.validate()){return new Target(this.params);}}});