!function(i){var o={};function a(e){if(o[e])return o[e].exports;var t=o[e]={i:e,l:!1,exports:{}};return i[e].call(t.exports,t,t.exports,a),t.l=!0,t.exports}a.m=i,a.c=o,a.d=function(e,t,i){a.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},a.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},a.t=function(t,e){if(1&e&&(t=a(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var i=Object.create(null);if(a.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)a.d(i,o,function(e){return t[e]}.bind(null,o));return i},a.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return a.d(t,"a",t),t},a.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},a.p="/asset//",a(a.s=346)}({346:function(module,exports,__webpack_require__){!function($){var GridManager=function(opt){var option=$.extend({mode:"default",id:"",canBatchSelect:!1,batchSelectInOrder:!1,canSingleSelectItem:!1,canMultiSelectItem:!1,title:null,titleAdd:null,pageTitleAdd:null,titleEdit:null,pageTitleEdit:null,titleShow:null,pageTitleShow:null,ttileImport:null,canAdd:!1,canEdit:!1,canDelete:!1,canShow:!1,canBatchDelete:!1,canSort:!1,canExport:!1,canImport:!1,urlGrid:null,urlAdd:null,urlEdit:null,urlDelete:null,urlShow:null,urlExport:null,urlImport:null,urlSort:null,batchOperatePrepend:"",gridToolbar:"",defaultPageSize:10,gridBeforeRequestScript:null,pageSizes:[],addDialogSize:["90%","90%"],editDialogSize:["90%","90%"],showDialogSize:["90%","90%"],importDialogSize:["90%","90%"],pageJumpEnable:!1,gridRowCols:null,lang:{loading:"Loading",noRecords:"No Records",add:"Add",edit:"Edit",show:"Show",import:"Import",confirmDelete:"Confirm Delete ?",pleaseSelectRecords:"Please Select Records",confirmDeleteRecords:"Confirm Delete %d records ?"}},opt),$grid=$("#"+option.id),recordIdsChecked=[],listerData={page:1,pageSize:1,records:[],total:1,head:[]},processArea=function(e){return/^(\d+)px$/.test(e[0])&&(e[0]=Math.min($(window).width(),parseInt(e[0]))+"px"),/^(\d+)px$/.test(e[1])&&(e[1]=Math.min($(window).height(),parseInt(e[1]))+"px"),e},getId=function(e){e=parseInt($(e).closest("[data-index]").attr("data-index"));return listerData.records[e]._id},getCheckedIds=function(){for(var e=layui.table.checkStatus(option.id+"Table").data,t=[],i=0;i<e.length;i++)t.push(e[i]._id);return option.batchSelectInOrder&&t.sort(function(e,t){return recordIdsChecked.indexOf(e)-recordIdsChecked.indexOf(t)}),t},getCheckedItems=function(){var i=[];"simple"==option.mode?$grid.find("[data-index].checked").each(function(e,t){i.push(listerData.records[parseInt($(t).attr("data-index"))])}):i=layui.table.checkStatus(option.id+"Table").data;for(var e=[],t=0;t<i.length;t++)e.push(i[t]);return option.batchSelectInOrder&&e.sort(function(e,t){return recordIdsChecked.indexOf(e._id)-recordIdsChecked.indexOf(t._id)}),e},updateTableCheckedOrder=function(){$grid.find("[data-index]").each(function(e,t){t=$(t).find('[data-field="0"]');t.length&&(t.find("[data-checked-order]").remove(),0<=(e=recordIdsChecked.indexOf(listerData.records[e]._id))&&t.append("<div data-checked-order>"+(e+1)+"</div>"))})};layui.use(["table","laypage"],function(){var $lister=$("#"+option.id),lister,first=!0,renderPaginate=function(){var e=["limit","prev","page","next","count"];option.pageJumpEnable&&e.push("skip"),layui.laypage.render({elem:option.id+"Pager",curr:listerData.page,count:listerData.total,limit:listerData.pageSize,limits:option.pageSizes,layout:e,jump:function(e,t){t||(lister.setPage(e.curr),lister.setPageSize(e.limit),lister.load())}})},emptyHtml,lister,tableOption,table,$listerTable;function doEdit(e){e=getId(e);lister.realtime.dialog.edit=layer.open({type:2,title:option.pageTitleEdit||option.titleEdit||(option.title?option.lang.edit+option.title:option.lang.edit),shadeClose:!1,shade:.5,maxmin:!1,scrollbar:!1,area:processArea(option.editDialogSize),content:lister.realtime.url.edit+(lister.realtime.url.edit&&0<=lister.realtime.url.edit.indexOf("?")?"&":"?")+"_id="+e,success:function(e,t){lister.realtime.dialog.editWindow=$(e).find("iframe").get(0).contentWindow,lister.realtime.dialog.editWindow.__dialogClose=function(){layer.close(lister.realtime.dialog.edit)},lister.realtime.dialog.editWindow.addEventListener("modstart:form.submitted",function(e){0===e.detail.res.code&&layer.close(lister.realtime.dialog.edit)})},end:function(){lister.refresh(),$grid.trigger("modstart:edit.end")}})}function doDelete(e){var t=getId(e);window.api.dialog.confirm(option.lang.confirmDelete,function(){window.api.dialog.loadingOn(),window.api.base.post(lister.realtime.url.delete,{_id:t},function(e){window.api.dialog.loadingOff(),window.api.base.defaultFormCallback(e,{success:function(e){lister.refresh(),$grid.trigger("modstart:delete.end")}})})})}function doShow(e){e=getId(e);lister.realtime.dialog.show=layer.open({type:2,title:option.pageTitleShow||option.titleShow||(option.title?option.lang.show+option.title:option.lang.show),shadeClose:!1,shade:.5,maxmin:!1,scrollbar:!1,area:processArea(option.showDialogSize),content:lister.realtime.url.show+(lister.realtime.url.show&&0<=lister.realtime.url.show.indexOf("?")?"&":"?")+"_id="+e,success:function(e,t){},end:function(){}})}lister="simple"==option.mode?(emptyHtml=$("#"+option.id+"EmptyHtml").html(),new window.api.lister({lister:$lister,search:$lister.find("[data-search]"),table:$lister.find("[data-table]")},{hashUrl:!1,server:window.location.href,param:{pageSize:option.defaultPageSize},render:function(e){if(listerData=e,recordIdsChecked=[],renderPaginate(),e.recordsHtml)$grid.find("[data-table]").html(e.recordsHtml);else{var t=[];if(option.gridRowCols){t.push('<div class="row">');for(var i=0;i<e.records.length;i++)t.push('<div class="col-md-'+option.gridRowCols[0]+" col-"+option.gridRowCols[1]+'" data-index="'+i+'">'+e.records[i].html+"</div>");t.push("</div>")}else for(i=0;i<e.records.length;i++)t.push('<div data-index="'+i+'">'+e.records[i].html+"</div>");$grid.find("[data-table]").html(t.join("")),e.records.length||$grid.find("[data-table]").html(emptyHtml)}},error:function(e){var t=$(emptyHtml);t.find(".text").text(e),$grid.find("[data-table]").html(t[0].outerHTML)}})):(tableOption={id:option.id+"Table",elem:"#"+option.id+"Table",defaultToolbar:option.gridToolbar,page:!1,skin:"line",text:{none:'<div class="ub-text-muted tw-py-10"><i class="iconfont icon-refresh tw-animate-spin tw-inline-block" style="font-size:2rem;"></i><br />'+option.lang.loading+"</div>"},escape:!1,loading:!0,cellMinWidth:100,cols:[[]],data:[],autoColumnWidth:!0,autoScrollTop:!1,autoSort:!1,done:function(){}},option.canMultiSelectItem&&(option.batchOperatePrepend||option.canDelete&&option.canBatchDelete)&&(tableOption.toolbar="#"+option.id+"TableHeadToolbar"),table=layui.table.render(tableOption),layui.table.on("sort("+option.id+"Table)",function(e){null==e.type?lister.setParam("order",[]):lister.setParam("order",[[e.field,e.type]]),lister.setPage(1),lister.load()}),layui.table.on("checkbox("+option.id+"Table)",function(e){if(option.batchSelectInOrder){for(var t=layui.table.checkStatus(option.id+"Table").data,i=[],o=0;o<t.length;o++)i.push(t[o]._id),-1===recordIdsChecked.indexOf(t[o]._id)&&recordIdsChecked.push(t[o]._id);for(o=0;o<recordIdsChecked.length;o++)-1===i.indexOf(recordIdsChecked[o])&&(recordIdsChecked.splice(o,1),o--);updateTableCheckedOrder()}}),$listerTable=$lister.find("[data-table]"),new window.api.lister({lister:$lister,search:$lister.find("[data-search]"),table:$listerTable},{hashUrl:!1,server:option.urlGrid,showLoading:!1,param:{pageSize:option.defaultPageSize},customLoading:function(loading){var offset=$listerTable.offset(),rect=$listerTable[0].getBoundingClientRect(),offsetTop=Math.max(-rect.top,0),windowHeight=$(window).height(),height=windowHeight-Math.max(rect.top,0)-Math.max(windowHeight-rect.bottom,0),top=0<height?offsetTop+height/2+"px":"50%";$lister[0].style.setProperty("--layui-table-loading-top",top),option.gridBeforeRequestScript&&eval(option.gridBeforeRequestScript),first?first=!1:table.loading(loading)},render:function(data){listerData=data,recordIdsChecked=[],option.canSingleSelectItem?data.head.splice(0,0,{type:"radio"}):option.canMultiSelectItem&&data.head.splice(0,0,{type:"checkbox"}),$grid.find("[data-addition]").html(data.addition||""),layui.table.reload(option.id+"Table",{text:{none:'<div class="ub-text-muted tw-py-10"><i class="iconfont icon-empty-box" style="font-size:2rem;"></i><br />'+option.lang.noRecords+"</div>"},cols:[data.head],data:data.records,limit:data.pageSize}),renderPaginate(),data.script&&eval(data.script)},error:function(e){layui.table.reload(option.id+"Table",{text:{none:'<div class="ub-text-muted tw-py-10"><i class="iconfont icon-warning" style="font-size:2rem;"></i><br />'+MS.util.specialchars(e)+"</div>"},cols:[],data:[],limit:0})}})),lister.realtime={url:{add:option.urlAdd,edit:option.urlEdit,delete:option.urlDelete,show:option.urlShow,export:option.urlExport,import:option.urlImport,sort:option.urlSort},dialog:{add:null,addWindow:null,edit:null,editWindow:null,import:null}},option.canAdd&&$grid.on("click","[data-add-button]",function(){var e,t=lister.realtime.url.add;$(this).is("[data-add-copy-button]")&&(e=getId(this),t+=(0<t.indexOf("?")?"&":"?")+"_copyId="+e),lister.realtime.dialog.add=layer.open({type:2,title:option.pageTitleAdd||option.titleAdd||(option.title?option.lang.add+option.title:option.lang.add),shadeClose:!1,shade:.5,maxmin:!1,scrollbar:!1,area:processArea(option.addDialogSize),content:t,success:function(e,t){lister.realtime.dialog.addWindow=$(e).find("iframe").get(0).contentWindow,lister.realtime.dialog.addWindow.__dialogClose=function(){layer.close(lister.realtime.dialog.add)},lister.realtime.dialog.addWindow.addEventListener("modstart:form.submitted",function(e){0===e.detail.res.code&&layer.close(lister.realtime.dialog.add)})},end:function(){lister.refresh(),$grid.trigger("modstart:add.end")}})}),option.canEdit&&($lister.on("click","[data-tab-open][data-refresh-grid-on-close]",function(){window._pageTabManager.runsOnFocus.push(function(){lister.refresh()})}),$lister.find("[data-table]").on("click","[data-edit]",function(){doEdit(this)}),$lister.find("[data-table]").on("click","[data-edit-quick]",function(){var e=$(this).attr("data-edit-quick").split(":"),t=e.shift(),e=e.join(":"),e={_id:getId(this),_action:"itemCellEdit",column:t,value:e};window.api.dialog.loadingOn(),window.api.base.post(lister.realtime.url.edit,e,function(e){window.api.dialog.loadingOff(),window.api.base.defaultFormCallback(e,{success:function(e){}}),lister.refresh(),$grid.trigger("modstart:edit.end")})}),$grid.on("grid-item-cell-change",function(e,t){t={_id:getId(t.ele),_action:"itemCellEdit",column:t.column,value:t.value};window.api.dialog.loadingOn(),window.api.base.post(lister.realtime.url.edit,t,function(e){window.api.dialog.loadingOff(),window.api.base.defaultFormCallback(e,{success:function(e){lister.refresh(),$grid.trigger("modstart:edit.end")}})})})),option.canDelete&&$lister.find("[data-table]").on("click","[data-delete]",function(){doDelete(this)}),option.canShow&&$lister.find("[data-table]").on("click","[data-show]",function(){doShow(this)}),$(document).on("click",".layui-table-tips .layui-layer-content [data-delete], .layui-table-tips .layui-layer-content [data-edit], .layui-table-tips .layui-layer-content [data-show]",function(){var o=$(this),e=$(this).closest(".layui-layer-content"),a=e.offset();a.width=e.width(),a.height=e.height(),a.centerY=a.top+a.height/2;e=$grid.offset();e.width=$grid.width(),e.height=$grid.height(),a.left<e.left||a.left>e.left+e.width||a.top<e.top||a.top>e.top+e.height||$grid.find(".layui-table-main [data-index]").each(function(e,t){var i=$(t),t=i.offset();t.top<a.centerY&&t.top+i.height()>a.centerY&&(o.is("[data-delete]")?doDelete(i):o.is("[data-show]")?doShow(i):o.is("[data-edit]")&&doEdit(i))})}),option.canDelete&&option.canBatchDelete&&$lister.find("[data-table]").on("click","[data-batch-delete]",function(){var e=getCheckedIds();e.length?window.api.dialog.confirm(option.lang.confirmDeleteRecords.replace("%d",e.length),function(){window.api.dialog.loadingOn(),window.api.base.post(lister.realtime.url.delete,{_id:e.join(",")},function(e){window.api.dialog.loadingOff(),window.api.base.defaultFormCallback(e,{success:function(e){lister.refresh(),$grid.trigger("modstart:delete.end")}})})}):window.api.dialog.tipError(option.lang.pleaseSelectRecords)}),option.canSort&&$lister.find("[data-table]").on("click","[data-sort]",function(){var e=getId(this),t=$(this).attr("data-sort");window.api.dialog.loadingOn(),window.api.base.post(lister.realtime.url.sort,{_id:e,direction:t,param:JSON.stringify(lister.getParam())},function(e){window.api.dialog.loadingOff(),window.api.base.defaultFormCallback(e,{success:function(e){lister.refresh(),$grid.trigger("modstart:sort.end")}})})}),option.canExport&&$lister.find("[data-export-button]").on("click",function(){lister.prepareSearch();var e=JSON.stringify(lister.getParam()),e=lister.realtime.url.export+"?_param="+MS.util.urlencode(e);window.open(e,"_blank")}),option.canImport&&$lister.find("[data-import-button]").on("click",function(){lister.realtime.dialog.import=layer.open({type:2,title:option.titleImport||(option.title?option.lang.import+option.title:option.lang.import),shadeClose:!1,shade:.5,maxmin:!1,scrollbar:!1,area:processArea(option.importDialogSize),content:lister.realtime.url.import,success:function(e,t){$grid.trigger("modstart:import.end")},end:function(){}})}),$lister.find("[data-table]").on("click","[data-batch-operate]",function(){var e,t=getCheckedIds(),i=$(this).attr("data-batch-operate");t.length?(e=function(){window.api.dialog.loadingOn(),window.api.base.post(i,{_id:t.join(",")},function(e){window.api.dialog.loadingOff(),window.api.base.defaultFormCallback(e,{success:function(e){lister.refresh(),$grid.trigger("modstart:batch.end")}})})},$(this).attr("data-batch-confirm")?window.api.dialog.confirm($(this).attr("data-batch-confirm").replace("%d",t.length),function(){e()}):e()):window.api.dialog.tipError(option.lang.pleaseSelectRecords)}),$lister.find("[data-table]").on("click","[data-batch-dialog-operate]",function(){var e,t,i,o=getCheckedIds(),a=$(this).attr("data-batch-dialog-operate");o.length?(e={},t=$(this).attr("data-dialog-width"),i=$(this).attr("data-dialog-height"),t&&(e.width=t),i&&(e.height=i),MS.dialog.dialog(a+"?_id="+o.join(","),e)):window.api.dialog.tipError(option.lang.pleaseSelectRecords)}),$lister.data("lister",lister),window.__grids=window.__grids||{instances:{},get:function(e){if("number"==typeof e){var t,i=0;for(t in window.__grids.instances){if(i===e)return window.__grids.instances[t];i++}}return window.__grids.instances[e]}},window.__grids.instances[option.id]={$grid:$grid,$lister:$lister,lister:lister,getCheckedIds:getCheckedIds,getCheckedItems:getCheckedItems,getId:getId}}),(option.canBatchSelect||option.canSingleSelectItem||option.canMultiSelectItem)&&$(function(){setTimeout(function(){window.__dialogFootSubmiting&&window.__dialogFootSubmiting(function(){var e=window.__grids.instances[option.id].getCheckedIds(),t=window.__grids.instances[option.id].getCheckedItems();window.parent.__dialogSelectIds=e,window.parent.__selectorDialogItems=t,parent.layer.closeAll()})},0)})};MS.GridManager=GridManager}.call(this,__webpack_require__(8))},8:function(e,t){e.exports=window.$}});