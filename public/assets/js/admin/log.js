$(function(){
    $('.btn_info').on('click',function(){
        var logid = $(this).data('logid');
        console.log(logid);
        $.ajax({
            'type':'post',
            'url':base_url+'/admin/log/info/'+logid+'?asd=asd',
            'data':{_token:_token,not_log:true},
            'dataType':'json',
            success:function(e){
                if(e.code == 1){
                    show_info(e.data);
                }else{
                    toastr.warning(e.msg);
                }
            }
        });
    });
});
function show_info(data)
{
    var table = $('.info_table');
    table.find('td').eq(1).html(data.mastername);
    table.find('td').eq(3).html(data.url);
    table.find('td').eq(5).html(data.createtime);
    table.find('td').eq(7).html(data.ip);
    table.find('td').eq(9).html(data.browser);
    table.find('td').eq(11).html(data.platform);
    table.find('td').eq(13).html('<pre>'+data.get+'</pre>');
    table.find('td').eq(15).html('<pre>'+data.post+'</pre>');
    $('.info_modal').modal('show');
}