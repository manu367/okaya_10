<?php
//set_exception_handler(function ($exception) {
//    if($exception instanceof HttpException){
//        echo "<h1>Error {$exception->status}</h1>";
//        echo "<p>".$exception->getMessage()."</p>";
//    }else{
//        echo "<p>".$exception->getMessage()."</p>";
//    }
//});
//
///**
// * Class Request
// *
// * Represents the current HTTP request.
// * Collects request method, current file URI, and query parameters.
// *
// * Example URL:
// *   http://localhost/rust.php?pid=10&name=manu
// *
// * Result:
// *   $request->method = "GET"
// *   $request->uri    = "rust.php"
// *   $request->query  = ["pid"=>"10","name"=>"manu"]
// */
//class Request{
//    /** @var string HTTP method (GET, POST, PUT, etc.) */
//    public string $method;
//    /** @var string Current script filename from URL */
//    public string $uri;
//    /** @var array Parsed query string parameters */
//    public $query=[];
//    private $host="";
//    private $cookie="";
//    private $ip="";
//    private $requestTime="";
//    /**
//     * Build request object from PHP superglobals.
//     */
//    public function __construct(){
//
//        // HTTP method like GET / POST
//        $this->method=$_SERVER['REQUEST_METHOD'];
//        // Extract only file name from URL path
//        $this->uri = basename(
//            parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)
//        );
//        // Convert query string into associative array
//        parse_str($_SERVER['QUERY_STRING'],$this->query);
//        $this->host=$_SERVER['HTTP_HOST'];
//        $this->ip=$_SERVER['REMOTE_ADDR'];
//        $this->cookie=$_COOKIE;
//        $this->requestTime=microtime(true);
//    }
//
//    /**
//     * Get a query parameter safely.
//     *
//     * @param string $key     Parameter name
//     * @param mixed  $default Default value if not found
//     * @return mixed
//     *
//     * Example:
//     *   $request->input("pid");
//     *   $request->input("page",1);
//     */
//    public function input(string $key,$default=null){
//        return $this->query[$key] ?? $default;
//
//    }
//    public function toString(){
//        $queryStr = "[" . implode(", ", array_map(function($k,$v){
//                return $k."=".$v;
//            }, array_keys($this->query), $this->query)) . "]";
//        $cookieStr = "[" . implode(", ", array_map(function($k,$v){
//                return $k."=".$v;
//            }, array_keys($this->cookie), $this->cookie)) . "]";
//
//        return "Method=".$this->method.PHP_EOL.
//            "URI=".$this->uri.PHP_EOL.
//            "Host=".$this->host.PHP_EOL.
//            "QUERY=".$queryStr.PHP_EOL.
//            "Cookie=".$cookieStr.PHP_EOL.
//            "RequestTime=".$this->requestTime;
//    }
//}
//
//
///**
// * Class Response
// *
// * Handles HTTP responses returned to the client.
// * Provides helper methods for sending plain text and JSON output.
// *
// * NOTE:
// * These methods RETURN the response content as a string.
// * Caller must echo the returned value to actually send output.
// *
// * Example:
// *   echo $res->send("Hello");
// *   echo $res->json(["ok"=>true]);
// */
//class Response {
//
//    protected int $status = 200;
//    protected array $headers = [];
//    protected string $body = "";
//
//    /** Set HTTP status */
//    public function status(int $code): self {
//        $this->status = $code;
//        return $this;
//    }
//
//    /** Add header */
//    public function header(string $name,string $value): self {
//        $this->headers[$name]=$value;
//        return $this;
//    }
//
//    /**
//     * Prepare a plain text / HTML response.
//     *
//     * @param string $text Response body
//     * @return string
//     *
//     * Example:
//     *   echo $res->send("Welcome");
//     */
//    public function send($text) {
//
//        $this->body=$text;
//        return $this->output();
//    }
//
//    /**
//     * Prepare a JSON response.
//     * Automatically sets the Content-Type header.
//     *
//     * @param array $arr Data to encode as JSON
//     * @return string JSON encoded string
//     *
//     * Example:
//     *   echo $res->json(["status"=>"ok"]);
//     */
//    public function json(array $data,int $status=200) {
//        $this->status=$status;
//        $this->headers["Content-Type"]="application/json";
//        $this->body=json_encode($data,JSON_PRETTY_PRINT);
//       return $this->output();
//    }
//
//    /** Final output */
//    protected function output() {
//        http_response_code($this->status);
//        foreach($this->headers as $k=>$v){
//            header("$k: $v");
//        }
//        return $this->body;
//    }
//}
//
//function GET(string $path, callable $callback){
//    $request = new Request();
//
//    $response = new Response();
//
//    if(($request->uri!==$path)){throw new HttpException(500,"Path is not valid");}
//
//    if($request->method === "GET" && $request->uri === $path){
//        $callback($request,$response);
//        exit;
//    }else{
//        throw new HttpException(500,"Request method not allowed");
//    }
//}
//
//
//function POST(string $path, callable $callback){
//    $request = new Request();
//    $response = new Response();
//    if(($request->uri!==$path)){throw new HttpException(500,"Path is not valid");}
//
//    if($request->method === "POST" && $request->uri === $path){
//        $callback($response, $request);
//        exit;
//    }else{
//        throw new HttpException(500,"Request method not allowed");
//    }
//}
//
//function PUT(string $path, callable $callback){
//    $request = new Request();
//    $response = new Response();
//    if(($request->uri!==$path)){throw new HttpException(500,"Path is not valid");}
//
//    if($request->method === "PUT" && $request->uri === $path){
//        $callback($response, $request);
//        exit;
//    }else{
//        throw new HttpException(500,"Request method not allowed");
//    }
//}
//
//function PATCH(string $path, callable $callback){
//    $request = new Request();
//    $response = new Response();
//    if(($request->uri!==$path)){throw new HttpException(500,"Path is not valid");}
//
//    if($request->method === "PATCH" && $request->uri === $path){
//        $callback($response, $request);
//        exit;
//    }else{
//        throw new HttpException(500,"Request method not allowed");
//    }
//}