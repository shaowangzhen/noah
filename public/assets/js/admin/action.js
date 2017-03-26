g_aid = 0;
var authtype = _type;
var folder_html = '<div class="tree-folder-header" onclick="set_node(this)">replace_info</div> <div class="tree-folder-content"> </div> <div class="tree-loader" style="display: none;"><div class="tree-loading"><i class="fa fa-rotate-right fa-spin"></i></div></div>';
var action = "<div class='tree-actions'><i onclick='do_action(this,event)' class='fa fa-plus' title='添加子节点' data-id='actionid' data-action='3' data-pid='parent_id'></i> <i onclick='del_action(this,event)' class='fa fa-trash-o' title='删除节点' role='dialog' data-toggle='modal' data-target='#myModal' data-id='actionid' data-action='2' data-pid='parent_id'></i> <i onclick='do_action(this,event)' class='fa fa-plus-square-o' title='添加同级节点' data-id='actionid' data-action='1' data-pid='parent_id'></i> </div>";
var node_info = '<div class="tree-item" style="display: block;"> <i class="tree-dot"></i> <div class="tree-item-name"><span class="cur_node_actionid">node_title</span>' + action + ' </div></div>';

$(document).ready(function () {
    $.fn.serializeObject = function () {
        var obj = {};
        var item = this.serializeArray();
        $.each(item, function () {
            if (obj[this.name] !== undefined) {
                if (!obj[this.name].push) {
                    obj[this.name] = [obj[this.name]];
                }
                obj[this.name].push(this.value || '');
            } else {
                obj[this.name] = this.value || '';
            }
        });
        return obj;
    };
    var DataSourceTree = function (options) {
        this.url = options.url;
    }

    DataSourceTree.prototype.data = function (options, callback) {
        var self = this;
        var $data = null;
        var param = null;
        if (!("name" in options) && !("type" in options)) {
            param = 0; //load the first level
        }
        else if ("type" in options && options.type == "folder") {
            if ("additionalParameters" in options && "children" in options.additionalParameters) {
                param = options.additionalParameters["id"];
            }
        }
        if (param == 0) {
            if (g_aid > 0)
                param = g_aid;
            g_aid = 0;
        }
        if (param != null) {
            $.post(this.url, {id: param, _token: _token, not_log: true}, function (response) {
                if (response.code == 1) {
                    setTimeout(function () {
                        var items = response.data, replace_action = '';
                        for (var e in items) {
                            replace_action = action.replace(/actionid/g, items[e]['id']);
                            items[e]['name'] = '<span class="cur_node_' + items[e]['id'] + '">' + items[e]['name'] + '</span>' + replace_action.replace(/parent_id/g, items[e]['pid']);
                        }
                        var data = jQuery.extend(true, [], items);
                        callback({data: data})
                    }, 400)
                } else if (response.code == '-401') {
                    toastr.success(response.msg);
                }

            }, 'json');
        }
    };
    //树形结构生成
    jQuery('#MyTree').tree({
        'dataSource': new DataSourceTree({url: _url_tree}),
        'multiSelect': false,
        'open-icon': 'icon-minus',
        'close-icon': 'icon-plus',
        'selectable': false,
        'selected-icon': 'icon-ok',
        'unselected-icon': 'icon-remove',
        'cacheItems': false,
        'loadingHTML': '<div class="tree-loading"><i class="fa fa-rotate-right fa-spin"></i></div>'
    });
    $('#MyTree').on('selected', function (evt, data) {
        var id = data.info[0].additionalParameters.id;
        getinfo(id);
    });
    $('#MyTree').on('opened', function (evt, data) {
        getinfo(data.id);
    });
    //checkbox
    $('.check_status').click(function () {
        var status = parseInt($(this).val());
        if (status == -1) {
            $(this).val(1);
            $(this).attr("checked",true);
        } else {
            $(this).val( - 1);
            $(this).attr("checked",false);
        }
    });
    //表单提交
    $('.saveinfo').on('click', function () {
        var isValid = validateForm("auth_form");
        if (isValid) {
            var data = $('.form-horizontal').serializeObject();
            $.post(_url_add, data, function (res) {
            var actionid = res.data['id'];
            var pid = $('#parent_actionid').val();
            var action_name = $('#actionname').val();
            //添加操作
            if (res.code == 1) {
                $('#actionid').val(actionid);
                if (parseInt(pid) == 0) {//动态添加同级节点 一级子节点
                    var str_html = node_info.replace(/actionid/g, actionid);
                    str_html = str_html.replace(/parent_id/g, pid);
                    str_html = str_html.replace(/node_title/g, action_name);
                    $('#MyTree').append(str_html);
                    if ($('#MyTree .cur_node_' + pid).parent().hasClass('tree-folder-name')) {
                        $('.cur_node_' + actionid).parent().parent().click();
                    }
                } else {
                    var node_class = '.cur_node_' + pid;
                    if ($('#MyTree ' + node_class).parent().hasClass('tree-item-name')) {
                        //item -> folder 添加子节点
                        var pid = $('#MyTree ' + node_class).next().find('i').attr('data-pid');
                        $('#MyTree ' + node_class).parent().removeClass('tree-item-name').addClass('tree-folder-name');
                        $('#MyTree ' + node_class).parent().parent().find('i').first().removeClass('tree-dot').addClass('fa fa-folder');
                        $('#MyTree ' + node_class).parent().parent().removeClass('tree-item').addClass('tree-folder');
                        var new_html = $('#MyTree ' + node_class).parent().parent().html();
                        new_html = folder_html.replace('replace_info', new_html);
                        $('#MyTree ' + node_class).parent().parent().html(new_html);
                    } else {
                        //item
                        if ($('#MyTree .cur_node_' + pid).parent().prev().hasClass('fa-folder-open')) {
                            $('#MyTree ' + node_class).parent().parent().click();
                            $('#MyTree ' + node_class).parent().parent().click();
                        } else {
                            $('#MyTree ' + node_class).parent().parent().click();
                        }
                    }
                }

                toastr.success(res.msg);
            } else if (res.code == 2) {
                $('#MyTree .cur_node_' + actionid).text(action_name);
                toastr.success(res.msg);
            } else {
                toastr.warning(res.msg);
            }
        }, 'json');
    } else {
        return isValid;
    }
    });
    //点击确认 删除按钮
    $('.del_confirm').bind('click', function () {
        var id = $(this).attr('data-id');
        if (parseInt(id) == - 1) return false;
        $.post(_url_del, {id: id, _token: _token}, function (res) {
            if (res.code == 1) {
                var cur_class = '.cur_node_' + res.data['id'];
                if ($(cur_class).parent().attr('class') == 'tree-item-name') {
                    $(cur_class).parent().parent().remove();
                } else {
                    $(cur_class).parent().parent().parent().remove();
                }
            }
            toastr.success(res.msg);
        }, 'json')

    });
}); //end

