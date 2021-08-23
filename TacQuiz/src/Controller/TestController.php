<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime ;

class TestController extends AbstractController
{
    /**
     * @Route("/Home", name="Home")
     */
    public function HomeNavigation(SessionInterface $session,SessionInterface $sessionQuiz): Response
    {if(is_null($session->get('user'))){
        return $this->redirectToRoute('connection');
    }

    $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        return $this->render('Home.html.twig', [
            'controller_name' => 'TestController',
            'user'=>$user,   ]);
    }

}
