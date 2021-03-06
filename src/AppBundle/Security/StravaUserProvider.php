<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class StravaUserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    public $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUsernameForApiKey($apiKey)
    {
        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        $username = '';
        return $username;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $stravaId = $response->getUsername();
        $currentUser = $this->em->getRepository('AppBundle:User')->findOneByStravaId($stravaId);

        // return found user, or create new account
        if ($currentUser) {
            return $currentUser;
        } else {

            $userInfo = $response->getResponse();
            $stravaId = $response->getUsername();
            $stravaToken = $response->getAccessToken();
            $email = $response->getEmail();
            $firstName = $userInfo['firstname'];
            $lastName =  $userInfo['lastname'];
            $displayName = "$firstName $lastName";
            $city = $userInfo['city'];
            $state = $userInfo['state'];
            $country = $userInfo['country'];
            $gender = $userInfo['sex'];

            $user = new User;
            $user->setStravaId($stravaId);
            $user->setStravaToken($stravaToken);
            $user->setEmail($email);
            $user->setPassword('testmeup');
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setDisplayName($displayName);
            $user->setCity($city);
            $user->setState($state);
            $user->setCountry($country);
            $user->setGender($gender);
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));

            $this->em->persist($user);
            $this->em->flush();

            return $user;
        }
    }

    public function loadUserByUsername($username)
    {
        // die('load by username: ' . $username);
        return new User(
            $username,
            null,
            // the roles for the user - you may choose to determine
            // these dynamically somehow based on the user
            array('ROLE_USER')
        );
    }

    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless

        $currentUser = $this->em->getRepository('AppBundle:User')->find($user->getId());
        return $currentUser;

        //return $this->find($user->getId());

        //throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}