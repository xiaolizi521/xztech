<?xml version="1.0" encoding="utf-8"?>
<project path="" name="Rackspace Extensions" author="Steve Kollars" version="1.0" copyright="$projectName&#xD;&#xA;Copyright(c) 2007 Rackspace Managed Hosting&#xD;&#xA;Author: $author&#xD;&#xA;Version: $version&#xD;&#xA;&#xD;&#xA;This library is free software; you can redistribute it and/or&#xD;&#xA;modify it under the terms of the GNU Lesser General Public&#xD;&#xA;License as published by the Free Software Foundation; either&#xD;&#xA;version 2.1 of the License, or (at your option) any later version.&#xD;&#xA;&#xD;&#xA;This library is distributed in the hope that it will be useful,&#xD;&#xA;but WITHOUT ANY WARRANTY; without even the implied warranty of&#xD;&#xA;MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU&#xD;&#xA;Lesser General Public License for more details.&#xD;&#xA;&#xD;&#xA;You should have received a copy of the GNU Lesser General Public&#xD;&#xA;License along with this library; if not, write to the Free Software&#xD;&#xA;Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA" output="$project\rui" source="False" source-dir="$output\source" minify="true" min-dir="$output\build" doc="False" doc-dir="$output\docs" master="true" master-file="$output\yui-ext.js" zip="true" zip-file="$output\yuo-ext.$version.zip">
  <directory name="rui" />
  <file name="rui\source\widgets\Accordion.js" path="source\widgets" />
  <target name="Rack All" file="$output\rack-all.js" debug="True" shorthand="False" shorthand-list="YAHOO.util.Dom.setStyle&#xD;&#xA;YAHOO.util.Dom.getStyle&#xD;&#xA;YAHOO.util.Dom.getRegion&#xD;&#xA;YAHOO.util.Dom.getViewportHeight&#xD;&#xA;YAHOO.util.Dom.getViewportWidth&#xD;&#xA;YAHOO.util.Dom.get&#xD;&#xA;YAHOO.util.Dom.getXY&#xD;&#xA;YAHOO.util.Dom.setXY&#xD;&#xA;YAHOO.util.CustomEvent&#xD;&#xA;YAHOO.util.Event.addListener&#xD;&#xA;YAHOO.util.Event.getEvent&#xD;&#xA;YAHOO.util.Event.getTarget&#xD;&#xA;YAHOO.util.Event.preventDefault&#xD;&#xA;YAHOO.util.Event.stopEvent&#xD;&#xA;YAHOO.util.Event.stopPropagation&#xD;&#xA;YAHOO.util.Event.stopEvent&#xD;&#xA;YAHOO.util.Anim&#xD;&#xA;YAHOO.util.Motion&#xD;&#xA;YAHOO.util.Connect.asyncRequest&#xD;&#xA;YAHOO.util.Connect.setForm&#xD;&#xA;YAHOO.util.Dom&#xD;&#xA;YAHOO.util.Event">
    <include name="rui\source\rack-core.js" />
    <include name="rui\source\ext-additions.js" />
    <include name="rui\source\util\util-core.js" />
    <include name="rui\source\util\Essence.js" />
    <include name="rui\source\util\BehavioralEssence.js" />
    <include name="rui\source\data\DLL.js" />
    <include name="rui\source\data\DLLI.js" />
    <include name="rui\source\behaviors\ExpandCollapse.js" />
    <include name="rui\source\behaviors\ShowHide.js" />
    <include name="rui\source\behaviors\Remote.js" />
    <include name="rui\source\widgets\Accordion\AccordionPanelRemote.js" />
    <include name="rui\source\widgets\DumbComboBox.js" />
    <include name="rui\source\widgets\Themes.js" />
    <include name="rui\source\widgets\Builder.js" />
    <include name="rui\source\widgets\Titlebar.js" />
    <include name="rui\source\widgets\Panel.js" />
    <include name="rui\source\widgets\Accordion\AccordionPanel.js" />
    <include name="rui\source\widgets\Accordion\AccordionPanelEssence.js" />
    <include name="rui\source\widgets\Accordion\AccordionPanelDropTarget.js" />
    <include name="rui\source\widgets\Accordion\AccordionPanelDragSource.js" />
    <include name="rui\source\widgets\Accordion\AccordionPanelStatusProxy.js" />
    <include name="rui\source\widgets\Accordion\Accordion.js" />
    <include name="rui\source\widgets\Accordion\AccordionEssence.js" />
    <include name="rui\source\widgets\Accordion\AccordionStateManager.js" />
  </target>
  <file name="rui\source\widgets\Accordion\AccordionPanel.js" path="source\widgets\Accordion" />
  <file name="rui\source\widgets\Accordion\AccordionPanelEssence.js" path="source\widgets\Accordion" />
  <file name="rui\source\widgets\Accordion\AccordionEssence.js" path="source\widgets\Accordion" />
  <file name="rui\source\widgets\Accordion\AccordionPanelRemote.js" path="source\widgets\Accordion" />
  <file name="rui\source\widgets\Accordion\AccordionPanelDragSource.js" path="source\widgets\Accordion" />
  <file name="rui\source\widgets\Accordion\AccordionPanelStatusProxy.js" path="source\widgets\Accordion" />
  <file name="rui\source\widgets\Accordion\AccordionStateManager.js" path="source\widgets\Accordion" />
  <file name="rui\source\widgets\Accordion\Accordion.js" path="source\widgets\Accordion" />
  <file name="rui\source\widgets\Accordion\AccordionPanelDropTarget.js" path="source\widgets\Accordion" />
  <file name="rui\source\widgets\Themes.js" path="source\widgets" />
  <file name="rui\source\widgets\Panel.js" path="source\widgets" />
  <file name="rui\source\widgets\Builder.js" path="source\widgets" />
  <file name="rui\source\widgets\DumbComboBox.js" path="source\widgets" />
  <file name="rui\source\widgets\Titlebar.js" path="source\widgets" />
  <file name="rui\source\behaviors\Remote.js" path="source\behaviors" />
  <file name="rui\source\behaviors\ShowHide.js" path="source\behaviors" />
  <file name="rui\source\behaviors\ExpandCollapse.js" path="source\behaviors" />
  <file name="rui\source\util\Essence.js" path="source\util" />
  <file name="rui\source\util\BehavioralEssence.js" path="source\util" />
  <file name="rui\source\util\util-core.js" path="source\util" />
  <file name="rui\source\data\DLL.js" path="source\data" />
  <file name="rui\source\data\DLLI.js" path="source\data" />
  <file name="rui\source\rack-core.js" path="source" />
  <file name="rui\source\ext-additions.js" path="source" />
</project>