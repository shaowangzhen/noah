/**
 字段验证 开始
 */

/*
 added by jtz 用于表单验证
 class="inp inpInput validate" validateContent='{"ValidateId":"VendorFullName","DisplayId":"VendorFullName","ValidateTypes":"1,2","CompareId":"",
 "Content":[{"ErrorMsg":"会员全称不能为空"},{"ErrorMsg":"会员名称最大长度100个字符","MaxLength":100}]}'

 */

//字段验证
var vendorValidate = function (formInput) {
    var msg = "";

    if ($(formInput).attr("validate") != undefined && $(formInput).val().trim().len() == 0) { //为空
        msg = $(formInput).attr("validate");
    } else if ($(formInput).attr("validateNumMsg") != undefined && $(formInput).val().trim().len() != 0 && (!isInteger($(formInput).val().trim()) || (isInteger($(formInput).val().trim()) && eval($(formInput).val()) < 0))) // 数字
    {
        msg = $(formInput).attr("validateNumMsg");
    } else if ($(formInput).attr("validateLen") != undefined && eval($(formInput).val().trim().len()) > eval($(formInput).attr("validateLen"))) { //长度
        msg = $(formInput).attr("validateLenMsg");
    }
    return msg;

};
//
var showValidateTip = function (formInput) {
    var result = {
        "IsSucceed": true,
        "Msg": "",
        "IsShowTip": true
    };
    if ($(formInput).attr("validateContent") != undefined) {
        var validateContent = jQuery.parseJSON($(formInput).attr("validateContent"));
        result = ValidateInput(validateContent, formInput);
        validateAllSucceed = result.IsSucceed;
        $(formInput).parent().parent().find(".numIco").remove();
        //$(formInput).parent().find('.form-control-feedback').remove();
        $(formInput).parent().parent().removeClass("has-error  has-feedback");
        $(formInput).parent().parent().removeClass("has-success");

        if (result.IsShowTip) {
            if (validateAllSucceed == true) {

                $(formInput).parent().parent().addClass("has-success");
            } else {
                //$(formInput).parent().append($('<span class="glyphicon glyphicon-remove form-control-feedback" style="float:left;width:200px;" aria-hidden="true" title="' + result.Msg + '">' + result.Msg + '</span>'));

                $(formInput).parent().parent().addClass("has-error  has-feedback");

            }
        }
        return result;
    }
    return result;
};
// form 验证
var validateForm = function (formid) {
    var result;
    var issuccess = true;
    var msg = '',
        flag = false;
    $("#" + formid + " .validate:enabled").each(function (item) { //所有显示 验证
        result = showValidateTip(this);
        if (result.IsSucceed == false) {
            issuccess = false;
            flag = true;
            msg += result.Msg + "<br>";
        }

    });
    //msg += '</ul>';
    if (flag) {
        toastr.clear();
        toastr.error(msg);
    }
    //notify(msg, 'bottom-right', '3000', 'callout callout-danger', 'callout callout-danger', true);
    return issuccess;
};

