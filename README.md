# httpCurl
一个简单的curl包

new \App\CurlHttp\CurlHttp();

传参
    
    $method; //请求方式 必须
    
    $url; //路由 必须
    
    $keysArr; //get参数 可为空
    
    $param; //post数据 可为空
   
    $post_file; //是否为文件 可为空 默认为非文件
    
简单实例
        
        $id = 1;
        
        $data = ['name' => 'Glass'];

        $data = new \App\CurlHttp\CurlHttp('PUT', '/openapi/hotels/'.$id, [], $data);

        $data = $data->http_post();

        return $data;
