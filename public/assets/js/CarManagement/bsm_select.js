/**
 * 品牌车系车型三级联动
 * @param  {[type]} y_serial [description]
 * @param  {[type]} y_model  [description]
 * @param  {[type]} y_trim   [description]
 * @return {[type]}          [description]
 */
function yxp_serial_model_trim(y_serial, y_model, y_trim) {
	var base_url = '/carmanagement/getAjaxMsInfo/?callback=?&';
	var yxp_serial_select = $(y_serial);
	var yxp_model_select = $(y_model);
	var yxp_trim_select = $(y_trim);

	var data_serial = $(y_serial).data('selected');
	var lang = $(y_serial).data('lang');
	lang = lang ? lang : 'zh';
	var data_model = $(y_model).data('selected');
	var data_trim = $(y_trim).data('selected');
	var re = /,/;
	data_serial = re.test(data_serial) ? data_serial.split(',')[0] : data_serial;
	data_model = re.test(data_model) ? data_model.split(',')[0] : data_model;
	data_trim = re.test(data_trim) ? data_trim.split(',')[0] : data_trim;
	var selected;
	$.getJSON(base_url, { 't':1 }, function(json){
		$.each(json, function(key, val){
			selected = '';
			if(val.brandid == parseInt(data_serial)){
				selected = 'selected';
			}
			selected = val.brandid == parseInt(data_serial) ? 'selected' : '';
			yxp_serial_select.append('<option title="'+val.brandname+'" value="'+val.brandid+'" '+selected+'>'+val.brandname+'</option>');
		});
		$(y_serial).trigger("change");
	});
	$(y_serial).bind('change', function(){
		yxp_model_select.html('<option value="">请选择车系</option>');
		yxp_trim_select.html('<option value="">请选择车型</option>');

		if($(this).val() == 0 || $(this).val()==''){
			$("#select2-"+y_model.substring(1)+"-container").html('请选择车系');
			$("#select2-"+y_trim.substring(1)+"-container").html('请选择车型');
			return ;
		}
		$.getJSON(base_url, { 't':2,'brandid':$(this).val() }, function(json){
			$.each(json, function(key, val){
				 selected = val.seriesid == parseInt(data_model) ? 'selected' : '';
				yxp_model_select.append('<option title="'+val.seriesname+'" value="'+val.seriesid+'" '+selected+'>'+val.seriesname+'</option>');
			});
			$(y_model).trigger("change");
		});
	});
	$(y_model).bind('change', function(){
		yxp_trim_select.html('<option value="">请选择车型</option>');
		$("#select2-"+y_trim.substring(1)+"-container").html('请选择车型');

		if($(this).val() == 0 || $(this).val()==''){
			//$("#select2-"+y_trim.substring(1)+"-container").html('请选择车型');
			return ;
		}
		$.getJSON(base_url, { 't':3,'seriesid':$(this).val() }, function(json){
			$.each(json, function(key, val){
				 selected = val.modeid == parseInt(data_trim) ? 'selected' : '';
				yxp_trim_select.append('<option title="'+val.modename+'" value="'+val.modeid+'" '+selected+'>'+val.modename+'</option>');
				if(selected == 'selected'){
					$("#select2-"+y_trim.substring(1)+"-container").html(val.modename);
				}
			});
		});
	});
}