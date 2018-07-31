<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use AppBundle\Form\CredentialsType;
use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;

class AuthTokenController extends Controller
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/auth-tokens")
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);
        $credentials->setLogin($request->get('login'));
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:utilisateur')
            ->findOneByEmail($credentials->getLogin());

        if (!$user) { // L'utilisateur n'existe pas
            return $this->invalidCredentials();
        }

       $encoder = $this->get('security.password_encoder');
       $isPasswordValid = $em->getRepository('AppBundle:utilisateur')
           ->findOneByplainPassword($credentials->getPassword());

        if (!$isPasswordValid) { // Le mot de passe n'est pas correct
            return $this->invalidCredentials();
        }

        $authToken = new AuthToken();
        $authToken->setValeur(base64_encode(random_bytes(50)))
                  ->setDate(new \DateTime('now'))
                  ->setUtilisateur($user);

        $em->persist($authToken);
        $em->flush();

        return $authToken->getValeur();
    }

    private function invalidCredentials()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Invalid credentials'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/auth-tokens/{id}")
     */
    public function removeAuthTokenAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $AuthToken = $em->getRepository('AppBundle:AuthToken')
            ->findOneByValeur($request->get('id'));
        /* @var $AuthToken AuthToken */
        if ($AuthToken) {
            $em->remove($AuthToken);
            $em->flush();
            return \FOS\RestBundle\View\View::create(['message' => 'Déconnecter'],200);
        }
        return \FOS\RestBundle\View\View::create(['message' => 'Réessayer'], Response::HTTP_NOT_FOUND);
    }
}
