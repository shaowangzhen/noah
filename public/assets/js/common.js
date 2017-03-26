/**
 * Created by Administrator on 14-4-23.
 */

function myformatter(date) {
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    var d = date.getDate();
    return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
}

function myparser(s) {
    if (!s) return new Date();
    var ss = (s.split('-'));
    var y = parseInt(ss[0], 10);
    var m = parseInt(ss[1], 10);
    var d = parseInt(ss[2], 10);
    if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
        return new Date(y, m - 1, d);
    } else {
        return new Date();
    }
}

/*
 The global object JSON contains two methods.

 JSON.stringify(value) takes a JavaScript value and produces a JSON text.
 The value must not be cyclical.

 JSON.parse(text) takes a JSON text and produces a JavaScript value. It will
 return false if there is an error.
 */
var JSON = function() {
    var m = {
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"': '\\"',
            '\\': '\\\\'
        },
        s = {
            'boolean': function(x) {
                return String(x);
            },
            number: function(x) {
                return isFinite(x) ? String(x) : 'null';
            },
            string: function(x) {
                if (/["\\\x00-\x1f]/.test(x)) {
                    x = x.replace(/([\x00-\x1f\\"])/g, function(a, b) {
                        var c = m[b];
                        if (c) {
                            return c;
                        }
                        c = b.charCodeAt();
                        return '\\u00' +
                            Math.floor(c / 16).toString(16) +
                            (c % 16).toString(16);
                    });
                }
                return '"' + x + '"';
            },
            object: function(x) {
                if (x) {
                    var a = [],
                        b, f, i, l, v;
                    if (x instanceof Array) {
                        a[0] = '[';
                        l = x.length;
                        for (i = 0; i < l; i += 1) {
                            v = x[i];
                            f = s[typeof v];
                            if (f) {
                                v = f(v);
                                if (typeof v == 'string') {
                                    if (b) {
                                        a[a.length] = ',';
                                    }
                                    a[a.length] = v;
                                    b = true;
                                }
                            }
                        }
                        a[a.length] = ']';
                    } else if (x instanceof Object) {
                        a[0] = '{';
                        for (i in x) {
                            v = x[i];
                            f = s[typeof v];
                            if (f) {
                                v = f(v);
                                if (typeof v == 'string') {
                                    if (b) {
                                        a[a.length] = ',';
                                    }
                                    a.push(s.string(i), ':', v);
                                    b = true;
                                }
                            }
                        }
                        a[a.length] = '}';
                    } else {
                        return;
                    }
                    return a.join('');
                }
                return 'null';
            }
        };
    return {
        copyright: '#',
        license: '#',
        /*
         Stringify a JavaScript value, producing a JSON text.
         */
        stringify: function(v) {
            var f = s[typeof v];
            if (f) {
                v = f(v);
                if (typeof v == 'string') {
                    return v;
                }
            }
            return null;
        },
        /*
         Parse a JSON text, producing a JavaScript value.
         It returns false if there is a syntax error.
         */
        parse: function(text) {
            try {
                return !(/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/.test(
                        text.replace(/"(\\.|[^"\\])*"/g, ''))) &&
                    eval('(' + text + ')');
            } catch (e) {
                return false;
            }
        }
    };
}();
$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
//序列化form表单内容，如果存在name相同input，则返回array
$.fn.serializeForm = function() {
    var o = {};
    $("#" + this.attr("id") + " :input").each(function(i, item) {
        if (o[item.name]) {
            if (!o[item.name].push) {
                o[item.name] = [o[item.name]];
            }
            o[item.name].push(item.value || '');
        } else {
            o[item.name] = item.value || '';
        }
    });
    return o;
};
//序列化form表单内容，如果存在name相同input，则返回逗号分隔的string
$.fn.serializeForm2 = function() {
    args = {};
    $("#" + this.attr('id') + " :input").each(function(i, item) {

        if ((item.type == 'checkbox' || item.type == 'radio') && item.checked == false)
            return;
        if (args[item.name]) {
            if (!args[item.name].push) {
                args[item.name] = [args[item.name]];
            }
            args[item.name] = args[item.name] + "," + item.value;
        } else {
            args[item.name] = item.value || '';
        }

    });
    return args;
};
$.fn.loadForm = function(data) {

    form = this;
    $.each(data, function(name, val) {
        input = $("#" + form.attr('id') + " [name=" + name + "]");
        if (input.length > 0) {

            console.log(input[0].type + "  " + name + "=" + val + "   " + "#" + form.attr('id') + " [name=" + name + "]");
            htmlType = input[0].type;
            if (htmlType == "text" || htmlType == "textarea" || htmlType == "hidden" || htmlType == "button") {
                $("#" + form.attr('id') + " [name=" + name + "]").val(val);
            } else if (htmlType == "radio") {
                $("#" + form.attr('id') + " input[type=radio][name='" + name + "'][value='" + val + "']").attr("checked", true);
            } else if (htmlType == "checkbox") {
                var vals = val.split(",");
                for (var i = 0; i < vals.length; i++) {
                    $("#" + form.attr('id') + " input[type=checkbox][name='" + name + "'][value='" + vals[i] + "']").attr("checked", true);
                }
            } else if (htmlType == "select-one") {
                $("#" + form.attr('id') + " [name=" + name + "]").select2('val', val);
                $("#" + form.attr('id') + " [name=" + name + "]").trigger('change');
            } else if (htmlType == "select-multiple") {
                vals = val.split(',');
                data_arr = [];
                $.each(vals, function(n, v) {
                    temp = [];
                    temp['id'] = v;
                    temp['text'] = name;
                    data_arr.push(temp);
                });

                $("#" + form.attr('id') + " [name=" + name + "]").select2('data', $.toJson(data_arr));
                $("#" + form.attr('id') + " [name=" + name + "]").trigger('change');
            }
        }
    });
}
var setValue = function(name, val) {
    if (val != "") {
        var htmlType = $("[name='" + name + "']").attr("type");
        if (htmlType == "text" || htmlType == "textarea" || htmlType == "select-one" || htmlType == "hidden" || htmlType == "button") {
            $("[name='" + name + "']").val(val);
        } else if (htmlType == "radio") {
            $("input[type=radio][name='" + name + "'][value='" + val + "']").attr("checked", true);
        } else if (htmlType == "checkbox") {
            var vals = val.split(",");
            for (var i = 0; i < vals.length; i++) {
                $("input[type=checkbox][name='" + name + "'][value='" + vals[i] + "']").attr("checked", true);
            }
        }
    }
};
/**
 字段验证 开始
 */

/*
 added by jtz 用于表单验证
 class="inp inpInput validate" validateContent='{"ValidateId":"VendorFullName","DisplayId":"VendorFullName","ValidateTypes":"1,2","CompareId":"",
 "Content":[{"ErrorMsg":"会员全称不能为空"},{"ErrorMsg":"会员名称最大长度100个字符","MaxLength":100}]}'

 */

//字段验证
var vendorValidate = function(formInput) {
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
var showValidateTip = function(formInput) {
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
        $(formInput).parent().find('.form-control-feedback').remove();
        $(formInput).parent().parent().removeClass("has-error  has-feedback")
        $(formInput).parent().parent().removeClass("has-success");

        if (result.IsShowTip) {
            if (validateAllSucceed == true) {

                $(formInput).parent().parent().addClass("has-success");
            } else {
                $(formInput).parent().append($('<span class="glyphicon glyphicon-remove form-control-feedback" style="right: 30px" aria-hidden="true" title="' + result.Msg + '"></span>'));

                $(formInput).parent().parent().addClass("has-error  has-feedback");

            }
        }
        return result;
    }
    return result;
};
// form 验证
var validateForm = function(formid) {
    var result;
    var issuccess = true;
    var msg = '<ul>',
        flag = false;
    $("#" + formid + " .validate:enabled").each(function(item) { //所有显示 验证
        result = showValidateTip(this);

        if (result.IsSucceed == false) {
            issuccess = false;
            flag = true;
            msg += '<li style="line-height: 20px">' + result.Msg + '</li>';
        }

    });
    msg += '</ul>';
    if (flag)
        Notify(msg, 'bottom-right', '3000', 'danger', 'fa-warning', true);
    return issuccess;
};

var ValidateInput = function(validateContent, forminput) {
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
    if (/^1\d{10}$/g.test(value)) {
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

String.prototype.trim = function() {
    // 用正则表达式将前后空格
    // 用空字符串替代。
    return this.replace(/(^\s*)|(\s*$)/g, "");
};
//得到字符串的字符长度（一个汉字占两个字符长）
String.prototype.len = function() // 给string增加个len ()方法，计算string的字节数
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

var ValidateDate = function(startDate, endDate) {
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
var IsEmpty = function(validateValue, exceptValue) {
    if (validateValue.length == 0)
        return false;
    if (exceptValue != undefined && validateValue == exceptValue)
        return false;
    return true;
};
//检查radio 必填项
var CheckRadioIsEmpty = function(forminput, exceptValue) {
    validateId = $(forminput).attr('name');
    if ($("input[name='" + validateId + "']:checked").val() == undefined || $("input[name='" + validateId + "']:checked").val() == exceptValue)
        return false;
    return true;
};
var CheckSelectIsEmpty = function(forminput, exceptValue) {
    if ($(forminput).find('option:selected').val() == undefined || $(forminput).find('option:selected').val() == exceptValue)
        return false;
    return true;
};
//验证字符串最大长度
var CompareStrLength = function(validateValue, maxLength) {
    if (validateValue.len() > maxLength)
        return false;
    return true;
};
//验证数字是否超过上限
var CompareNumGt = function(validateValue, maxNum) {
    if (validateValue <= maxNum)
        return false;
    return true;
};
//验证数字是否小于下限
var CompareNumLt = function(validateValue, minNum) {
    if (validateValue >= minNum)
        return false;
    return true;
};
//是否数字
var IsNum = function(validateValue) {
    var regu = /^[0-9]*$/;
    return regu.test(validateValue);
};
//是否日期
var IsDate = function(validateValue) {
    var d = new Date(validateValue);
    return (d.getDate()==validateValue.substring(validateValue.length-2));
};
//邮箱
var IsEmail = function(validateValue) {

    var regu = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    return regu.test(validateValue);
};
var CompareDate = function(startDate, endDate) {
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
var isChinese = function(validateValue) {
    var regu = /^[\u4E00-\u9FA5]*(?:·[\u4E00-\u9FA5])*$/;
    return regu.test(validateValue);
}

/**
 字段验证 结束
 */

/**
加载中。。。
*/

! function(a, b) {
    "object" == typeof module && module.exports ? module.exports = b() : "function" == typeof define && define.amd ? define(b) : a.Spinner = b()
}(this, function() {
    "use strict";

    function a(a, b) {
        var c, d = document.createElement(a || "div");
        for (c in b) d[c] = b[c];
        return d
    }

    function b(a) {
        for (var b = 1, c = arguments.length; c > b; b++) a.appendChild(arguments[b]);
        return a
    }

    function c(a, b, c, d) {
        var e = ["opacity", b, ~~(100 * a), c, d].join("-"),
            f = .01 + c / d * 100,
            g = Math.max(1 - (1 - a) / b * (100 - f), a),
            h = j.substring(0, j.indexOf("Animation")).toLowerCase(),
            i = h && "-" + h + "-" || "";
        return m[e] || (k.insertRule("@" + i + "keyframes " + e + "{0%{opacity:" + g + "}" + f + "%{opacity:" + a + "}" + (f + .01) + "%{opacity:1}" + (f + b) % 100 + "%{opacity:" + a + "}100%{opacity:" + g + "}}", k.cssRules.length), m[e] = 1), e
    }

    function d(a, b) {
        var c, d, e = a.style;
        if (b = b.charAt(0).toUpperCase() + b.slice(1), void 0 !== e[b]) return b;
        for (d = 0; d < l.length; d++)
            if (c = l[d] + b, void 0 !== e[c]) return c
    }

    function e(a, b) {
        for (var c in b) a.style[d(a, c) || c] = b[c];
        return a
    }

    function f(a) {
        for (var b = 1; b < arguments.length; b++) {
            var c = arguments[b];
            for (var d in c) void 0 === a[d] && (a[d] = c[d])
        }
        return a
    }

    function g(a, b) {
        return "string" == typeof a ? a : a[b % a.length]
    }

    function h(a) {
        this.opts = f(a || {}, h.defaults, n)
    }

    function i() {
        function c(b, c) {
            return a("<" + b + ' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">', c)
        }
        k.addRule(".spin-vml", "behavior:url(#default#VML)"), h.prototype.lines = function(a, d) {
            function f() {
                return e(c("group", {
                    coordsize: k + " " + k,
                    coordorigin: -j + " " + -j
                }), {
                    width: k,
                    height: k
                })
            }

            function h(a, h, i) {
                b(m, b(e(f(), {
                    rotation: 360 / d.lines * a + "deg",
                    left: ~~h
                }), b(e(c("roundrect", {
                    arcsize: d.corners
                }), {
                    width: j,
                    height: d.scale * d.width,
                    left: d.scale * d.radius,
                    top: -d.scale * d.width >> 1,
                    filter: i
                }), c("fill", {
                    color: g(d.color, a),
                    opacity: d.opacity
                }), c("stroke", {
                    opacity: 0
                }))))
            }
            var i, j = d.scale * (d.length + d.width),
                k = 2 * d.scale * j,
                l = -(d.width + d.length) * d.scale * 2 + "px",
                m = e(f(), {
                    position: "absolute",
                    top: l,
                    left: l
                });
            if (d.shadow)
                for (i = 1; i <= d.lines; i++) h(i, -2, "progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)");
            for (i = 1; i <= d.lines; i++) h(i);
            return b(a, m)
        }, h.prototype.opacity = function(a, b, c, d) {
            var e = a.firstChild;
            d = d.shadow && d.lines || 0, e && b + d < e.childNodes.length && (e = e.childNodes[b + d], e = e && e.firstChild, e = e && e.firstChild, e && (e.opacity = c))
        }
    }
    var j, k, l = ["webkit", "Moz", "ms", "O"],
        m = {},
        n = {
            lines: 12,
            length: 7,
            width: 5,
            radius: 10,
            scale: 1,
            corners: 1,
            color: "#000",
            opacity: .25,
            rotate: 0,
            direction: 1,
            speed: 1,
            trail: 100,
            fps: 20,
            zIndex: 2e9,
            className: "spinner",
            top: "50%",
            left: "50%",
            shadow: !1,
            hwaccel: !1,
            position: "absolute"
        };
    if (h.defaults = {}, f(h.prototype, {
            spin: function(b) {
                this.stop();
                var c = this,
                    d = c.opts,
                    f = c.el = a(null, {
                        className: d.className
                    });
                if (e(f, {
                        position: d.position,
                        width: 0,
                        zIndex: d.zIndex,
                        left: d.left,
                        top: d.top
                    }), b && b.insertBefore(f, b.firstChild || null), f.setAttribute("role", "progressbar"), c.lines(f, c.opts), !j) {
                    var g, h = 0,
                        i = (d.lines - 1) * (1 - d.direction) / 2,
                        k = d.fps,
                        l = k / d.speed,
                        m = (1 - d.opacity) / (l * d.trail / 100),
                        n = l / d.lines;
                    ! function o() {
                        h++;
                        for (var a = 0; a < d.lines; a++) g = Math.max(1 - (h + (d.lines - a) * n) % l * m, d.opacity), c.opacity(f, a * d.direction + i, g, d);
                        c.timeout = c.el && setTimeout(o, ~~(1e3 / k))
                    }()
                }
                return c
            },
            stop: function() {
                var a = this.el;
                return a && (clearTimeout(this.timeout), a.parentNode && a.parentNode.removeChild(a), this.el = void 0), this
            },
            lines: function(d, f) {
                function h(b, c) {
                    return e(a(), {
                        position: "absolute",
                        width: f.scale * (f.length + f.width) + "px",
                        height: f.scale * f.width + "px",
                        background: b,
                        boxShadow: c,
                        transformOrigin: "left",
                        transform: "rotate(" + ~~(360 / f.lines * k + f.rotate) + "deg) translate(" + f.scale * f.radius + "px,0)",
                        borderRadius: (f.corners * f.scale * f.width >> 1) + "px"
                    })
                }
                for (var i, k = 0, l = (f.lines - 1) * (1 - f.direction) / 2; k < f.lines; k++) i = e(a(), {
                    position: "absolute",
                    top: 1 + ~(f.scale * f.width / 2) + "px",
                    transform: f.hwaccel ? "translate3d(0,0,0)" : "",
                    opacity: f.opacity,
                    animation: j && c(f.opacity, f.trail, l + k * f.direction, f.lines) + " " + 1 / f.speed + "s linear infinite"
                }), f.shadow && b(i, e(h("#000", "0 0 4px #000"), {
                    top: "2px"
                })), b(d, b(i, h(g(f.color, k), "0 0 1px rgba(0,0,0,.1)")));
                return d
            },
            opacity: function(a, b, c) {
                b < a.childNodes.length && (a.childNodes[b].style.opacity = c)
            }
        }), "undefined" != typeof document) {
        k = function() {
            var c = a("style", {
                type: "text/css"
            });
            return b(document.getElementsByTagName("head")[0], c), c.sheet || c.styleSheet
        }();
        var o = e(a("group"), {
            behavior: "url(#default#VML)"
        });
        !d(o, "transform") && o.adj ? i() : j = d(o, "animation")
    }
    return h
});

;
(function(factory) {

    if (typeof exports == 'object') {
        // CommonJS
        factory(require('jquery'), require('spin.js'))
    } else if (typeof define == 'function' && define.amd) {
        // AMD, register as anonymous module
        define(['jquery', 'spin'], factory)
    } else {
        // Browser globals
        if (!window.Spinner) throw new Error('Spin.js not present')
        factory(window.jQuery, window.Spinner)
    }

}(function($, Spinner) {

    $.fn.spin = function(opts, color) {

        return this.each(function() {
            var $this = $(this),
                data = $this.data()

            if (data.spinner) {
                data.spinner.stop()
                delete data.spinner
            }
            if (opts !== false) {
                opts = $.extend({
                    color: color || $this.css('color')
                }, $.fn.spin.presets[opts] || opts)
                data.spinner = new Spinner(opts).spin(this)
            }
        })
    }

    $.fn.spin.presets = {
        tiny: {
            lines: 8,
            length: 2,
            width: 2,
            radius: 3
        },
        small: {
            lines: 8,
            length: 4,
            width: 3,
            radius: 5
        },
        large: {
            lines: 10,
            length: 8,
            width: 4,
            radius: 8
        }
    }

}));


/**
加载中结束。。。
*/
