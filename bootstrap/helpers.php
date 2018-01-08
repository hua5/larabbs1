<?php
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}
//生成简介的方法
function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}






