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


Rack.widget.AccordionEssence=function(o){this.setTarget(Rack.widget.Accordion);this.addParams({parent:null,multi:false,stateId:null,panelHeight:null,enableDD:false});Rack.widget.AccordionEssence.superclass.constructor.call(this,o);};Ext.extend(Rack.widget.AccordionEssence,Rack.util.Essence,{validate:function(){var p=this.params;if(!p.parent){throw new TypeError('AccordionEssence is not valid.  The parent parameter is required and must be an id, DOM element or Ext.Element object.');}
p.parent=Ext.get(p.parent,true);if(!p.parent){throw new TypeError('AccordionEssence is not valid.  The parent parameter is not a valid id, DOM element or Ext.Element object.');}
return true;}});