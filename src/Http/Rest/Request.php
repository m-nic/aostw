<?php

namespace App\Http\Rest;

class Request
{
    private $jsonBody = [];
    private $queryString = [];
    private $postData = [];

    public static function make()
    {
        return new self();
    }

    public function __construct()
    {
        $this->loadJsonPayload();
        $this->loadQueryString();
        $this->loadPostData();
    }

    private function loadJsonPayload()
    {
        $json = file_get_contents('php://input');
        $this->jsonBody = json_decode($json, true) ?? [];
    }

    private function loadQueryString()
    {
        $this->queryString = $_GET ?? [];
    }

    private function loadPostData()
    {
        $this->postData = $_POST ?? [];
    }

    public function get($prop)
    {
        return $this->jsonBody[$prop] ?? $this->postData[$prop] ?? $this->queryString[$prop];
    }

    public function __get($prop)
    {
        return $this->get($prop);
    }

    public function toArray()
    {
        return array_merge(
            $this->queryString,
            $this->postData,
            $this->jsonBody
        );
    }
}