<?php

namespace App\Http\Rest;

class Response
{

    private $content;

    private $headers = [];
    private $status = 200;

    public static function of($response)
    {
        return new self($response);
    }

    public function __construct($response)
    {
        $this->content = $response;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function addHeader($header)
    {
        $this->headers[] = $header;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

}