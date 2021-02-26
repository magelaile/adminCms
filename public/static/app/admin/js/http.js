/*
 * 基于jquery的http请求工具
 * 在使用该工具前需要先引入 jquery
*/



var httpTool = {

    //get请求
    httpGet:function (param){
        var url = param.url ? param.url : '';
        var data= param.data? param.data: {};

        $.ajax(url, {
            type : 'get',
            data : data,
            dataType : 'json',
            timeout : 1000*60,//60s
            success : function(res) {
                sucess(res);
            },
            error : function(xhr, type, errorThrown) {
                sucess(xhr,type,errorThrown);
            },
            complete: function (res){
                complete(res);
            }
        });
    },

    //post请求
    httpPost:function (url,data,sucess,error,complete){
        $.ajax(url, {
            data : data,
            type : 'post',
            timeout : 1000*60,//60s
            dataType : 'json',
            success : function(res) {
                sucess(res);
            },
            error : function(xhr, type, errorThrown) {
                sucess(xhr,type,errorThrown);
            },
            complete: function (res){
                complete(res);
            }
        });
    }


};
