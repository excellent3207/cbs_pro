var App = function () {

  var config = {//Basic Config
    tooltip: false,
    popover: true,
    nanoScroller: false,
    nestableLists: false,
    hiddenElements: false,
    bootstrapSwitch:false,
    dateTime:false,
    select2:false,
    tags:false,
    slider:false
  }; 
      function toggleSideBar(_this){
        var b = $("#sidebar-collapse")[0];
        var w = $("#cl-wrapper");
        var s = $(".cl-sidebar");
        
        if(w.hasClass("sb-collapsed")){
          $(".fa",b).addClass("fa-angle-left").removeClass("fa-angle-right");
          w.removeClass("sb-collapsed");
        }else{
          $(".fa",b).removeClass("fa-angle-left").addClass("fa-angle-right");
          w.addClass("sb-collapsed");
        }
        //updateHeight();
      }
      
      function updateHeight(){
        if(!$("#cl-wrapper").hasClass("fixed-menu")){
          var button = $("#cl-wrapper .collapse-button").outerHeight();
          var navH = $("#head-nav").height();
          //var document = $(document).height();
          var cont = $("#pcont").height();
          var sidebar = ($(window).width() > 755 && $(window).width() < 963)?0:$("#cl-wrapper .menu-space .content").height();
          var windowH = $(window).height();
          
          if(sidebar < windowH && cont < windowH){
            if(($(window).width() > 755 && $(window).width() < 963)){
              var height = windowH;
            }else{
              var height = windowH - button - navH;
            }
          }else if((sidebar < cont && sidebar > windowH) || (sidebar < windowH && sidebar < cont)){
            var height = cont + button + navH;
          }else if(sidebar > windowH && sidebar > cont){
            var height = sidebar + button;
          }  
          
          // var height = ($("#pcont").height() < $(window).height())?$(window).height():$(document).height();
          $("#cl-wrapper .menu-space").css("min-height",height);
        }else{
          $("#cl-wrapper .nscroller").nanoScroller({ preventPageScrolling: true });
        }
      }
        
  return {
   
    init: function (options) {
      //Extends basic config with options
      $.extend( config, options );
      
      /*VERTICAL MENU*/
      $(".cl-vnavigation li ul").each(function(){
        $(this).parent().addClass("parent");
      });
      
      $(".cl-vnavigation li ul li.active").each(function(){
        $(this).parent().show().parent().addClass("open");
        //setTimeout(function(){updateHeight();},200);
      });
      
      $(".cl-vnavigation").delegate(".parent > a","click",function(e){
        $(".cl-vnavigation .parent.open > ul").not($(this).parent().find("ul")).slideUp(300, 'swing',function(){
           $(this).parent().removeClass("open");
        });
        
        var ul = $(this).parent().find("ul");
        ul.slideToggle(300, 'swing', function () {
          var p = $(this).parent();
          if(p.hasClass("open")){
            p.removeClass("open");
          }else{
            p.addClass("open");
          }
          //var menuH = $("#cl-wrapper .menu-space .content").height();
          // var height = ($(document).height() < $(window).height())?$(window).height():menuH;
          //updateHeight();
         $("#cl-wrapper .nscroller").nanoScroller({ preventPageScrolling: true });
        });
        e.preventDefault();
      });
      
      /*Small devices toggle*/
      $(".cl-toggle").click(function(e){
        var ul = $(".cl-vnavigation");
        ul.slideToggle(300, 'swing', function () {
        });
        e.preventDefault();
      });
      
      /*Collapse sidebar*/
      $("#sidebar-collapse").click(function(){
          toggleSideBar();
      });
      
      
      if($("#cl-wrapper").hasClass("fixed-menu")){
        var scroll =  $("#cl-wrapper .menu-space");
        scroll.addClass("nano nscroller");
 
        function update_height(){
          var button = $("#cl-wrapper .collapse-button");
          var collapseH = button.outerHeight();
          var navH = $("#head-nav").height();
          var height = $(window).height() - ((button.is(":visible"))?collapseH:0) - navH;
          scroll.css("height",height);
          $("#cl-wrapper .nscroller").nanoScroller({ preventPageScrolling: true });
        }
        
        $(window).resize(function() {
          update_height();
        });    
            
        update_height();
        $("#cl-wrapper .nscroller").nanoScroller({ preventPageScrolling: true });
        
      }else{
        $(window).resize(function(){
          //updateHeight();
        }); 
        //updateHeight();
      }

      
      /*SubMenu hover */
        var tool = $("<div id='sub-menu-nav' style='position:fixed;z-index:9999;'></div>");
        
        function showMenu(_this, e){
          if(($("#cl-wrapper").hasClass("sb-collapsed") || ($(window).width() > 755 && $(window).width() < 963)) && $("ul",_this).length > 0){   
            $(_this).removeClass("ocult");
            var menu = $("ul",_this);
            if(!$(".dropdown-header",_this).length){
              var head = '<li class="dropdown-header">' +  $(_this).children().html()  + "</li>" ;
              menu.prepend(head);
            }
            
            tool.appendTo("body");
            var top = ($(_this).offset().top + 8) - $(window).scrollTop();
            var left = $(_this).width();
            
            tool.css({
              'top': top,
              'left': left + 8
            });
            tool.html('<ul class="sub-menu">' + menu.html() + '</ul>');
            tool.show();
            
            menu.css('top', top);
          }else{
            tool.hide();
          }
        }

        $(".cl-vnavigation li").hover(function(e){
          showMenu(this, e);
        },function(e){
          tool.removeClass("over");
          setTimeout(function(){
            if(!tool.hasClass("over") && !$(".cl-vnavigation li:hover").length > 0){
              tool.hide();
            }
          },500);
        });
        
        tool.hover(function(e){
          $(this).addClass("over");
        },function(){
          $(this).removeClass("over");
          tool.fadeOut("fast");
        });
        
        
        $(document).click(function(){
          tool.hide();
        });
        $(document).on('touchstart click', function(e){
          tool.fadeOut("fast");
        });
        
        tool.click(function(e){
          e.stopPropagation();
        });
     
        $(".cl-vnavigation li").click(function(e){
          if((($("#cl-wrapper").hasClass("sb-collapsed") || ($(window).width() > 755 && $(window).width() < 963)) && $("ul",this).length > 0) && !($(window).width() < 755)){
            showMenu(this, e);
            e.stopPropagation();
          }
        });
        
        $(".cl-vnavigation li").on('touchstart click', function(){
          //alert($(window).width());
        });
        
      $(window).resize(function(){
        //updateHeight();
      });

      var domh = $("#pcont").height();
      $(document).bind('DOMSubtreeModified', function(){
        var h = $("#pcont").height();
        if(domh != h) {
          //updateHeight();
        }
      });
      
      /*Return to top*/
      var offset = 220;
      var duration = 500;
      var button = $('<a href="#" class="back-to-top"><i class="fa fa-angle-up"></i></a>');
      button.appendTo("body");
      
      jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.back-to-top').fadeIn(duration);
        } else {
            jQuery('.back-to-top').fadeOut(duration);
        }
      });
    
      jQuery('.back-to-top').click(function(event) {
          event.preventDefault();
          jQuery('html, body').animate({scrollTop: 0}, duration);
          return false;
      });
      
       /*Slider*/
      if(config.slider){
        $('.bslider').slider();     
      }
      
      /*Input & Radio Buttons*/
      if(jQuery().iCheck){
        $('.icheck').iCheck({
          checkboxClass: 'icheckbox_square-blue checkbox',
          radioClass: 'iradio_square-blue'
        });
      }
      
      /*Bind plugins on hidden elements*/
      if(config.hiddenElements){
      	/*Dropdown shown event*/
        $('.dropdown').on('shown.bs.dropdown', function () {
          $(".nscroller").nanoScroller();
        });
          
        /*Tabs refresh hidden elements*/
        $('.nav-tabs').on('shown.bs.tab', function (e) {
          $(".nscroller").nanoScroller();
        });
      }
      /*Popover*/
      if(config.popover){
        $('[data-popover="popover"]').popover();
      }
      
      /*DateTime Picker*/
      if(config.dateTime){
        $(".datetime").datetimepicker({autoclose: true});
      }
    }
  };

}();

