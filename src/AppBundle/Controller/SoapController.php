<?php

namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Zend\Soap;
class SoapController extends Controller
{

    public function indexAction()
    {
        $server = new \SoapServer($this->get('kernel')->getRootDir()."/../web/soap/test.wsdl");
        $server->setObject($this->get('serv.soapservice'));

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');

        ob_start();
        $server->handle();
        $response->setContent(ob_get_clean());

        return $response;
    }


    public function init()
    {
        // No cache
        ini_set('soap.wsdl_cache_enable', 0);
        ini_set('soap.wsdl_cache_ttl', 0);
    }

    public function checkAction()
    {
        if(isset($_GET['wsdl'])) {
            return $this->handleWSDL($this->generateUrl('acme_api_soap_check', array(), true), 'check_service');
        } else {
            return $this->handleSOAP($this->generateUrl('acme_api_soap_check', array(), true), 'check_service');
        }
    }

    /**
     * return the WSDL
     */
    public function handleWSDL($uri, $class)
    {
        // Soap auto discover
        $autodiscover = new Soap\AutoDiscover();
        $autodiscover->setClass($this->get($class));
        $autodiscover->setUri($uri);

        // Response
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');
        ob_start();

        // Handle Soap
        $autodiscover->handle();
        $response->setContent(ob_get_clean());
        return $response;
    }

    /**
     * execute SOAP request
     */
    public function handleSOAP($uri, $class)
    {
        // Soap server
        $soap = new Soap\Server(null,
            array('location' => $uri,
                'uri' => $uri,
            ));
        $soap->setClass($this->get($class));

        // Response
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');

        ob_start();
        $client = new SoapClient("http://127.0.0.1:8000/soap/check?wsdl");
        $response = $client->check('Chuck');
        var_dump($response);
        // Handle Soap
        $soap->handle();
        $response->setContent(ob_get_clean());
        return $response;
    }

}