<?php

namespace Twitter\ApiBundle\Controller;

use Twitter\DomainBundle\Entity\Tweet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;

class TweetController extends ApiController
{
    /**
     * Get a Tweet
     *
     * **Response Format**
     *
     *      {
     *          "tweet": {
     *              "username": "sgordalina",
     *              "content": "an example tweet",
     *              "created_at": "2013-04-11T20:00:00+0000"
     *          }
     *      }
     *
     * @ApiDoc(
     *     section="Tweets",
     *     resource=true,
     *     statusCodes={
     *         200="OK"
     *     }
     * )
     */
    public function getAction(Tweet $tweet)
    {
        return array('tweet' => $tweet);
    }

    /**
     * Post a Tweet
     *
     * **Request Format**
     *
     *      {
     *          "content": "an example tweet"
     *      }
     *
     * **Response Headers**
     *
     *      Location: http://example.com/api/tweets/4
     *
     * @ApiDoc(
     *     section="Tweets",
     *     statusCodes={
     *         201="Created",
     *         400="Invalid input"
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        $tweet = $this->deserialize('Twitter\DomainBundle\Entity\Tweet', $request);

        if ($tweet instanceof Tweet === false) {
            return View::create(array('errors' => $tweet), 400);
        }

        $tweet->setUser($this->getUser());

        $em = $this->getEntityManager();
        $em->persist($tweet);
        $em->flush();

        $url = $this->generateUrl(
            'tweet_get',
            array('id' => $tweet->getId()),
            true
        );

        $response = new Response();
        $response->setStatusCode(201);
        $response->headers->set('Location', $url);

        return $response;
    }

    /**
     * Update a Tweet
     *
     * **Request Format**
     *
     *      {
     *          "content": "an example tweet"
     *      }
     *
     * **Response Format**
     *
     *      {
     *          "tweet": {
     *              "username": "sgordalina",
     *              "content": "an example tweet",
     *              "created_at": "2013-04-11T20:00:00+0000"
     *          }
     *      }
     *
     * @ApiDoc(
     *     section="Tweets",
     *     statusCode={
     *         200="Updated",
     *         400="Invalid input",
     *         403="Forbidden"
     *     }
     * )
     */
    public function putAction(Tweet $tweet, Request $request)
    {
        if ($this->getUser() !== $tweet->getUser()) {
            throw new HttpException(403, 'Forbidden');
        }
        $newTweet = $this->deserialize('Twitter\DomainBundle\Entity\Tweet', $request);

        if ($newTweet instanceof Tweet === false) {
            return View::create(array('errors' => $newTweet), 400);
        }

        $tweet->merge($newTweet);
        $this->getEntityManager()->flush();

        return array('tweet' => $tweet);
    }

    /**
     * Delete a Tweet
     *
     * @ApiDoc(
     *     section="Tweets",
     *     statusCodes={
     *         204="Deleted",
     *         403="Forbidden"
     *     }
     * )
     * @RestView(statusCode=204)
     */
    public function deleteAction(Tweet $tweet)
    {
        if ($this->getUser() !== $tweet->getUser()) {
            throw new HttpException(403, 'Forbidden');
        }

        $em = $this->getEntityManager();
        $em->remove($tweet);
        $em->flush();
    }

}
