<?php
// 应用公共文件


/* 设置查询列表的页码page和数量limit
 * $param请求参数
 */
function set_page_and_limit($param) {
    $page = isset($param['page']) ? intval($param['page']) : 1;
    $limit = isset($param['limit'])? intval($param['limit']) : 20;
    return [$page,$limit];
}

/* 判断不为空并设置 where条件
 * $param请求参数
 * $param_filed 设置字段
 * $condition 设置条件
 */
function set_where_if_not_empty(&$where,$param,$param_filed,$condition,$where_filed='') {
    if(!is_array($where) || empty($param_filed) || empty($condition)) {
        die('条件设置错误');
    }
    if(empty($where_filed)) {
        $where_filed = $param_filed;
    }

    if(!empty($param[$param_filed])) {
        if('LIKE'==strtoupper($condition)) {
            $where[] = [$where_filed,'LIKE','%'.trim($param[$param_filed]).'%'];
        }else{
            $where[] = [$where_filed,$condition,trim($param[$param_filed])];
        }
    }
}

/* where条件 设置时间查询条件
 * $param请求参数
 * $filter_filed 设置字段
 */
function set_where_time(&$where,$param,$where_filed,$start_time_key,$end_time_key) {
    if(!is_array($where) || empty($where_filed) || empty($start_time_key) || empty($end_time_key)) {
        die('条件设置错误');
    }

    if(!empty($param[$start_time_key])&&empty($param[$end_time_key])){
        $where[] = [$where_filed,'>=',strtotime($param[$start_time_key])];
    }elseif(!empty($param[$start_time_key])&&!empty($param[$end_time_key])){
        $where[] = [$where_filed,'between',[strtotime($param[$start_time_key]),strtotime($param[$end_time_key])]];
    }elseif(empty($param[$start_time_key])&&!empty($param[$end_time_key])){
        $where[] = [$where_filed,'<=',strtotime($param[$end_time_key])];
    }
}