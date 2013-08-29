<?php

namespace Freifunk\StatisticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsController
 *
 * @package Freifunk\StatisticBundle\Controller
 *
 * @Route("/js")
 */
class JsController extends Controller
{
    /**
     * Generates the dynamic widget loader.
     *
     * @param Request $request
     *
     * @return $response
     *
     * @Route("/widget")
     */
    public function widgetJsAction(Request $request)
    {
        $response = $this->render(
            "FreifunkStatisticBundle:Js:widget.js.twig",
            array(
                'baseurl' => $request->getBaseUrl()
            )
        );
        $response->headers->add(array(
            'Content-Type' => 'text/javascript; charset=UTF-8'
        ));
        // cache
        $response->setPublic();
        $response->setETag(md5($response->getContent()));
        $response->isNotModified($this->getRequest());
        $response->setMaxAge(60 * 60 * 24 * 30);

        return $response;
    }
}
