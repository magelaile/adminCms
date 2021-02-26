/*
 * 基于jquery的http请求工具
 * 在使用该工具前需要先引入 jquery
*/



var httpTool = {

    //get请求
    httpGet:function (param){
        var url = param.url ? param.url : '';
        var data= param.data? param.data: {};
        var success = param.success && typeof param.success== 'function' ? param.success : false;
        var error   = param.error && typeof param.error== 'function' ? param.error : false;
        var complete = param.complete && typeof param.complete== 'function' ? param.complete : false;
        var dataType = param.dataType? param.dataType: 'json';

        $.ajax(url, {
            type : 'get',
            data : data,
            dataType : dataType,
            timeout : 1000*60,//60s
            success : function(res) {
                if(false!==success) sucess(res);
            },
            error : function(xhr, type, errorThrown) {
                if(false!==error) error(xhr,type,errorThrown);
            },
            complete: function (res){
                if(false!==complete) complete(res);
            }
        });
    },

    //post请求
    httpPost:function (param){
        var url = param.url ? param.url : '';
        var data= param.data? param.data: {};
        var success = param.success && typeof param.success== 'function' ? param.success : false;
        var error   = param.error && typeof param.error== 'function' ? param.error : false;
        var complete = param.complete && typeof param.complete== 'function' ? param.complete : false;
        var dataType = param.dataType? param.dataType: 'json';

        $.ajax(url, {
            type : 'post',
            data : data,
            dataType : dataType,
            timeout : 1000*60,//60s
            success : function(res) {
                if(false!==success) success(res);
            },
            error : function(xhr, type, errorThrown) {
                if(false!==error) error(xhr,type,errorThrown);
            },
            complete: function (res){
                if(false!==complete) complete(res);
            }
        });
    }


};
