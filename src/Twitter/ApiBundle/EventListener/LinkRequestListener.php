<?php

namespace Twitter\ApiBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class LinkRequestListener
{
    /**
     * @var HttpKernelInterface
     */
    private $httpKernel;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ControllerResolverInterface
     */
    private $resolver;

    /**
     * @var UrlMatcherInterface
     */
    private $urlMatcher;

    /**
     * @param HttpKernelInterface         $httpKernel         The 'http_kernel' service
     * @param EventDispatcherInterface    $eventDispatcher    The 'event_dispatcher' service
     * @param ControllerResolverInterface $controllerResolver The 'controller_resolver' service
     * @param UrlMatcherInterface         $urlMatcher         The 'router' service
     */
    public function __construct(HttpKernelInterface $httpKernel, EventDispatcherInterface $eventDispatcher, ControllerResolverInterface $controllerResolver, UrlMatcherInterface $urlMatcher)
    {
        $this->httpKernel = $httpKernel;
        $this->eventDispatcher = $eventDispatcher;
        $this->resolver = $controllerResolver;
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->getRequest()->headers->has('link')) {
            return;
        }

        $links  = array();
        $header = $event->getRequest()->headers->get('link');

        /*
         * Due to limitations, multiple same-name headers are sent as comma
         * separated values.
         *
         * This breaks those headers into Link headers following the format
         * http://tools.ietf.org/html/rfc2068#section-19.6.2.4
         */
        while (preg_match('/^((?:[^"]|"[^"]*")*?),/', $header, $matches)) {
            $header  = trim(substr($header, strlen($matches[0])));
            $links[] = $matches[1];
        }

        if ($header) {
            $links[] = $header;
        }

        $requestMethod = $this->urlMatcher->getContext()->getMethod();
        // Force the GET method to avoid the use of the
        // previous method (LINK/UNLINK)
        $this->urlMatcher->getContext()->setMethod('GET');

        // The controller resolver needs a request to resolve the controller.
        $stubRequest = new Request();

        foreach ($links as $idx => $link) {
            $linkParams = explode(';', trim($link));
            $resource   = array_shift($linkParams);
            $resource   = preg_replace('/<|>/', '', $resource);

            if (preg_match('#^/|https?://#', $resource) === 0) {
                $resource = '/' . $resource;
            }

            try {
                $route = $this->urlMatcher->match($resource);
            } catch (\Exception $e) {
                // If we don't have a matching route we return
                // the original Link header
                continue;
            }

            $stubRequest->attributes->replace($route);

            if (false === $controller = $this->resolver->getController($stubRequest)) {
                continue;
            }

            try {
                $stubEvent = new FilterControllerEvent($this->httpKernel, $controller, $stubRequest, HttpKernelInterface::SUB_REQUEST);
                $this->eventDispatcher->dispatch(KernelEvents::CONTROLLER, $stubEvent);

                $arguments = $this->resolver->getArguments($stubRequest, $controller);

                $result = call_user_func_array($controller, $arguments);

                // By convention the controller action must return an array
                if (!is_array($result)) {
                    continue;
                }

                // The key of first item is discarded
                $links[$idx] = current($result);
            } catch (\Exception $e) {
                continue;
            }
        }

        $event->getRequest()->attributes->set('link', $links);
        $this->urlMatcher->getContext()->setMethod($requestMethod);
    }
}
