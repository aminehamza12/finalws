<?php

namespace App\Controller;

use App\Form\EmployerType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Employer;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Validate;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Form\Form;

class RestController extends Controller
{
    /**
     * @Route("/B&D/{id}",name="Get_employer")
     * @Method({"GET"})
     */
    public function Getemployer($id = null)
    {
        if ($id != null)
            $post = $this->getDoctrine()->getRepository(Employer::class)->find($id);
        else
            $post = $this->getDoctrine()->getRepository(Employer::class)->findAll();

        if (empty($post)) {
            $response = array(
                'code' => 1,
                'message' => 'employer introuvable',
                'error' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $data = $this->get('jms_serializer')->serialize($post, 'json');

        $response = array(
            'code' => 0,
            'message' => 'employer trouver',
            'errors' => null,
            'result' => json_decode($data)
        );
        return new JsonResponse($response, 200);
    }

    /**
     * @Get("/places")
     */
    public function getPlacesAction()
    {
        $places = $this->get('doctrine.orm.entity_manager')
            ->getRepository(Employer::class)
            ->findAll();
        /* @var $places Place[] */

        $formatted = [];
        foreach ($places as $place) {
            $formatted[] = [
                'id' => $place->getId(),
                'nom'=> $place->getNom(),
                'prenom'=> $place->getPrenom()
            ];
        }

        // Récupération du view handler
        $viewHandler = $this->get('fos_rest.view_handler');

        // Création d'une vue FOSRestBundle
        $view = View::create($formatted);
        $view->setFormat('json');

        // Gestion de la réponse
        return $viewHandler->handle($view);
    }
//    /**
//     * @Rest\View()
//     * @Rest\Post("/places")
//     */
//    public function postPlacesAction(Request $request)
//    {
//        return [
//            'payload' => [
//                $request->get('name'),
//                $request->get('address')
//            ]
//        ];
//    }

/**
 * @Route ("/{id}")
 * @Method("DELETE")
 */
 public function deleteAction(Employer $id)
 {
     $doctrine=$this->getDoctrine()->getManager();
     $doctrine->remove($id);
     $doctrine->flush();
     return new JsonResponse("employer supprimer",200);
 }
    /**
     * @Route ("/B&D")
     * @Method("POST")
     */
    public function creeemployer(Request $request)
    {
        $data=$request->getContent();

        $employer=new Employer();

        $employer->setNom($request->get("nom"))
            ->setPrenom($request->get("prenom"))
            ->setDateRec(new \DateTime($request->get("date_rec")))
            ->setJob($request->get("job"))
            ->setSalaire($request->get("salaire"))
            ->setNemp($request->get("nemp"));


        $doctrine = $this->getDoctrine()->getManager();

        $doctrine->persist($employer);
        $doctrine->flush();

        return new JsonResponse('success',200);
    }

    /**
     * @param Request $request
     * @param $id
     * @param Validate $validate
     * @Route ("/B&D/{id}",name="update_employer")
     * @Method({"PUT"})
     * @return JsonResponse
     */
public function UpdateEmployer(Request $request,$id,Validate $validate)
{
    $post=$this->getDoctrine()->getRepository(Employer::class)->find($id);
    if(empty($post))
    {
        $response = array(
            'code' => 0,
            'message' => 'error update',
            'errors' => null,
            'result' => null
        );
        return new JsonResponse($response, Response::HTTP_NOT_FOUND);
    }
    $body=$request->getContent();

    $data=$this->get('jms_serializer')->deserialize($body,'App\Entity\Employer','json');

    $response=$validate->validateRequest($data);

    if(!empty($response))
    {
        return new JsonResponse($response,Response::HTTP_BAD_REQUEST);

    }
    $post->setNom($data->getNom());
        $post->setPrenom($data->getPrenom());
        $post->setNemp($data->getNemp());
        $post->setJob($data->getJob());
        $post->setSalaire($data->getSalaire());
        $post->setDateRec($data->getDateRec());

        $em=$this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

    $response = array(
        'code' => 0,
        'message' => 'Post updated',
        'errors' => null,
        'result' => null
    );

return new JsonResponse($response,200);

}




//    /**
//     * @Post("/B&D")
//     */
//
//    public function postArticleAction(Request $request)
//    {
//        $post = new Employer();
//        $post->setNom($request->get("nom"))
//            ->setPrenom($request->get("prenom"))
//            ->setDateRec($request->get("date_rec"))
//            ->setJob($request->get("job"))
//            ->setSalaire($request->get("salaire"))
//            ->setNemp($request->get("nemp"));
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($post);
//        $em->flush();
//        $response = array(
//            'code' => 0,
//            'message' => 'employer trouver',
//            'errors' => null,
//            'result' => null
//        );
//        return new JsonResponse($response, 200);
//
//    }




//    /**
//     * @param Request $request
//     * @param  string $id
//     * @Route("/B&D/{id}",name="update_employer")
//     * @Method ({"PUT"})
//     * @return view
//     */
//    public function putAction(Request $request, string $id)
//    {
//        $existingEmployer = $this->findEmployerById($id);
//
//        $form = $this->createForm(Employer::class, $existingEmployer);
//
//        $form->submit($request->request->all());
//
//        if (false === $form->isValid()) {
//            return $this->view($form);
//        }
//
//        $this->entityManager->flush();
//
//        return $this->view(null, Response::HTTP_NO_CONTENT);
//    }





//    /**
//     * @param string $id
//     * @return Employer
//     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
//     */
//    private function findEmployerById(string $id)
//    {
//        $existingEmployer = $this->getDoctrine()->getRepository(Employer::class)->find($id);
//
//        if (null === $existingEmployer) {
//            throw new NotFoundHttpException();
//        }
//
//        return $existingEmployer;
//    }




    /**
     * @Route("/",name="update_employer")
     * @Method({"PUT"})
     */
    public function updatePost(Request $request)
    {

        $data=$request->getContent();
        parse_str($data,$data_arr);
        $post = $this->getDoctrine()->getRepository(Employer::class)->find($data_arr['id']);
        if (!$post) {
            $response = array(
                'code' => 1,
                'message' => 'employer introuvable',
                'error' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $data = $this->get('jms_serializer')->deserialize($post, 'json');

        $post->setNom($data->getNom());
        $post->setPrenom($data->getPrenom());
        $post->setNemp($data->getNemp());
        $post->setJob($data->getJob());
        $post->setSalaire($data->getSalaire());
        $post->setDateRec($data->getDateRec());

        $em = $this->getDoctrine()->getManager();
        $em->merge($post);
        $em->flush();
        $response = array(
            'code' => 0,
            'message' => 'mise a jour employer',
            'errors' => null,
            'result' => null
        );
        return new JsonResponse($response, 200);

    }



//    /**
//     * @Param Request $request
//     * @Param Validate $validate
//     * @return JsonResponse
//     * @Route("/B&D",name="create_post")
//     * @Method({"POST"})
//     */
//
//    public  function createPost(Request $request,Validate $validate)
//    {
//        $data=$request->getContent();
//
//        $post=$this->get('jms_serializer')->deserialize($data,Employer::class,'json');
//
//        $response =$validate->validateRequest($post);
//
//        if (!empty($response))
//        {
//            return new JsonResponse($response,Response::HTTP_BAD_REQUEST);
//        }
//
//        $em=$this->getDoctrine()->getManager();
//        $em->persist($post);
//        $em->flush();
//
//        $response=array(
//
//            'code'=>0,
//            'message'=>'Post created',
//            'errors'=>null,
//            'result'=>null
//        );
//        return new JsonResponse($response, 200);
//    }




//    /**
//     * @Rest\View(statusCode=Response::HTTP_CREATED)
//     * @Rest\Post("/places")
//     */
//    public function postPlacesAction(Request $request)
//    {
//        $place = new Employer();
//        $place->setNom($request->get("nom"))
//            ->setPrenom($request->get("prenom"))
//            ->setDateRec($request->get("date_rec"))
//            ->setJob($request->get("job"))
//            ->setSalaire($request->get("salaire"))
//            ->setNemp($request->get("nemp"));
//
//        $em = $this->get('doctrine.orm.entity_manager');
//        $em->persist($place);
//        $em->flush();
//
//        return $place;
//    }
}
