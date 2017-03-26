var conAct = false;
var con = $('input[name=controller]');
var act = $('input[name=action]');
var name = $('input[name=actionname]');
$(function () {
    changeType($('select[name=type]').val());
    $('select[name=type]').on('change', function () {
        changeType($(this).val());
    });
    $('.btn_sub').on('click', function () {
        if (!conAct) {
            if (!con.val()) {
                alert('controller不能为空');
                return false;
            }
            if (!act.val()) {
                alert('action不能为空');
                return false;
            }
        }
        if (!$('input[name=actionname]').val()) {
            alert('权限名称不能为空');
            return false;
        }
        $('#action_form').submit();
    });
});
function changeType(type)
{
    var pid = $('select[name=parent_actionid]').closest('.form-group');
    var ord = $('input[name=orderid]').closest('.form-group');
    if (type == 1)
    {
        conChange(0);
        pid.hide();
        ord.show();
    } else if (type == 2)
    {
        conChange(1);
        pid.show();
        ord.show();
    } else {
        conChange(1);
        pid.hide();
        ord.hide();
    }
}
function conChange(val)
{
    if (val == 0)
    {
        con.attr({disabled: true}).val('');
        act.attr({disabled: true}).val('');
        conAct = true;
    } else
    {
        con.attr('disabled', false);
        act.attr('disabled', false);
        conAct = false;
    }
}