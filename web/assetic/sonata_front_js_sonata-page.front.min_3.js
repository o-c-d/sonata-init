/**
 *
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * generated on: Tue Aug 04 2015 10:58:59 GMT+0200 (CEST)
 * revision:     0b23b1d8dcabda41d99ba73c4aa33c3b300d4f76
 *
 */
var Sonata=Sonata||{};Sonata.Page={debug:!1,blocks:[],containers:[],data:[],blockSelector:".cms-block",containerSelector:".cms-container",dropPlaceHolderClass:"cms-block-placeholder",dropPlaceHolderSize:100,dropZoneClass:"cms-container-drop-zone",blockHoverClass:"cms-block-hand-over",url:{block_save_position:null,block_edit:null},init:function(t){t=t||[];for(property in t)this[property]=t[property];this.initInterface(),this.initBlocks(),this.initContainers(),this.initBlockData()},initInterface:function(){jQuery("#page-action-enabled-edit").change(jQuery.proxy(this.toggleEditMode,this)),jQuery("#page-action-save-position").click(jQuery.proxy(this.saveBlockLayout,this))},initBlocks:function(){this.blocks=jQuery(this.blockSelector),this.blocks.mouseover(jQuery.proxy(this.handleBlockHover,this)),this.blocks.dblclick(jQuery.proxy(this.handleBlockClick,this))},initContainers:function(){this.containers=jQuery(this.containerSelector),this.containers.sortable({connectWith:this.containerSelector,items:this.blockSelector,placeholder:this.dropPlaceHolderClass,helper:"clone",dropOnEmpty:!0,forcePlaceholderSize:this.dropPlaceHolderSize,opacity:1,cursor:"move",start:jQuery.proxy(this.startContainerSort,this),stop:jQuery.proxy(this.stopContainerSort,this)}).sortable("disable")},initBlockData:function(){this.data=this.buildBlockData()},startContainerSort:function(t,e){this.containers.addClass(this.dropZoneClass),this.containers.append(jQuery('<div class="cms-fake-block">&nbsp;</div>'))},stopContainerSort:function(t,e){this.containers.removeClass(this.dropZoneClass),jQuery("div.cms-fake-block").remove(),this.refreshLayers()},handleBlockClick:function(t){var e=t.currentTarget,o=jQuery(e).attr("data-id");window.open(this.url.block_edit.replace(/BLOCK_ID/,o),"_newtab"),t.preventDefault(),t.stopPropagation()},handleBlockHover:function(t){this.blocks.removeClass(this.blockHoverClass),jQuery(this).addClass(this.blockHoverClass),t.stopPropagation()},toggleEditMode:function(t){t&&t.currentTarget.checked?(jQuery("body").addClass("cms-edit-mode"),jQuery(".cms-container").sortable("enable"),this.buildLayers()):(jQuery("body").removeClass("cms-edit-mode"),jQuery("div.cms-container").sortable("disable"),this.removeLayers()),t.preventDefault(),t.stopPropagation()},buildLayers:function(){this.blocks.each(function(t){var e,o=jQuery(this),i=o.attr("data-role")||"block",a=o.attr("data-name")||"missing data-name",r=(o.attr("data-id")||"missing data-id",[]);r.push("cms-layout-layer"),r.push("cms-layout-role-"+i),e=jQuery('<div class="'+r.join(" ")+'" ></div>'),e.css({position:"absolute",left:0,top:0,width:"100%",height:"100%",zIndex:2}),title=jQuery('<div class="cms-layout-title"></div>'),title.css({position:"absolute",left:0,top:0,zIndex:2}),title.html("<span>"+a+"</span>"),e.append(title),o.prepend(e)})},removeLayers:function(){jQuery(".cms-layout-layer").remove()},refreshLayers:function(){jQuery(".cms-layout-layer").each(function(t){var e=jQuery(this),o=e.parent();e.css("width",o.width()),e.css("height",o.height())})},buildBlockData:function(){var t=[];return this.blocks.each(jQuery.proxy(function(e,o){var i=this.buildSingleBlockData(o);i&&t.push(i)},this)),t.sort(function(t,e){return t.page_id==e.page_id?t.parent_id==e.parent_id?t.position-e.position:t.parent_id-e.parent_id:t.page_id-e.page_id}),t},buildSingleBlockData:function(t){var e,o,i,a,r,n,s;return e=jQuery(t),(o=e.attr("data-id"))?(i=this.findParentContainer(e))?(a=jQuery(i).attr("data-id"),root=this.findRootContainer(e),root?(r=jQuery(root).attr("data-page-id"),n=e.prevAll(this.blockSelector+"[data-id]"),s=n.length+1,o&&a?{id:o,position:s,parent_id:a,page_id:r}:void 0):void this.log("Block "+o+" has no root but has a parent, should never happen!")):void this.log("Block "+o+" has no parent, it must be a root container, ignored"):void this.log("Block has no data-id, ignored !")},buildDiffBlockData:function(t,e){var o=[];return jQuery.map(t,function(t,i){var a;a=jQuery.grep(e,function(e,o){return t.id!=e.id?!1:t.position!=e.position||t.parent_id!=e.parent_id||t.page_id!=e.page_id?!0:void 0}),a&&a[0]&&o.push(a[0])}),o},findParentContainer:function(t){var e,o;return e=jQuery(t).parents(this.containerSelector+"[data-id]"),o=e.get(0)},findRootContainer:function(t){var e,o;return e=jQuery(t).parents(this.containerSelector+"[data-id]"),o=e.get(-1)},saveBlockLayout:function(t){var e;return t.preventDefault(),t.stopPropagation(),e=this.buildDiffBlockData(this.data,this.buildBlockData()),0==e.length?void alert("No changes found."):(jQuery.each(e,jQuery.proxy(function(t,e){this.log("Update block "+e.id+" (Page "+e.page_id+"), parent "+e.parent_id+", at position "+e.position+")")},this)),void jQuery.ajax({type:"POST",url:this.url.block_save_position,data:{disposition:e},dataType:"json",success:jQuery.proxy(function(t,e,o){"ok"==t.result?(alert("Block ordering saved!"),this.initBlockData()):(this.log(t),alert("Server could not save block ordering!"))},this),error:jQuery.proxy(function(t,e,o){this.log("Unable to save block ordering: "+o),this.log(e),this.log(t)},this)}))},log:function(){if(this.debug)try{console.log(arguments)}catch(t){}}};