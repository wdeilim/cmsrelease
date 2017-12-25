define(function(){
	var uploader = {};

	uploader.delbase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACABAMAAAAxEHz4AAAAA3NCSVQICAjb4U/gAAAAMFBMVEX///////////////////////////////////////////////////////////////9Or7hAAAAAEHRSTlMAESIzRFVmd4iZqrvM3e7/dpUBFQAAAAlwSFlzAAABsQAAAbEBYZgoDgAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAH8SURBVGiB7dhLTgJBEAbgnhhBMSZsXMGCo3gDEy8Aazd4A70B3gBXbgdPgJ6A6AU8ggsTRXmUM9320POorq4yxk3/KzJV1fkSmAFKqZiYmJiYmL/LgG5Jup5auh1R83uL9SlaPAb4pA4YAryhxTEA3Pjn97OWFVqdAknIALBFqxOgCDkANmj5DCjC0N9xCARBA+AJb0gJggZsBnhDx08wgGd8niKQAILQogF+wlgDPLcCQWhRbwFFCAJ4CIEAnBAIQAnBAIygAesAAEIwgMeQ+WYCA6DUUZ3AAig1z7uXYoAlXIsBdQITUCOwAVUCG1AhtPmAMuGKDygRRACXIAI4BCFgRxACCoIYYAligFI9KCICqGRu52UAhyAD7AhSQEGQAjLCSz4f8l2A5MAILsUHTMwBH78EyAkTe4CQUACkBA1YzcUE86PxvicmTDWgm0gJPwD7ceQTDEDZO4JNMIBZ/lJGKABCggOQERyAiFACSAglgIBQAfAJGvDlXEgWLIIB3LqX+ixCWgUwCZ06gEdoALAIjQAOoRHAICAASxhJAZbwLgaEElBAIMEDCCN4AEEELyCE4AUEEAgATRj7AZaA75F02bvF6ZefddW8ArVH0gR8j7SgAIaA75GG9C4tJyzRarv0Z7E52QP6Aa+enFPzSl3c0T0xMTExMf+Ub5v8OIz+A+sRAAAAAElFTkSuQmCC';

	/**
	 * @param valobj 输入框
	 * @param btnobj 按钮
	 * @param imgobj 图片显示
	 * @param option 参数
     * @param result 返回
     */
	uploader.image = function(valobj, btnobj, imgobj, option, result) {
		require(['util', 'formfile/jquery.formfile'], function(util){

			valobj.blur(function(){
				if (imgobj) { imgobj.attr("src", util.tomedia(valobj.val())); }
			}).blur();

			if (imgobj) {
				imgobj.error(function(){
					imgobj.attr("src", util.base_uri+'caches/statics/images/nopic.jpg');
				});
				var tempdel = $('<div class="__uploader_formfile_del" style="display:none;position:absolute;background-color:rgba(0, 0, 0, 0.3);background-image:url(\'' + uploader.delbase64 + '\');background-repeat:no-repeat;background-size:100% 100%;"></div>');
				$("body").append(tempdel);
				tempdel.mousemove(function(){
					$("div.__uploader_formfile_del").show();
				}).mouseout(function(){
					$("div.__uploader_formfile_del").hide();
				});
				imgobj.mousemove(function(){
					if (imgobj.attr("src").indexOf("nopic.jpg") != -1) { return false; }
					tempdel.unbind('click').click(function(){
						$("div.__uploader_formfile_del").hide();
						imgobj.attr("src", util.base_uri+'caches/statics/images/nopic.jpg');
						valobj.val("");
						if (typeof result == 'function') {
							result(false);
						}
					}).css({
						top: imgobj.offset().top,
						left: imgobj.offset().left,
						width: imgobj.outerWidth(),
						height: imgobj.outerHeight()
					});
					$("div.__uploader_formfile_del").show();
				}).mouseout(function(){
					$("div.__uploader_formfile_del").hide();
				});
			}

			btnobj.click(function() {
				$.formfile(function(url){
					if(url.path){
						valobj.val(url.path);
						valobj.attr("path",url.path);
					}
					if(url.fullpath){
						if(imgobj){
							imgobj.get(0).src = url.fullpath;
						}
						valobj.attr("url",url.fullpath);
					}
					if (typeof result == 'function') {
						result(url);
					}
				}, valobj.val(), util.base_uri);

			});
		});
	};

	return uploader;
});