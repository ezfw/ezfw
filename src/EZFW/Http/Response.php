<?php

namespace EZFW\Http;

class Response
{
    public string $body = "";
    public int $response_code = 200;
    public array $headers = [];

    public function send()
    {
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
        http_response_code($this->response_code);
        echo $this->body;
    }

    public function text(string $body)
    {
        $this->addHeader('Content-type', 'text/plain; charset=UTF-8');
        $this->body = $body;
        return $this;
    }

    public function html(string $body)
    {
        $this->addHeader('Content-type', 'text/html; charset=UTF-8');
        $this->body = $body;
        return $this;
    }

    public function notFound(string $body)
    {
        $this->response_code = 404;
        $this->body = $body;
        return $this;
    }

    public function internalServerError(string $body)
    {
        $this->response_code = 500;
        $this->body = $body;
        return $this;
    }

    public function addHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
    }
}
