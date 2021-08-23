<?php

namespace App\Controller;

use App\Entity\PersonalInformations;
use App\Entity\PIReply;
use App\Entity\Quiz;
use App\Entity\Result;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class ResultController extends AbstractController
{
    /**
     * @Route("/result", name="result")
     */
    public function index(): Response
    {
        return $this->render('result/index.html.twig', [
            'controller_name' => 'ResultController',
        ]);
    }
    /**
     * @Route("/DisplayResults", name="DisplayResults")
     */
    public function DisplayResults(Request $request,SessionInterface $session,PaginatorInterface $paginator): Response
    {   if(is_null($session->get('user'))){
        return $this->redirectToRoute('connection');
    }

        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->findby(['user'=>$user,'public'=>true,]);
        $Pi=$this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$Quiz]);
        $PiReply=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['PI'=>$Pi]);



        $Results=array();
        foreach ($PiReply as $p)
        {if($p->getResult()!=null){array_push($Results,$p->getResult());}}

        $Results=array_unique($Results,SORT_REGULAR);
        $Results=array_values($Results);
        $Results = $paginator->paginate($Results,$request->query->getInt('page', 1),3);

        return $this->render('result/displayAllResults.html.twig', [
            'Results' => $Results,'user'=>$user,
        ]);
    }
    /**
     * @Route("/DisplayRecentResults", name="DisplayRecentResults")
     */
    public function DisplayRecentResults(SessionInterface $session,Request $request,PaginatorInterface $paginator): Response
    {   if(is_null($session->get('user'))){
        return $this->redirectToRoute('connection');
    }



        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->findby(['user'=>$user,'public'=>true,]);
        $Pi=$this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$Quiz]);
        $PiReply=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['PI'=>$Pi]);




        $Results=array();
        foreach ($PiReply as $p)
        {if($p->getResult()!=null){array_push($Results,$p->getResult());}}

        $Results=array_unique($Results,SORT_REGULAR);
        $Results=array_values($Results);
        foreach ($Results as $R)
        {

            foreach ($R->getPIReplies() as $r)
            {if($r->getPI()->getInformation()=="testDate")
            {$testdate=$r->getReply();}}

            if( $testdate<date("Y-m-d",strtotime("-1 week")))
            {
              $key=array_search( $R,$Results);
              unset($Results[$key]);
            }
        }


        $Results=array_values($Results);
        $Results = $paginator->paginate($Results,$request->query->getInt('page', 1),3);

        return $this->render('result/displayAllResults.html.twig', [
            'Results' => $Results,'user'=>$user,
        ]);
    }
    /**
     * @Route("/DisplayQuizResults{id}", name="DisplayQuizResults")
     */
    public function DisplayQuizResults($id,SessionInterface $session,Request $request,PaginatorInterface $paginator): Response
    {    if(is_null($session->get('user'))){
        return $this->redirectToRoute('connection');
    }

        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->find($id);
        $Pi=$this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$Quiz]);
        $PiReply=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['PI'=>$Pi]);



        $Results=array();
        foreach ($PiReply as $p)
        {if($p->getResult()!=null){array_push($Results,$p->getResult());}}

        $Results=array_unique($Results,SORT_REGULAR);
        $Results=array_values($Results);
        $Results = $paginator->paginate($Results,$request->query->getInt('page', 1),3);
        return $this->render('result/displayAllResults.html.twig', [
            'Results' => $Results,'user'=>$user,
        ]);
    }
    /**
     * @Route("/DisplayCatResults{id}", name="DisplayCatResults")
     */
    public function DisplayCatResults($id,SessionInterface $session,Request $request,PaginatorInterface $paginator): Response
    {   if(is_null($session->get('user'))){
        return $this->redirectToRoute('connection');
    }

        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->findby(['Category'=>$id]);
        $Pi=$this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$Quiz]);
        $PiReply=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['PI'=>$Pi]);



        $Results=array();
        foreach ($PiReply as $p)
        {if($p->getResult()!=null){array_push($Results,$p->getResult());}}

        $Results=array_unique($Results,SORT_REGULAR);
        $Results=array_values($Results);
        $Results = $paginator->paginate($Results,$request->query->getInt('page', 1),3);
        return $this->render('result/displayAllResults.html.twig', [
            'Results' => $Results,'user'=>$user,
        ]);
    }

}
