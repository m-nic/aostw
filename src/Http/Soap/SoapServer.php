<?php

namespace App\Http\Soap;

class SoapServer
{
    private $serverUrl;
    private $service;

    public function __construct($url)
    {
        $this->disableSoapCache();
        $this->serverUrl = $url;
    }

    public function registerService($service)
    {
        $this->service = $service;
    }

    public function get_wsdl()
    {
        $soapAutoDiscover = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence());
        $soapAutoDiscover->setBindingStyle(['style' => 'document']);
        $soapAutoDiscover->setOperationBodyStyle(['use' => 'literal']);

        $soapAutoDiscover->setClass($this->service['class']);

        $soapAutoDiscover->setUri($this->serverUrl);

        return $soapAutoDiscover->generate()->toXml();
    }

    public function handle_call()
    {
        $params = $this->service['params'] ?? [];
        $serviceInstance = new $this->service['class'](...$params);

        $soap = new \Zend\Soap\Server($this->serverUrl . '?wsdl');
        $soap->setObject(new \Zend\Soap\Server\DocumentLiteralWrapper($serviceInstance));
        $soap->handle();
    }

    private function disableSoapCache()
    {
        ini_set("soap.wsdl_cache_enabled", 0);
        ini_set('soap.wsdl_cache_ttl', 0);
    }
}