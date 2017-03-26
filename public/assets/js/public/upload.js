/**
 资料上传
 */
$(function () {


});

var agreementCheck = function (applyid, phase, callFunction) {
    var agreementCheck = false;
    $.ajax({
        url: '/mortgageApproval/agreementCheck',
        type: 'GET',
        'dataType': 'json',
        data: 'applyid=' + applyid + "&phase=" + phase,
        success: function (data) {
            if (data.code == 1) {
                agreementCheck = true;
            } else {
                toastr.error(data.msg);
            }

            if ($.isFunction(callFunction)) {
                callFunction(agreementCheck);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            toastr.error('服务器异常' + XMLHttpRequest.status);

            if ($.isFunction(callFunction)) {
                callFunction(false);
            }
        }
    })
};


