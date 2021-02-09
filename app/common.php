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