$(function(){
	//$("body").animate({opacity:1,'margin-left':0},500);
	$("body").css({opacity:1,'margin-left':0});
	$('body').append('<div class="modal fade" id="mod-common" tabindex="-1" role="dialog"><div class="modal-dialog">'+
		'<div class="modal-content"><div class="modal-header">'+
		'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
		'</div><div class="modal-body"><div class="text-center"></div></div><div class="modal-footer"></div></div></div></div>');
	$('body').append('<div class="md-modal colored-header success md-effect-10" id="mod-table-select" style="display:none"><div class="md-content">' +
			      '<div class="modal-header">' +
			        '<h3></h3>' +
			        '<button type="button" class="close md-close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
			      '</div>' +
			      '<div class="modal-body">' +
			        '<div class="text-center">' +
			          
			        '</div>' +
			      '</div>' +
			      '<div class="modal-footer clearFix">' +
			      	'<div class="btn-syh-cancel-wrap pull-left"></div>' + 
			        '<button type="button" class="btn btn-default btn-flat md-close" data-dismiss="modal">取消</button>' +
			        '<button type="button" class="btn btn-primary btn-flat md-close md-process">确定</button>' +
			      '</div>' +
			    '</div>' +
			'</div>' +
			'<div class="md-overlay"></div>');
});
function syhWarn(msg, callback){
	$('#mod-common .modal-body .text-center')
		.html('<div class="i-circle warning"><i class="fa fa-warning"></i></div><p>'+msg+'</p>');
	$('#mod-common .modal-footer')
		.html('<button type="button" class="btn btn-default warn-close" data-dismiss="modal">关闭</button>');
	$('.warn-close')[0].onclick = function(){
		if(callback) callback();
	}
	$('#mod-common').modal('show');
}
function syhConfirm(msg, callback){
	$('#mod-common .modal-body .text-center')
		.html('<div class="i-circle warning"><i class="fa fa-info"></i></div><p>'+msg+'</p>');
	$('#mod-common .modal-footer')
		.html('<button type="button" class="btn btn-default confirm-cancel" data-dismiss="modal">取消</button>'+
				'<button type="button" class="btn btn-default confirm-ok" data-dismiss="modal">确定</button>');
	$('.confirm-ok')[0].onclick = function(){
		callback(true);
	}
	$('.confirm-cancel')[0].onclick = function(){
		callback(false);
	}
	$('#mod-common').modal('show');
}
function syhError(msg, callback){
	$('#mod-common .modal-body .text-center')
		.html('<div class="i-circle danger"><i class="fa fa-times"></i></div><p>'+msg+'</p>');
	$('#mod-common .modal-footer')
		.html('<button type="button" class="btn btn-default error-close" data-dismiss="modal">关闭</button>');
	$('.error-close')[0].onclick = function(){
		if(callback) callback();
	}
	$('#mod-common').modal('show');
}
function syhSuccess(msg, callback){
	$('#mod-common .modal-body .text-center')
		.html('<div class="i-circle success"><i class="fa fa-check"></i></div><p>'+msg+'</p>');
	$('#mod-common .modal-footer')
		.html('<button type="button" class="btn btn-default success-close" data-dismiss="modal">关闭</button>');
	$('.success-close')[0].onclick = function(){
		if(callback) callback();
	}
	$('#mod-common').modal('show');
}
function syhInfo(title, content, callback){
	$('#mod-table-select h3').html(title);
	$('#mod-table-select .modal-body .text-center').html(content);
	$('#mod-table-select .md-process')[0].callback = callback;
	$('#mod-table-select .md-process')[0].onclick = function(){
		if(this.callback)
			this.callback($('#mod-table-select'));
	};
	$('#mod-table-select .modal-footer .btn-syh-cancel-wrap').html('');
	$('#mod-table-select').niftyModal('show');
}
function modalHidden(callback){
	$('#mod-common').on('hidden.bs.modal', function (e) {
		callback();
		$('#mod-common').off('hidden.bs.modal');
	});
}
var tableSelected = {};
//单选 title 标题  data 列表数据 headdData 列表头数据 fields 列表属性[[属性名称,属性宽度],..]  callback 点击确定时的回调函数
function modalTableRadio(title, data, headData, fields, callback, pagination, pageClickCallback, tableSearch, searchClick){
	modalTableSelect(1, title, data, headData, fields, callback, pagination, pageClickCallback, tableSearch, searchClick);
}
//多选
function modalTableCheckBox(title, data, headData, fields, callback, pagination, pageClickCallback, tableSearch, searchClick){
	modalTableSelect(2, title, data, headData, fields, callback, pagination, pageClickCallback, tableSearch, searchClick);
}
var syhGlobal = {};
//列表选择模态框  type=1单选 2多选
function modalTableSelect(type ,title, data, headData, fields, callback, pagination, pageClickCallback, tableSearch, searchClick){
	$('#mod-table-select')[0].fields = fields;
	$('#mod-table-select')[0].selectType = type;
	$('#mod-table-select')[0].pageClickCallback = pageClickCallback;
	$('#mod-table-select h3').html(title+'（双击选择该行）');
	var ths = '';
	for(var i = 0; i < headData.length; i++){
		var width = '';
		if(fields[i][1]){
			width = 'width="'+fields[i][1]+'"';
		}
		ths += '<th '+width+'>'+headData[i]+'</th>'
	}
	var trs = '';
	for(var i = 0; i < data.length; i++){
		datas = ' ';
		for( var n = 0; n < fields.length; n++){
			var str = 'id';
			if(n > 0){
				str = fields[n][0];
			}
			datas += 'data-' + str + '="'+data[i][fields[n][0]]+'"'; 
		}
		trs += '<tr '+datas+' data-type="'+type+'">';
		for(var j = 0; j < fields.length; j++){
			trs += '<td>'+data[i][fields[j][0]]+'</td>'
		}
		trs += '</tr>';
	}
	var searchBtn = '';
	if(tableSearch) searchBtn = '<div<a class="btn btn-info search-btn">搜索</a>';
	$('#mod-table-select .modal-body .text-center').html('<div class="table-search" style="padding-bottom:15px">'+tableSearch+searchBtn+'</div><table class="table table-bordered table-hover" id="table-select">' +
			'<thead>'+ths+'</thead><tbody>'+trs+'</tbody></table><div class="table-page">'+pagination+'</div>');
	createSelected();
	if(tableSearch){
		var sBtn = $('#mod-table-select .modal-body .text-center .table-search .search-btn');
		sBtn[0].searchClick = searchClick;
		sBtn[0].onclick = function(){
			if(this.searchClick) this.searchClick();
		}
	}
	var aObj = $('#mod-table-select .modal-body .text-center .pagination a');
	for(var i = 0; i < aObj.length; i++){
		aObj[i].pageClickCallback = pageClickCallback;
		aObj[i].selectType = type;
		aObj[i].onclick = function(){
			var page = $(this).data('page');
			if(this.pageClickCallback){
				this.pageClickCallback(page);
			}
		}
	}
	var trObj = $('#mod-table-select table tbody tr');
	trObj.dblclick(function(){
		var datas = $(this).data();
		var id = $(this).data('id');
		var type = $(this).data('type');
		delete datas.type;
		if(type == 1){
			tableSelected = {};
		}
		tableSelected[id] = datas;
		createSelected();
	});
	$('#mod-table-select .md-process')[0].callback = callback;
	$('#mod-table-select .md-process')[0].onclick = function(){
		if(this.callback)
			this.callback(tableSelected);
	};
	$('#mod-table-select').niftyModal('show');
}
function updateSelectModalTableRadio(data, fields, pagination, pageClickCallback){
	updateSelectModalTable(1, data, fields, pagination, pageClickCallback);
}
function updateSelectModalTableCheckBox(data, fields, pagination, pageClickCallback){
	updateSelectModalTable(2, data, fields, pagination, pageClickCallback);
}
function updateSelectModalTable(data, pagination){
	var fields = $('#mod-table-select')[0].fields;
	var type = $('#mod-table-select')[0].selectType;
	var trs = '';
	for(var i = 0; i < data.length; i++){
		datas = ' ';
		for( var n = 0; n < fields.length; n++){
			var str = 'id';
			if(n > 0){
				str = fields[n][0];
			}
			datas += 'data-' + str + '="'+data[i][fields[n][0]]+'"'; 
		}
		trs += '<tr '+datas+' data-type="'+type+'">';
		for(var j = 0; j < fields.length; j++){
			trs += '<td>'+data[i][fields[j][0]]+'</td>'
		}
		trs += '</tr>';
	}
	$('#mod-table-select .modal-body .text-center table tbody').html(trs);
	$('#mod-table-select .modal-body .text-center .table-page').html(pagination);
	var aObj = $('#mod-table-select .modal-body .text-center .pagination a');
	for(var i = 0; i < aObj.length; i++){
		aObj[i].onclick = function(){
			var page = $(this).data('page');
			var pageClickCallback = $('#mod-table-select')[0].pageClickCallback;
			if(pageClickCallback){
				pageClickCallback(page);
			}
		}
	}
	var trObj = $('#mod-table-select table tbody tr');
	trObj.dblclick(function(){
		var datas = $(this).data();
		var id = $(this).data('id');
		var type = $(this).data('type');
		delete datas.type;
		if(type == 1){
			tableSelected = {};
		}
		tableSelected[id] = datas;
		createSelected();
	});
}
function createSelected(){
	var wrapObj = $('#mod-table-select .modal-footer .btn-syh-cancel-wrap');
	var selectHtml = '';
	for(var id in tableSelected){
		if(!id) continue;
		var dArr = [];
		for(var k in tableSelected[id]){
			dArr.push(tableSelected[id][k]);
		}
		dArr.reverse();
		selectHtml += '<a data-popover="popover" data-original-title="详细信息" data-content="'+dArr.join(',')+
			'" data-placement="top" data-trigger="hover"  data-id="'+id+
			'" class="btn btn-info btn-syh-cancel">'+id+'<i>&times;</i></a>'
	}
	wrapObj.html(selectHtml);
	$('#mod-table-select .btn-syh-cancel-wrap a i').on('click', function(){
		$(this).parent().popover('hide');
		$(this).parent().remove();
		var id = $(this).parent().data('id');
		delete tableSelected[id];
	});
	$('#mod-table-select .btn-syh-cancel-wrap a').popover();
}
function getFormData(ele){
	var d = {};
	var data = $(ele).serializeArray();
	$.each(data, function() {
	  	d[this.name] = this.value;
	  });
	return d;
}
function updateSelectTag(selected, tagWrap, inputObj){
	var ids = [];
	var aHtmls = '';
	for(var k in selected){
		ids.push(k);
		var dArr = [];
		for(var sk in selected[k]){
			dArr.push(selected[k][sk]);
		}
		dArr.reverse();
		aHtmls += '<a data-popover="popover" data-original-title="详细信息" data-content="'+dArr.join(',')+
		'" data-placement="top" data-trigger="hover"  data-id="'+k+
		'" class="btn btn-info btn-syh-cancel">'+k+'<i>&times;</i></a>';
	}
	tagWrap.html(aHtmls);
	tagWrap.find('a i').on('click', function(){
		$(this).parent().popover('hide');
		$(this).parent().remove();
		var id = $(this).parent().data('id');
		delete tableSelected[id];
		var ids = [];
		for(var k in tableSelected){
			ids.push(k);
		}
		inputObj.val(ids.join(','));
	});
	tagWrap.find('a').popover();
	inputObj.val(ids.join(','));
}