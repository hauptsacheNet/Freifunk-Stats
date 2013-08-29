<?php

namespace Freifunk\StatisticBundle\Events;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Freifunk\StatisticBundle\Controller\WidgetController;
use Doctrine\Common\Persistence\ObjectManager;
use Freifunk\StatisticBundle\Entity\Widget;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class WidgetLogListener
 * @package Freifunk\StatisticBundle\Events
 */
class WidgetLogListener
{

    /** @var ObjectManager */
    private $manager;

    /**
     * Constructor
     *
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Logs calls of any widget.
     *
     * @param FilterControllerEvent $event
     */
    public function onWidgetControllerCall(FilterControllerEvent $event)
    {

        $controller = $event->getController();

        if ($controller[0] instanceof WidgetController) {

            $widget = new Widget();
            $request = $event->getRequest();

            $widget->setRequest($request->getUri());
            $widget->setReferer($request->headers->get('referer'));
            $widget->setIp($request->getClientIp());
            $widget->setUserAgent($request->headers->get('user-agent'));

            // todo: reactivate this!!!
            //$this->manager->persist($widget);
            //$this->manager->flush();

        }

        $event->setController($controller);
    }

}