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


Rack.widget.Accordion=function(config){this.parent=config.parent;this.container=this.el=Ext.DomHelper.append(this.parent,{},true);this.panels=new Rack.data.DLLI();this.stateIdMap={};this.active=null;this.config=config;this.panelEssence=(new Rack.widget.AccordionPanelEssence()).using(Rack.behavior.ShowHide).using(Rack.behavior.ExpandCollapse).using(Rack.behavior.AccordionPanelRemote);this.addEvents({'beforepanelactivate':true,'panelactivate':true,'panelexpanded':true,'panelcollapsed':true,'panelorder':true});};Ext.extend(Rack.widget.Accordion,Ext.util.Observable,{getParent:function(){return this.el;},getContainer:function(){return this.el;},getBody:function(){return this.el;},setHeight:function(x){if(this.config.multi){return this;}
this.el.setHeight(x);var p=this.panels.firstNode;while(p){x-=p.data.getHeader().getHeight();p=p.next;}
p=this.panels.firstNode;while(p){p.data.setHeight(x);p=p.next;}
return this;},setWidth:function(x){p=this.panels.firstNode;while(p){p.data.setWidth(x);p=p.next;}
this.el.setWidth(x);return this;},addPanel:function(config){var p=this.panelEssence.clearParams().setCollapsed(true).setBodyHeight(this.config.panelHeight).setAccordion(this).setParent(this.getBody()).applyParams(config).create();this.panels.insertEnd(new Rack.data.DLLINode(p.getId(),p));this.p_setPanelStateId(p.getId(),p.getStateId());return p;},p_setPanelStateId:function(id,stateId){if(id&&stateId){this.stateIdMap[stateId]=id;}
return this;},p_getPanelId:function(id){return this.stateIdMap[id]||id;},getPanel:function(id){return this.p_getPanelNode(id).data;},p_getPanelNode:function(id){return this.panels.item(this.p_getPanelId(id));},removePanel:function(id){var p=this.panels.remove(this.p_getPanelId(id));if(p){if(p.data===this.active&&!this.config.multi){this.activate(this.panels.firstNode.id);}
p.data.getContainer().remove();}
return this;},expandPanel:function(id,now){var p=this.getPanel(id);if(p){this.expandPanelObject(p,now);}
return this;},expandPanelObject:function(p,now){p.expand(null,now);this.fireEvent('panelexpanded',this,p);return this;},collapsePanel:function(id,now){var p=this.getPanel(id);if(p){this.collapsePanelObject(p,now);}
return this;},collapsePanelObject:function(p,now){p.collapse(null,now);this.fireEvent('panelcollapsed',this,p);return this;},activate:function(id,now){var panel=this.getPanel(id);var e={};var multi=this.config.multi;if(panel===this.active&&!multi){return panel;}
this.fireEvent("beforepanelactivate",this,e,this.active);if(e.cancel!==true){if(this.active&&!multi){this.collapsePanelObject(this.active,now);}
this.active=panel;if(multi&&!this.active.isCollapsed()){this.collapsePanelObject(this.active,now);}else{this.expandPanelObject(this.active,now);}
this.fireEvent('panelactivate',this,this.active);}
return panel;},moveUp:function(id){var p1=this.p_getPanelNode(id),p2=p1&&p1.prev?p1.prev:null;this.p_insertPanelBefore(p1,p2);return this;},moveDown:function(id){var p2=this.p_getPanelNode(id),p1=p2&&p2.next?p2.next:null;this.p_insertPanelBefore(p1,p2);return this;},insertPanelBeginning:function(id){var p1=this.p_getPanelNode(id),p2=this.panels.firstNode;this.p_insertPanelBefore(p1,p2);return this;},insertPanelBefore:function(id1,id2){var p1=this.p_getPanelNode(id1),p2=this.p_getPanelNode(id2);this.p_insertPanelBefore(p1,p2);return this;},p_insertPanelBefore:function(p1,p2){if(p1&&p2&&p1!==p2){p1.data.getContainer().insertBefore(p2.data.getContainer());this.panels.insertBefore(this.panels.remove(p1),p2);var list=this.panels.listOrdered().map(function(e,a,i){return e.data.getId();});this.fireEvent('panelorder',this,list);}
return this;},insertPanelEnd:function(id){var p1=this.p_getPanelNode(id),p2=this.panels.lastNode;if(p1&&p2&&p1!==p2){p1.data.getContainer().insertAfter(p2.data.getContainer());this.panels.insertEnd(this.panels.remove(p1));var list=this.panels.listOrdered().map(function(e,a,i){return e.data.getId();});this.fireEvent('panelorder',this,list);}
return this;},restoreState:function(provider){var sm=new Rack.widget.AccordionStateManager(),stateId=this.config.stateId||this.getContainer().id;if(!provider){provider=Ext.state.Manager;}
sm.init(this,stateId,provider);return this;},restorePanelState:function(pid,state){var p=this.getPanel(pid);if(!p.getAlwaysExpanded()&&!p.getAlwaysCollapsed()){if(this.config.multi){if(state.exp){this.expandPanel(pid,true);}else{this.collapsePanel(pid,true);}}else{if(state.exp){p.activate();}}}},destroy:function(removeEl){this.purgeListeners();YAHOO.util.Event.purgeElement(this.container.dom,true);var p;while(p=this.panels.remove(this.panels.firstNode)){if(p.data&&p.data.destroy){p.data.destroy(true);p.data=null;}}
this.container.remove();}});