function del_action(tar, event) {
    var data_id = $(tar).attr('data-id');
    var tips = '确定要删除<font class="red">' + $('.cur_node_' + data_id).text() + '</font>吗？';
    $('.tip_body p').html(tips);
    $('.del_confirm').attr('data-id', data_id);
}

//item -> folder 动态获取子菜单
function set_node(tar) {
    var action_id = $('.tree-actions i', tar).first().attr('data-id');
    g_aid = action_id;
    console.log('g_aaa', g_aid);
}

//新节点后 选中操作
function new_node(tar, event) {
    var action_id = $('.tree-actions i', tar).first().attr('data-id');
    g_aid = action_id;
    console.log('g_aaa', g_aid);
    getinfo(action_id);
    event.stopPropagation();
}

function do_action(tar, event) {
    var action_id = $(tar).attr('data-action');
    var data_id = $(tar).attr('data-id');
    var pid = $(tar).attr('data-pid');
    $('.form-horizontal input').val('');
    $('.showtype option[value="1"]').attr('selected', true);
    $('.authtype option[value="0"]').attr('selected', true);
    $('#actionid').val(0);
    $('.ctm_2').val($('.ctm_2').attr('data-info'));
    $('.ctm_3').val($('.ctm_3').attr('data-info'));
    $('#auth_status').val(1);
    var cur_node_name = $('.cur_node_' + data_id).text();
    if (action_id == 1) {
        $('.form-title').html('添加 ' + cur_node_name + ' 同级节点信息');
        $('#parent_actionid').val(pid);
    }

    if (action_id == 3) {
        $('.form-title').html('添加 ' + cur_node_name + ' 子节点信息');
        $('#parent_actionid').val(data_id);
    }

    event.stopPropagation();
}



function getinfo(id)
{
    $('.form-group').removeClass('has-error has-success');
    var cur_name = $('.cur_node_' + id).text();
    var cur_pid = $('.cur_node_' + id).next().find('i').eq(0).attr('data-pid');
    var cur_parent_name = '';
    if (cur_pid != 0) {
        cur_parent_name = $('.cur_node_' + cur_pid).text() + ' - ';
    }

    $('.form-title').text(cur_parent_name + cur_name);
    $.post(_url_get, {
        id: id,
        _token: _token,
        not_log: true
    }, function (response) {
    if (response.code == 1) {
        var data = response.data;
        var val;
        for (var key in data) {
            val = data[key];
            if (key == 'status'){
                if(val == 1){
                    $('.check_status').prop("checked",true);
                }else if(val == -1){
                    $('.check_status').prop("checked",false);
                }
            }
            $(".form-horizontal [name='" + key + "']").val(val);
        }

        $('.showtype option[value=' + data.showtype + ']').attr('selected', true);
        $('.authtype option[value=' + data.type + ']').attr('selected', true);
        /*(var status = parseInt($('#auth_status').val());
        $('.check_status').val(data.status);
        if(parseInt(data.status) == 1){
            $('.check_status').attr("checked",true);
        }else if(parseInt(data.status) == -1){
            $('.check_status').attr("checked",false);
        }
        if (parseInt(data.status) != parseInt(status)) {
           // $('.check_status').click();
        }*/

    } else if (response.code == '-401'){
        toastr.success(response.msg);
    } else {
        toastr.success('数据异常,稍后重试');
    }

    }, 'json');
}