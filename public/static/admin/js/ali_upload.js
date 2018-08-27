function uploadBase(file, barEle, showEle, dataName){
	$(barEle).parent().parent().show();
    var name = file.name;
    var index1 = name.lastIndexOf('.');
    var index2 = name.length;
    var ext = name.substring(index1, index2);
	$.get('/admin/media/imgsts', function(res){
		if(res.errorcode == 0){
			sts = res.data;
		    var filename = sts.dir + ext;
			var client = new OSS.Wrapper({
		     	region: sts.region,
		        accessKeyId: sts.AccessKeyId,
		        accessKeySecret: sts.AccessKeySecret,
		        stsToken: sts.SecurityToken,
		        bucket: sts.bucket
		    });
		    return client.multipartUpload(filename, file, {
			      progress: function(p){
			    	  return function(done){
			    		  var bar = $(barEle);
			    		  bar.css({width:Math.floor(p * 100) + '%'});
			    		  bar.html(Math.floor(p * 100) + '%');
			    		  if(p == 1){
			    			  bar.parent().parent().hide();
			    		  }
			    		  done();
			    	  }
			      }
			    }).then(function (res) {
			      var url = sts.domain + '/' + res.name;
			      $(showEle).html('<img src="'+url+'" alt="" width="100%" />');
			      var dataEle = 'input[name="'+dataName+'"]';
			      $(dataEle).val(res.name);
				  parsleyRemoveError($(dataEle));
			    });
		}else{
			alert(res.msg);
		}
	}, 'json');
}
function uploadPicOrder(file, conEle, dataEle, callback){
	$(conEle).find('.progress').show();
    var name = file.name;
    var index1 = name.lastIndexOf('.');
    var index2 = name.length;
    var ext = name.substring(index1, index2);
	$.get('/admin/media/imgsts', function(res){
		if(res.errorcode == 0){
			sts = res.data;
		    var filename = sts.dir + ext;
			var client = new OSS({
		     	region: sts.region,
		        accessKeyId: sts.AccessKeyId,
		        accessKeySecret: sts.AccessKeySecret,
		        stsToken: sts.SecurityToken,
		        bucket: sts.bucket
		    });
		    return client.multipartUpload(filename, file, {
			      progress: function(p){
			    	  return function(done){
			    		  var bar = $(conEle).find('.progress-bar');
			    		  bar.css({width:Math.floor(p * 100) + '%'});
			    		  bar.html(Math.floor(p * 100) + '%');
			    		  if(p == 1){
			    			  $(conEle).find('.progress').hide();
			    		  }
			    		  done();
			    	  }
			      }
			    }).then(function (res) {
			      var url = sts.domain + '/' + res.name;
			      $(conEle).find('img').attr({'data-uri': res.name, src:url});
			      $(conEle).find('img').show();
			      parsleyRemoveError($(dataEle));
			      callback(conEle);
			    });
		}else{
			alert(res.msg);
		}
	}, 'json');
}
function uploadFile(file, barEle, showEle, dataName){
	$(barEle).parent().parent().show();
    var name = file.name;
    var index1 = name.lastIndexOf('.');
    var index2 = name.length;
    var ext = name.substring(index1, index2);
	$.get('/admin/media/filests', function(res){
		if(res.errorcode == 0){
			sts = res.data;
		    var filename = sts.dir + ext;
			var client = new OSS.Wrapper({
		     	region: sts.region,
		        accessKeyId: sts.AccessKeyId,
		        accessKeySecret: sts.AccessKeySecret,
		        stsToken: sts.SecurityToken,
		        bucket: sts.bucket
		    });
		    return client.multipartUpload(filename, file, {
			      progress: function(p){
			    	  return function(done){
			    		  var bar = $(barEle);
			    		  bar.css({width:Math.floor(p * 100) + '%'});
			    		  bar.html(Math.floor(p * 100) + '%');
			    		  if(p == 1){
			    			  bar.parent().parent().hide();
			    		  }
			    		  done();
			    	  }
			      }
			    }).then(function (res) {
			      var url = sts.domain + '/' + res.name;
			      $(showEle).html(url);
			      var dataEle = 'input[name="'+dataName+'"]';
			      $(dataEle).val(res.name);
				  parsleyRemoveError($(dataEle));
			    });
		}else{
			alert(res.msg);
		}
	}, 'json');
}