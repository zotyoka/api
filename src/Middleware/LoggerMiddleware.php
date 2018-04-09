<?php
namespace Zotyo\Api\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerMiddleware
{
    private $log;
    private $request;

    public function __construct()
    {
        $this->log = new Logger('api');
        $this->log->pushHandler(new StreamHandler(storage_path('logs/api/'.date('Y-m-d').'.log'), Logger::INFO));
    }

    public function handle(Request $request, Closure $next)
    {
        $this->request  = $request;
        $this->logRequest();

        $response = $next($request);

        if ($response instanceof Response) {
            $this->logResponse($response);
        }
        if ($response instanceof JsonResponse) {
            $this->logJsonResponse($response);
        }
        
        return $response;
    }

    private function logRequest()
    {
        $this->log->info("REQUEST  | ".$this->ip()." | ".$this->url()." | ".json_encode($this->escapedInput()));
    }

    private function logResponse(Response $response)
    {
        $status   = $response->getStatusCode();
        $output = method_exists($response, 'getContent') ? $response->getContent() : 'NON-JSON';
        $this->log->info("RESPONSE | ".$this->ip()." | ".$this->url()." | ".$status." | ".$output);
    }

    private function logJsonResponse(JsonResponse $response)
    {
        $status   = $response->getStatusCode();
        $output = method_exists($response, 'getContent') ? $response->getContent() : 'NON-JSON';
        $this->log->info("RESPONSE | ".$this->ip()." | ".$this->url()." | ".$status." | ".$output);
    }

    private function ip()
    {
        return $this->request->getClientIp();
    }

    private function url()
    {
        return $this->request->getUri();
    }

    private function escapedInput()
    {
        $input = $this->request->all();

        $this->escapeIfExists($input, 'password');
        $this->escapeIfExists($input, 'password_confirmation');

        return $input;
    }

    private function escapeIfExists(array &$input, string $key)
    {
        if (!array_key_exists($key, $input)) {
            return;
        }

        $value = $input[$key];

        if (is_string($value)) {
            $input[$key] = $value[0].'***'.$value[strlen($value) - 1];
        }
    }
}
