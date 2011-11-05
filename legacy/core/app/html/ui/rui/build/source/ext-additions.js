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


Ext.menu.Menu.prototype.load=function(url,params){var loader=new Ext.menu.Item({text:'Loading...'});this.addItem(loader);Ext.lib.Ajax.request((params)?'POST':'GET',url,{success:function(o){this.remove(loader);Ext.util.JSON.decode(o.responseText).menu.forEach(function(e,i,a){this.add(e);}.createDelegate(this));}.createDelegate(this),failure:function(o){this.remove(loader);this.add({text:'Failed to load menu items'});}.createDelegate(this)},params);};Ext.grid.TableGrid=function(table,config){config=config||{};var cf=config.fields||[],ch=config.columns||[];table=Ext.get(table);var ct=table.insertSibling();var fields=[],cols=[];var headers=table.query('thead[@class="grid-headers"]/tr/th');for(var i=0,h;h=headers[i];i++){var text=h.innerHTML;var name='tcol-'+i;fields.push(Ext.applyIf(cf[i]||{},{name:name,mapping:'td:nth('+(i+1)+')/@innerHTML'}));cols.push(Ext.applyIf(ch[i]||{},{'header':text,'dataIndex':name,'width':h.offsetWidth,'tooltip':h.title,'sortable':true}));}
var ds=new Ext.data.Store({reader:new Ext.data.XmlReader({record:'tbody[@class="grid-records"]/tr'},fields)});ds.loadData(table.dom);var cm=new Ext.grid.ColumnModel(cols);if(config.width||config.height){ct.setSize(config.width||'auto',config.height||'auto');}
if(config.remove!==false){table.remove();}
Ext.grid.TableGrid.superclass.constructor.call(this,ct,Ext.applyIf(config,{'ds':ds,'cm':cm,'sm':new Ext.grid.RowSelectionModel(),autoHeight:true,autoWidth:true}));};Ext.extend(Ext.grid.TableGrid,Ext.grid.Grid);