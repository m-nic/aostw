<?php

require_once __DIR__ . '/vendor/autoload.php';

class Hello
{
    /**
     * Say hello.
     *
     * @param string $firstName
     * @return string $greetings
     */
    public function sayHello($firstName)
    {
        return 'Hello ' . $firstName;
    }

    /**
     * Test fn.
     *
     * @param string $firstName
     * @return string $greetings
     */
    public function testCall($firstName)
    {
        return 'qwe ' . $firstName;
    }
}

ini_set( "soap.wsdl_cache_enabled", 0 );
ini_set( 'soap.wsdl_cache_ttl', 0 );

/**
 * @param $serverUrl
 */
function runServer($serverUrl)
{
    $options = [
        'uri' => $serverUrl,
    ];
    $server = new Zend\Soap\Server(null, $options);

    if (isset($_GET['wsdl'])) {
        $soapAutoDiscover = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence());
        $soapAutoDiscover->setBindingStyle(['style' => 'document']);
        $soapAutoDiscover->setOperationBodyStyle(['use' => 'literal']);
        $soapAutoDiscover->setClass('Hello');
        $soapAutoDiscover->setUri($serverUrl);

        header("Content-Type: text/xml");
        echo $soapAutoDiscover->generate()->toXml();
    } else {
        $soap = new \Zend\Soap\Server($serverUrl . '?wsdl');
        $soap->setObject(new \Zend\Soap\Server\DocumentLiteralWrapper(new Hello()));
        $soap->handle();
    }
}

//$serverUrl = "http://localhost:8000/server.php";
$serverUrl = 'http://nicolaemihale.com/aostw/soap/server.php';
runServer($serverUrl);