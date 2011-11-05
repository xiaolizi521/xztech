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


Rack.widget.AccordionStateManager=function(){this.state={order:[],panels:{}};};Rack.widget.AccordionStateManager.prototype={init:function(accordion,stateId,provider){this.provider=provider;this.id=stateId+'-accordion-state';var state=provider.get(this.id);if(state){var pid;if(Array.si(state.order)){Ext.each(state.order,function(e,a,i){accordion.insertPanelEnd(e);});}else{state.order=[];}
if(state.panels){for(pid in state.panels){accordion.restorePanelState(pid,state.panels[pid])}}else{state.panels={};}
this.state=state;}
this.accordion=accordion;accordion.on('panelexpanded',this.onPanelExpanded,this);accordion.on('panelcollapsed',this.onPanelCollapsed,this);accordion.on('panelorder',this.onPanelOrder,this);},storeState:function(){this.provider.set(this.id,this.state);},onPanelExpanded:function(accordion,panel){var pid=panel.getStateId();if(!this.state.panels[pid]){this.state.panels[pid]={};}
this.state.panels[pid].exp=true;this.storeState();},onPanelCollapsed:function(accordion,panel){var pid=panel.getStateId();if(!this.state.panels[pid]){this.state.panels[pid]={};}
this.state.panels[pid].exp=false;this.storeState();},onPanelOrder:function(accordion,list){this.state.order=list.map(function(e,a,i){return accordion.getPanel(e).getStateId();});this.storeState();}};