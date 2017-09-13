<?php
/**
 * CURL get
 *
 * @param string $url 地址
 *
 * @return array
 */
function curlGet($url, $headers=[]) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //将抓取的东西返回
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $return = curl_exec($curl); //抓取
    curl_close($curl);
    if (curl_errno($curl)) {
        var_dump(curl_errno($curl));
    }
    return json_decode($return, true);
}