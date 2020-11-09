<?php

// Change the namespace according to your project.
namespace AppBundle\Entity;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;


// Source: https://gist.github.com/danvbe/4476697
class FOSUBUserProvider extends BaseClass
{
    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        $service = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';
        //On déconnecte le précedent utilisateur
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }
        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }


    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $property = $this->getProperty($response);

        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        $email = $response->getEmail();
        // check if we already have this user
        $existing = $this->userManager->findUserBy(array('email' => $email));
        if ($existing instanceof User) {
            // in case of Facebook login, update the facebook_id
            if ($property == "facebookId") {
                $existing->setFacebookId($username);
            }
            // in case of Google login, update the google_id
            if ($property == "googleId") {
                $existing->setGoogleId($username);
            }
            $this->userManager->updateUser($existing);

            return $existing;
        }

        // if we don't know the user, create it
        if (null === $user || null === $username) {
            /** @var User $user */
            $user = $this->userManager->createUser();
            $user->setLastLogin(new \DateTime());
            $user->setEnabled(true);

            $user->setUsername($response->getFirstName() . $response->getLastName());
            $user->setUsernameCanonical($response->getFirstName() . $response->getLastName());
            $user->setPassword(sha1(uniqid()));
            $user->addRole('ROLE_USER');
            $user->setImageName($response->getProfilePicture());

            if ($property == "facebookId") {
                $user->setFacebookId($username);
            }
            if ($property == "googleId") {
                $user->setGoogleId($username);
            }
        }

        $user->setEmail($response->getEmail());
        $user->setNom($response->getFirstName());
        $user->setPrenom($response->getLastName());

        $this->userManager->updateUser($user);

        return $user;
    }

}