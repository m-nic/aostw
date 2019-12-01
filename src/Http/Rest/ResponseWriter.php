<?php

namespace App\Http\Rest;

class ResponseWriter
{

    /**
     * @var Response $responseInstance
     */
    private $responseInstance;

    public static function of(Response $responseInstance)
    {
        return new self($responseInstance);
    }

    public function __construct(Response $responseInstance)
    {
        $this->responseInstance = $responseInstance;
    }

    public function write()
    {
        try {
            $this->outputHeadersAndBody();
        } catch (\Exception $e) {
            ResponseWriter::of(
                Response::of($e)->setStatus(500)
            )->write();
        }
    }

    private function outputHeadersAndBody()
    {
        $this->outputStatusCode();
        $this->outputHeaders();
        $this->outputResponse();
    }

    private function outputStatusCode()
    {
        $statusCode = $this->responseInstance->getStatus();
        if ($statusCode) {
            http_response_code($statusCode);
        }
    }

    private function outputHeaders()
    {
        $responseHeaders = $this->responseInstance->getHeaders();
        foreach ($responseHeaders as $name => $value) {
            header("{$name}: {$value}");
        }

        if (isJsonSerializable($this->responseInstance->getContent())) {
            header('Content-Type: application/json');
        }
    }

    private function outputResponse()
    {
        echo $this->processResponse();
        exit;
    }

    private function processResponse()
    {
        $content = $this->responseInstance->getContent();

        if (isJsonSerializable($content)) {
            return json_encode($content);
        } elseif (isSerializable($content)) {
            return $content->serialize();
        }

        return (string) $content;
    }
}