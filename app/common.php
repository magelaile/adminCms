<?php
// 应用公共文件

/*格式化打印输出
 */
function p($data,$die_flag=true)
{
    echo '<pre>';print_r($data);
    echo '<pre>------------------------------------------';
    if($die_flag==true) die;
}

function response_json($res,$extra_data=[]) {
    $res_arr = [
        'msg'        => $res['msg']  ? $res['msg']  : '默认提示信息',
        'data'       => $res['data'] ? $res['data'] : [],
        'code'       => $res['code'] ? $res['code'] : -1,
        'count'      => $res['count']? $res['count']: 0,
        'extra_data' => $res['extra_data'] ? array_merge($res['extra_data'],$extra_data) : $extra_data,
    ];
    return json($res_arr);
}

function success_return($msg='',$data=[],$count=0,$extra_data=[],$code=1) {
    if(empty($msg)) $msg='操作成功';
    $res = [
        'msg'        => $msg,
        'data'       => $data,
        'count'      => $count,
        'code'       => $code,
        'extra_data' => $extra_data,
        'status'     => true,
    ];
    return $res;
}

function fail_return($msg='',$data=[],$count=0,$extra_data=[],$code=-1) {
    if(empty($msg)) $msg='操作失败';
    $res = [
        'msg'        => $msg,
        'data'       => $data,
        'count'      => $count,
        'code'       => $code,
        'extra_data' => $extra_data,
        'status'     => false,
    ];
    return $res;
}


/*加密函数
 */
function md100($pwd,$salt)
{
    return md5(md5($pwd.$salt));
}


/*验证密码
 */
function check_pwd($new_pwd,$old_pwd,$salt)
{
    if($old_pwd===md100($new_pwd,$salt)){
        return true;
    }else{
        return false;
    }
}


/*随机生成指定长度字符串
 * @param $length  生成的字符串的长度
 */
function get_str_by_len($length=4)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = '';
    for ($i=0;$i<$length;$i++)
    {
        $str .= $chars[mt_rand(0,strlen($chars)-1)];
    }
    return $str;
}


/*检查邮箱格式
 */
function check_email($email)
{
    if(!preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/",$email)){
        return false;
    }
    return true;
}


/*检查手机号格式
 */
function check_mobile($mobile){
    if(!preg_match('/^1[34578]\d{9}$/',$mobile)){
        return false;
    }
    return true;
}


/* 递归删除文件夹
 * @param array $path 文件夹路径或者文件路径
 * @param boolean $delDir $path为文件夹路径时候是否删除该文件夹
 */
function del_file($path,$delDir=false){
    //先清除函数的缓存结果
    clearstatcache();

    if(is_dir($path) && file_exists($path))
    {//是一个路径且路径存在
        $handle = @opendir($path);
        if($handle){
            //删除文件
            while(false !== ($item=readdir($handle)))
            {
                if($item!='.' && $item!='..')
                {   //跳过 . 和 .. 文件夹
                    if(is_dir("$path/$item")){
                        del_file("$path/$item",$delDir);
                    }else{
                        unlink("$path/$item");
                    }
                }
            }
            closedir($handle);

            //删除文件夹
            if($delDir){
                return rmdir($path);
            }
            return true;
        }
        //打开文件失败
        return false;
    }
    elseif(is_file($path) && file_exists($path))
    {//是一个文件且文件存在
        return unlink($path);
    }
    else
    {//路径无效
        return false;
    }
}


//===================后台独有======================

/*管理员操作日志
 * @param int      $admin_id     管理员ID
 * @param string   $log_info     操作日志
 * @param string   $log_ip       操作IP
 * @param string   $log_url      操作连接
 */
function admin_log($admin_id=0,$log_info='',$log_ip='',$log_url='')
{
    $data = array();
    $data['admin_id'] = $admin_id>0 ? $admin_id : 0;
    $data['log_info'] = $log_info;
    $data['log_ip']   = !empty($log_ip) ? $log_ip : request()->ip();
    $data['log_url']  = !empty($log_url) ? $log_url : request()->url();
    $data['log_time'] = time();
    db('admin_log')->insert($data);
}

