
var NumFormat = {
    /* 数字格式化：小数点后超过的位数进行四舍五入
     * num：数字
     * dotNum: 小数点后保留几位
     * sep:千位分割符(不能包含 .) 例如 123456.45  格式化后为 123,456.45
     */
    numRoundOverflow : function(num, dotNum, sep) {
        if (!num) {
            return parseFloat(0).toFixed(2);
        }
        if (!dotNum) {
            dotNum = 0;
        }
        var negFlag = parseFloat(num) < 0;
        num = num + "";
        // 第一位就是 .
        if (num.indexOf('.') == 0) {
            num = '0' + num
        }
        // 清除“数字”和“.”以外的字符
        num = num.replace(/[^\d.]/g, "");
        // 只保留第一个.
        num = num.replace(/\.{2,}/g, ".");
        num = num.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
        // 以上已经过滤，此处控制的是如果没有小数点，首位不能为类似于 01、02的金额
        if (num.indexOf(".") < 0) {
            num = ~~num + "";
        }
        if (num.lastIndexOf(".") == (num.length - 1)) {
            num = num.replace(".", "");
        }
        // 小数点位数
        if (dotNum < 0) {
            num = parseFloat(num);
        } else {
            num = parseFloat(num).toFixed(dotNum)
        }
        // 千位分割
        if (sep && sep.indexOf(".") == -1) {
            num = num.toString().replace(/\d+/, function(n) { // 先提取整数部分
                return n.replace(/(\d)(?=(\d{3})+$)/g, function($1) {
                    return $1 + ",";
                });
            })
        }
        return negFlag ? "-" + num : num;
    },



    /* 数字格式化：小数点后超过的位数进行舍弃
     * num：数字
     * dotNum: 小数点后保留几位
     * sep:千位分割符(不能包含 .) 例如 123456.45  格式化后为 123,456.45
     */
    numDropOverflow : function(num, dotNum, sep) {
        if (!num) {
            return "";
        }
        if (!dotNum) {
            dotNum = 0;
        }
        num = num + "";
        // 第一位就是 .
        if (num.indexOf('.') == 0) {
            num = '0' + num
        }
        // 清除“数字”和“.”以外的字符
        num = num.replace(/[^\d.]/g, "");
        // 只保留第一个.
        num = num.replace(/\.{2,}/g, ".");
        num = num.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
        // 只能输入dotNum个小数
        num = num.replace(eval("/^(\\-)*(\\d+)\\.(\\d{" + dotNum + "}).*$/"), '$1$2.$3');
        // 以上已经过滤，此处控制的是如果没有小数点，首位不能为类似于 01、02的金额
        if (num.indexOf(".") < 0) {
            num = ~~num + "";
        }
        // 千位分割
        if (sep && sep.indexOf(".") == -1) {
            num = num.toString().replace(/\d+/, function(n) { // 先提取整数部分
                return n.replace(/(\d)(?=(\d{3})+$)/g, function($1) {
                    return $1 + ",";
                });
            })
        }
        return num;
    },

    /* 只能输入数字
     * num：输入数字
     */
    numOnly : function(num) {
        if (!num) {
            return "";
        }
        num = num + "";
        // 清除“数字”以外的字符
        num = num.replace(/[^\d]/g, "");
        return num;
    }
}




