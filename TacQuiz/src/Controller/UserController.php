<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\ConnectionType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/connection", name="connection")
     */
    public function connection(Request $request,SessionInterface $session): Response
    {
        $form = $this->createForm(ConnectionType::class);
        $form->add('Connect', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

        $userFind=$this->getDoctrine()->getRepository(User::class)->findOneBy(['email'=>$form['Email']->getData(),]);
            if ($userFind!=null){

                if($userFind->getPassword() ==$form['Password']->getData() )
                {   $session->set('user', $userFind);

                return $this->redirectToRoute('Home');}
                else   {return $this->render('user/connectionError.html.twig', [ 'message'=>'false Password',
                    'form' =>$form->createView(),
                ]);}
            }
         else {   return $this->render('user/connectionError.html.twig', [ 'message'=>'false Email',
             'form' =>$form->createView(),
         ]);}
        }

        return $this->render('user/connection.html.twig', [
            'form' =>$form->createView(),
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(SessionInterface $session): Response
    {
        $session->set('user',null);
        return $this->redirectToRoute('connection');
    }


}
