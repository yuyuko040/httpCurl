<?php
namespace App\CurlHttp;

use App\Exceptions\ApiException;

class CurlHttp
{
    private $method; //请求方式
    private $url; //路由
    private $keysArr; //get参数
    private $param; //post数据
    private $post_file; //是否为文件

    public function __construct($method, $route, $keysArr=[], $param=[], $post_file=false)
    {
        $this->url = env('YUJIAN_HOTEL_API').$route;
        $this->keysArr = $keysArr;
        $this->param = $param;
        $this->post_file = $post_file;
        $this->method = $method;
    }

    /**
     * get获取数据
     *
     * @return string
     * @throws ApiException
     */
    public function http_get()
    {
        $url = self::combineURL($this->url, $this->keysArr);

        $method = $this->method;

        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);

        $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        curl_setopt($oCurl, CURLOPT_USERAGENT, $user_agent);

        if (!empty($timeout)) {
            curl_setopt($oCurl, CURLOPT_TIMEOUT, $timeout);   //秒
        }

        if (!empty($headers)) {
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $headers);
        }

        switch ($method) {
            case 'GET':
                curl_setopt($oCurl, CURLOPT_HTTPGET, true);
                break;
            case "DELETE":
                curl_setopt ($oCurl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);

        if ( $aStatus["http_code"] == 500 ) {
            throw new ApiException('服务器错误!');
        }

        return response()->json(\GuzzleHttp\json_decode($sContent), $aStatus["http_code"], array(), JSON_UNESCAPED_UNICODE);

    }

    /**
     * post获取数据
     *
     * @return string
     * @throws ApiException
     */
    public function http_post()
    {
        $url = self::combineURL($this->url, $this->keysArr);

        $param = $this->param;
        $post_file = $this->post_file;
        $method = $this->method;

        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);

        if (!empty($timeout)) {
            curl_setopt($oCurl, CURLOPT_TIMEOUT, $timeout);   //秒
        }

        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; // browsers keep this blank.

        if (!empty($headers)) {
            $header = array_merge($header, $headers);
        }

        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);

        $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        curl_setopt($oCurl, CURLOPT_USERAGENT, $user_agent);

        switch ($method) {
            case 'POST':
                curl_setopt($oCurl, CURLOPT_HTTPGET, "POST");
                break;
            case "PUT":
                curl_setopt ($oCurl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
        }

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);

        if ( $aStatus["http_code"] == 500 ) {
            throw new ApiException('服务器错误!');
        }

        return response()->json(\GuzzleHttp\json_decode($sContent), $aStatus["http_code"], array(), JSON_UNESCAPED_UNICODE);

    }

    /**
     * 链接绑定参数
     * @param $baseURL string 链接
     * @param $keysArr array 参数
     * @return bool|mixed
     */
    public function combineURL($baseURL,$keysArr)
    {
        if ($keysArr==[]){
            return $baseURL;
        }

        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);

        return $combined;
    }

}