<?php

namespace Twitter\ApiBundle\Controller;

use Twitter\DomainBundle\Entity\Tweet;
use Twitter\DomainBundle\Entity\User;
use Twitter\ApiBundle\Form\Registration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;

class UserController extends ApiController
{
    /**
     * Get user's info
     *
     *      {
     *          "user": {
     *              "username": "sgordalina",
     *          }
     *      }
     *
     * @ApiDoc(
     *     resource=true,
     *     section="User"
     * )
     */
    public function getAction(User $user)
    {
        return array('user' => $user);
    }
    /**
     * Get Tweets by user
     *
     * **Response Format**
     *
     *      {
     *          "tweets": [
     *              {
     *                  "username": "sgordalina",
     *                  "content": "an example tweet",
     *                  "created_at": "2013-04-11T20:00:00+0000"
     *              }
     *          ]
     *      }
     *
     * @ApiDoc(
     *     section="User"
     * )
     */
    public function getTweetsAction(User $user)
    {
        return array('tweets' => $user->getTweets());
    }

    /**
     * Get user's followers
     *
     * **Response Format**
     *
     *      {
     *          "followers": [
     *              {
     *                  "username": "sgordalina",
     *              }
     *          ]
     *      }
     *
     * @ApiDoc(
     *     section="User"
     * )
     */
    public function getFollowersAction(User $user)
    {
        return array('followers' => $user->getFollowers());
    }

    /**
     * Get users following by user
     *
     * **Response Format**
     *
     *      {
     *          "following": [
     *              {
     *                  "username": "sgordalina",
     *              }
     *          ]
     *      }
     *
     * @ApiDoc(
     *     section="User"
     * )
     */
    public function getFollowingAction(User $user)
    {
        return array('following' => $user->getFollowing());
    }

    /**
     * Get own tweets timeline
     *
     * **Response Format**
     *
     *      {
     *          "tweets": [
     *              {
     *                  "username": "sgordalina",
     *                  "content": "an example tweet",
     *                  "created_at": "2013-04-11T20:00:00+0000"
     *              }
     *          ]
     *      }
     *
     * @ApiDoc(
     *     section="User Bound"
     * )
     */
    public function getTimelineAction()
    {
        $timeline = $this->getEntityManager()
            ->getRepository('TwitterDomainBundle:User')
            ->getTimeline($this->getUser());

        return array('tweets' => $timeline);
    }

    /**
     * Register as a user in the application
     *
     * **Request Format**
     *
     *      {
     *          "username": "sgordalina",
     *          "email": "samuel.gordalina@gmail.com",
     *          "password": "sample-password"
     *      }
     *
     * **Response Headers**
     *
     *      Location: http://example.com/api/v1/sgordalina
     *
     * @ApiDoc(
     *     section="Authorization",
     *     statusCodes={
     *         201="Created",
     *         400="Invalid input",
     *         409="Username already taken"
     *     }
     * )
     */
    public function registerAction(Request $request)
    {
        $registration = $this->deserialize('Twitter\ApiBundle\Form\Registration', $request);

        if ($registration instanceof Registration === false) {
            return View::create(array('errors' => $registration), 400);
        }

        $user = $registration->getUser();
        $userManager = $this->get('fos_user.user_manager');
        $exists = $userManager->findUserBy(array('username' => $user->getUsername()));

        if ($exists instanceof User) {
            throw new HttpException(409, 'Username already taken');
        }

        $userManager->updateUser($user);

        $url = $this->generateUrl(
            'user_get',
            array('username' => $user->getUsername()),
            true
        );

        $response = new Response();
        $response->setStatusCode(201);
        $response->headers->set('Location', $url);

        return $response;
    }
    /**
     * Follow a user
     *
     * @ApiDoc(
     *     section="User Bound",
     *     statusCodes={
     *         204="User followed",
     *         409="Already following user"
     *     }
     * )
     * @RestView(statusCode="204")
     */
    public function followAction(User $user)
    {
        $me = $this->getUser();

        if ($me->isFollowing($user)) {
            throw new HttpException(409, 'Already following user');
        }

        $me->followUser($user);

        $this->getEntityManager()->flush();
    }

    /**
     * Unfollow a user
     *
     * @ApiDoc(
     *     section="User Bound",
     *     statusCodes={
     *         204="User followed",
     *         409="Not following user"
     *     }
     * )
     * @RestView(statusCode="204")
     */
    public function unfollowAction(User $user)
    {
        $me = $this->getUser();

        if (!$me->isFollowing($user)) {
            throw new HttpException(409, 'Not following user');
        }

        $me->unfollowUser($user);

        $this->getEntityManager()->flush();
    }
}
