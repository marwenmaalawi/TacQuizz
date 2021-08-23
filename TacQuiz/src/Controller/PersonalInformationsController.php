<?php

namespace App\Controller;

use App\Entity\PersonalInformations;
use App\Entity\Quiz;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PIType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PersonalInformationsController extends AbstractController
{
    /**
     * @Route("/personal/informations", name="personal_informations")
     */
    public function index(): Response
    {
        return $this->render('personal_informations/index.html.twig', [
            'controller_name' => 'PersonalInformationsController',
        ]);
    }

    /**
     * @Route("/DisplayPersonalInfo/{idquiz}", name="DisplayPersonalInfo")
     */
    public function DisplayPersonalInfo(Request $request,$idquiz, SessionInterface $session)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');
        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $Quiz = $this->getDoctrine()->getRepository(Quiz::class)->find($idquiz);
        $PI = $this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$Quiz]);
        $newPI=new PersonalInformations();
        $addform = $this->createForm(PIType::class, $newPI);
        $addform->add('Submit', SubmitType::class);
        $addform->handleRequest($request);
        if ($addform->isSubmitted() ) {
            $newPI->setQuiz($Quiz);
            $newPI->setType($addform['type']->getData());
            $em = $this->getDoctrine()->getManager();
            $em->persist($newPI);
            $em->flush();
            return $this->redirectToRoute('DisplayPersonalInfo',['user'=>$user,'idquiz'=>$idquiz,]);
        }

        return $this->render('personal_informations/displayPI.html.twig', ['addform'=>$addform->createView(),'quiz' => $Quiz,'PI'=>$PI, 'user'=>$user,]);
    }

    /**
     * @param $id
     * @param SessionInterface $session
     * @param $idquiz
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/DeletePI/{id}/{idquiz}", name="DeletePI")
     */
    public function DeletePI($id, SessionInterface $session,$idquiz)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $PIFind = $this->getDoctrine()->getRepository(PersonalInformations::class)->find($id);


        $em = $this->getDoctrine()->getManager();
        $em->remove($PIFind);
        $em->flush();
        return $this->redirectToRoute('DisplayPersonalInfo',['user'=>$user,'idquiz'=>$idquiz,]);
    }

    /**
     * @Route("/EditPI", name="EditPI")
     */
    public function EditPI(Request $request, SessionInterface $session): Response
    {

        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $optionEdit=$request->get('OptionEdit');



        $inputEdit=$request->get('InputEdit');
        $Id=$request->get('InputId');
        $PIFind=$this->getDoctrine()->getRepository(PersonalInformations::class)->find($Id);
        $idquiz=$PIFind->getQuiz()->getId();
        if($inputEdit==null ){
         return $this->redirectToRoute('DisplayPersonalInfo',['user'=>$user,'idquiz'=>$idquiz,]);
       }else{

            $PIFind->setInformation($inputEdit);
            $PIFind->setType($optionEdit);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        return $this->redirectToRoute('DisplayPersonalInfo',['user'=>$user,'idquiz'=>$idquiz,]);
        }
    }
}