var ValidateInput = function (validateContent, forminput) {
    var result = {
        "IsSucceed": true,
        "Msg": "",
        "IsShowTip": true
    };
    if (validateContent.ValidateTypes != undefined) {
        var validateTypes = validateContent.ValidateTypes.split(",");
        var validatContents = validateContent.Content;
        var validateValue = $(forminput).val();
        validateValue = $.trim(validateValue);
        for (i = 0; i < validateTypes.length; i++) {

            if (eval(validateTypes[i]) != 1 && !IsEmpty(validateValue, validatContents[i].ExceptValue)) //非必填的 如果为空，则不做后续规则验证
            {
                result.IsSucceed = true;
                result.IsShowTip = false;
            } else {
                switch (eval(validateTypes[i])) {
                    case 1:
                        //非空验证
                        result.IsSucceed = IsEmpty(validateValue, validatContents[i].ExceptValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 2:
                        //最大长度验证
                        result.IsSucceed = CompareStrLength(validateValue, eval(validatContents[i].MaxLength));
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 3:
                        //是否数字
                        result.IsSucceed = IsNum(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 4:
                        //是否日期格式
                        result.IsSucceed = IsDate(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 5:
                        //时间比较
                        result.IsSucceed = CompareDate($("#" + validatContents[i].StartDateID).val(), $("#" + validatContents[i].EndDateID).val());
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 6:
                        //身份证验证
                        result.IsSucceed = checkCard(validateValue)[0];
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 7:
                        //座机
                        result.IsSucceed = isTel(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 8:
                        //手机
                        result.IsSucceed = isMobile(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 9:
                        //邮件
                        result.IsSucceed = IsEmail(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 10:
                        //检查radio 必填项
                        result.IsSucceed = CheckRadioIsEmpty(forminput, validatContents[i].ExceptValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 11:
                        //检查两个字段比较是否相同
                        result.IsSucceed = $("#" + validateContent.CompareId).val() == validateValue;
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 12:
                        //特殊字符验证
                        result.IsSucceed = isLegalString(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 13:
                        //检查select 必填项
                        result.IsSucceed = CheckSelectIsEmpty(forminput, validatContents[i].ExceptValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 14:
                        //检查手机号和座机号
                        result.IsSucceed = isLegalPhone(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 15:
                        //检查是否是中文姓名
                        result.IsSucceed = isChinese(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 16:
                        //特殊字符验证
                        result.IsSucceed = checkval(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 22:
                        //最小长度验证
                        result.IsSucceed = !CompareStrLength(validateValue, eval(validatContents[i].MinLength));
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 23:
                        //数字小于下限验证
                        result.IsSucceed = !CompareNumLt(validateValue, eval(validatContents[i].MinNum));
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 24:
                        //数字大于上限验证
                        result.IsSucceed = !CompareNumGt(validateValue, eval(validatContents[i].MaxNum));
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 25:
                        //价格验证
                        result.IsSucceed = IsPrice(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 26:
                        //0值验证
                        result.IsSucceed = !IsZero(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                    case 27:
                        //是否短日期格式
                        result.IsSucceed = IsShortDate(validateValue);
                        result.Msg = validatContents[i].ErrorMsg;
                        break;
                }
            }
            if (result.IsSucceed == false)
                break;
        }
    }

    return result;
};

function isInteger(str) {
    var regu = /^[-]{0,1}[0-9]{1,}$/;
    return regu.test(str);
}

function isMobile(value) {
    if (/^13\d{9}$/g.test(value) || (/^15[0-35-9]\d{8}$/g.test(value)) || (/^17[04-8]\d{8}$/g.test(value)) || (/^18\d{9}$/g.test(value))) {
        return true;
    } else {
        return false;
    }
}

//固定电话
function isTel(value) {
    if (value == "" || value == undefined)
        return false;
    //var reg = /^([0-9]|[\-])+$/g;
    var reg = /^0\d{2,3}-?\d{7,8}$/;
    return reg.test(value);
}

function isLegalPhone(value) {
    return isTel(value) ? true : isMobile(value);
    //return  isMobile(value);
}

String.prototype.trim = function () {
    // 用正则表达式将前后空格
    // 用空字符串替代。
    return this.replace(/(^\s*)|(\s*$)/g, "");
};
//得到字符串的字符长度（一个汉字占两个字符长）
String.prototype.len = function () // 给string增加个len ()方法，计算string的字节数
{
    return this.replace(/[^\x00-\xff]/g, "xx").length;
};
//定义允许含有的字符
function isLegal(str) {
    if (str >= '0' && str <= '9')
        return true;
    if (str >= 'a' && str <= 'z')
        return true;
    if (str >= 'A' && str <= 'Z')
        return true;
    if (str == '_')
        return true;
    if (str == ' ')
        return true;
    var reg = /^[\u4e00-\u9fa5]+$/i;
    if (reg.test(str))
        return true;
    return false;
}

//检测字符串是否含有非法字符
function isAllLegal(str1) {
    if (str1 == "" || str1 == undefined)
        return true;
    for (n = 0; n < str1.length; n++) {
        if (!isLegal(str1.charAt(n))) {
            return false;
        }
    }
    return true;
}

//字母.数字和下划线
function checkchars(value) {
    if (value == "" || value == undefined)
        return true;
    var reg = /^[a-zA-Z0-9_\-]{1,}$/;
    return value.match(reg);
}

function checkval(t) {
    var re = /[@#\$%\^&\*]+/g;//只能输入汉字和英文字母
    if (re.test(t)) {
        return false;
    } else {
        return true;
    }
}


//特殊字符窜验证
function isLegalString(str1) {
    if (str1 == "" || str1 == undefined)
        return true;
    for (m = 0; m < str1.length; m++) {
        if (isNoLegalChar(str1.charAt(m))) {
            return false;
        }
    }
    return true;
}
//不合法字符验证
function isNoLegalChar(checkedObject) {
    var re = /<|>|'|;|&|#|"|\$|\*|\[|\]|\{|\}|\%|\`|\||\:|\,|\\|\//;
    return re.test(checkedObject);
}
//身份证验证函数
var aCity = {
    11: "北京",
    12: "天津",
    13: "河北",
    14: "山西",
    15: "內蒙古",
    21: "遼寧",
    22: "吉林",
    23: "黑龍江",
    31: "上海",
    32: "江蘇",
    33: "浙江",
    34: "安徽",
    35: "福建",
    36: "江西",
    37: "山東",
    41: "河南",
    42: "湖北",
    43: "湖南",
    44: "廣東",
    45: "廣西",
    46: "海南",
    50: "重慶",
    51: "四川",
    52: "貴州",
    53: "雲南",
    54: "西藏",
    61: "陝西",
    62: "甘肅",
    63: "青海",
    64: "寧夏",
    65: "新疆",
    71: "臺灣",
    81: "香港",
    82: "澳門",
    91: "國外"
};

function checkCard(sId) {
    var iSum = 0;
    var info = "";
    var result = [];

    if (sId.length != 15 && sId.length != 18) {
        result[0] = false;
        result[1] = "身份證號碼長度錯誤";
        return result;
    }

    if (sId.length == 15) { //15位身份證驗證
        if (isNaN(sId)) {
            result[0] = false;
            result[1] = "身份證號碼格式錯誤";
            return result;
        }
        if (aCity[parseInt(sId.substr(0, 2))] == null) {
            result[0] = false;
            result[1] = "非法地區";
            return result;
        }
        var sBirthday = "19" + sId.substr(6, 2) + "-" + Number(sId.substr(8, 2)) + "-" + Number(sId.substr(10, 2));
        var d = new Date(sBirthday.replace(/-/g, "/"));
        if (sBirthday != (d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate())) {
            result[0] = false;
            result[1] = "非法生日";

            return result;

        }
    } else { //18位身份證驗證
        if (!/^\d{17}(\d|x)$/i.test(sId)) {
            result[0] = false;
            result[1] = "非身份證號碼";
            return result;
        }
        sId = sId.replace(/x$/i, "a");
        if (aCity[parseInt(sId.substr(0, 2))] == null) {
            result[0] = false;
            result[1] = "非法地區";
            return result;
        }
        var sBirthday = sId.substr(6, 4) + "-" + Number(sId.substr(10, 2)) + "-" + Number(sId.substr(12, 2));
        var d = new Date(sBirthday.replace(/-/g, "/"));
        if (sBirthday != (d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate())) {
            result[0] = false;
            result[1] = "非法生日";

            return result;

        }
        for (var i = 17; i >= 0; i--)
            iSum += (Math.pow(2, i) % 11) * parseInt(sId.charAt(17 - i), 11);
        if (iSum % 11 != 1) {
            result[0] = false;
            result[1] = "非法證號";
            return result;

        }
    }
    result[0] = true;

    var Sex_Flag = (sId.length == 15) ? sId.substr(14, 1) : sId.substr(16, 1);
    //男性為奇數，女性為偶數
    result[1] = "合法證件\r\n\r\n證件基本信息為：" + aCity[parseInt(sId.substr(0, 2))] + "," + sBirthday + "," + (Sex_Flag % 2 ? "男" : "女");

    return result;
}

//身份证验证函数

//返回距 1970 年 1 月 1 日之间的毫秒数
function GetTime(date) {

    var arr = date.split("-");
    var time = new Date(arr[0], arr[1], arr[2]);
    return time.getTime();
}

var ValidateDate = function (startDate, endDate) {
    if (startDate == "") {
        alert("开始时间不能为空！");
        return false;
    }
    if (endDate == "") {
        alert("结束时间不能为空！");
        return false;
    }
    var start_time = GetTime(startDate);
    var end_time = GetTime(endDate);

    if (start_time > end_time) {
        alert("开始时间不能大于结束时间，请重新选择");
        return false;
    }

    return true;
};
//非空验证
var IsEmpty = function (validateValue, exceptValue) {
    if (validateValue.length == 0)
        return false;
    if (exceptValue != undefined && validateValue == exceptValue)
        return false;
    return true;
};
//检查radio 必填项
var CheckRadioIsEmpty = function (forminput, exceptValue) {
    validateId = $(forminput).attr('name');
    if ($("input[name='" + validateId + "']:checked").val() == undefined || $("input[name='" + validateId + "']:checked").val() == exceptValue)
        return false;
    return true;
};
var CheckSelectIsEmpty = function (forminput, exceptValue) {
    if ($(forminput).find('option:selected').val() == undefined || $(forminput).find('option:selected').val() == exceptValue)
        return false;
    return true;
};
//验证字符串最大长度
var CompareStrLength = function (validateValue, maxLength) {
    if (validateValue.len() > maxLength)
        return false;
    return true;
};
//验证数字是否超过上限
var CompareNumGt = function (validateValue, maxNum) {
    if (validateValue <= maxNum)
        return false;
    return true;
};
//验证数字是否小于下限
var CompareNumLt = function (validateValue, minNum) {
    if (validateValue >= minNum)
        return false;
    return true;
};
//是否数字
var IsNum = function (validateValue) {
    var regu = /^[0-9]*$/;
    return regu.test(validateValue);
};
//是否日期
var IsDate = function (validateValue) {
    var reg = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2}) (\d{1,2}):(\d{1,2})(:(\d{1,2}))?$/;
    var r = validateValue.match(reg);
    if(r==null){
        return false;
    } else {
        return true;
    }
};
//是否日期
var IsShortDate = function (validateValue) {
    var reg = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})?$/;
    var r = validateValue.match(reg);
    if(r==null){
        return false;
    } else {
        return true;
    }
};

//是否为价格（保留2位小数点）
var IsPrice = function (validateValue) {
    var reg = /(^[-+]?[1-9]\d*(\.\d{1,2})?$)|(^[-+]?[0]{1}(\.\d{1,2})?$)/;
    var r = validateValue.match(reg);
    if(r==null){
        return false;
    } else {
        return true;
    }
};

//邮箱
var IsEmail = function (validateValue) {

    var regu = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    return regu.test(validateValue);
};
var CompareDate = function (startDate, endDate) {
    if (startDate != "" && endDate != "") {
        var start_time = GetTime(startDate);
        var end_time = GetTime(endDate);

        if (start_time > end_time) {

            return false;
        }

    }
    return true;
};

//是否是中文名字
var isChinese = function (validateValue) {
    var regu = /^[\u4E00-\u9FA5]*(?:·[\u4E00-\u9FA5])*$/;
    return regu.test(validateValue);
}

//是否值为0
var IsZero = function (validateValue) {
    if(parseInt(validateValue) == 0) {
        return true;
    } else {
        return false;
    }
};

/**
 字段验证 结束
 */

function isMobileUser(){
    var sUserAgent= navigator.userAgent.toLowerCase(),
    bIsIpad= sUserAgent.match(/ipad/i) == "ipad",
    bIsIphoneOs= sUserAgent.match(/iphone os/i) == "iphone os",
    bIsMidp= sUserAgent.match(/midp/i) == "midp",
    bIsUc7= sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4",
    bIsUc= sUserAgent.match(/ucweb/i) == "ucweb",
    bIsAndroid= sUserAgent.match(/android/i) == "android",
    bIsCE= sUserAgent.match(/windows ce/i) == "windows ce",
    bIsWM= sUserAgent.match(/windows mobile/i) == "windows mobile",
    bIsWebview = sUserAgent.match(/webview/i) == "webview";
    return (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM || bIsWebview);
}
/**
 * 兼容手机fancybox手机端新页面打开
 * @returns {undefined}
 */
$(function(){
    if(isMobileUser()){
        $('a.fancybox').prop('target','_blank').removeClass('fancybox');
        $('button.fancybox').removeClass('fancybox').click(function(){
            window.open($(this).attr('href'));
        });
    }
});