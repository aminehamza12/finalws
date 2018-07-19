<?php

namespace AppBundle\Controller;

use AppBundle\Form\EmployerType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Employer;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EmployerController extends Controller
{

    /**
     * @Rest\View()
     * @Rest\Get("/employers/{id}")
     */
    public function getEmployerAction($id)
    {

            $employer = $this->getDoctrine()
                ->getRepository('AppBundle:Employer')
                ->find($id); // L'identifiant en tant que paramétre n'est plus nécessaire

            if (empty($employer)) {
                return \FOS\RestBundle\View\View::create(['message' => 'Employer introuvable'], Response::HTTP_NOT_FOUND);
            }

        /* @var $employer Employer[] */

        return $employer;
    }
    /**
     * @Rest\View()
     * @Rest\Get("/employers/")
     */
    public function getEmployersAction()
    {
            $places = $this->getDoctrine()
                ->getRepository('AppBundle:Employer')
                ->findAll();

        if (empty($places)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Employer introuvable'], Response::HTTP_NOT_FOUND);
        }

        /* @var $places Employer[] */

        return $places;
    }
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/employers/")
     */
    public function postEmployerAction(Request $request)
    {

        $employer = new Employer();
        $employer->setNom($request->get("nom"))
            ->setPrenom($request->get("prenom"))
            ->setDateRec(new \DateTime($request->get("date_rec")))
            ->setJob($request->get("job"))
            ->setSalaire($request->get("salaire"))
            ->setNemp($request->get("nemp"));

            $file=$request->files->get("image");

            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('image_directory'),$fileName);

            $employer->setImage($fileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($employer);
            $em->flush();
            return $employer;

    }
    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/employers/{id}")
     */
    public function removeEmployerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $employer = $em->getRepository('AppBundle:Employer')
            ->find($request->get('id'));
        /* @var $employer Employer */
        if ($employer) {
            $em->remove($employer);
            $em->flush();
            return \FOS\RestBundle\View\View::create(['message' => 'Employer supprimer'],200);
        }
        return \FOS\RestBundle\View\View::create(['message' => 'Employer introuvable'], Response::HTTP_NOT_FOUND);
    }
    /**
     * @Rest\View()
     * @Rest\Put("/employers/{id}")
     */
    public function updateEmployerAction(Request $request)
    {
        $employer = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Employer')
            ->find($request->get('id'));
        /* @var $employer Employer */

        if (empty($employer)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Employer non trouver'], Response::HTTP_NOT_FOUND);
        }
        $employer->setNom($request->get("nom"))
            ->setPrenom($request->get("prenom"))
            ->setDateRec(new \DateTime($request->get("date_rec")))
            ->setJob($request->get("job"))
            ->setSalaire($request->get("salaire"))
            ->setNemp($request->get("nemp"));


            $em = $this->getDoctrine()->getManager();
            $em->merge($employer);
            $em->flush();
            return $employer;

    }

}
