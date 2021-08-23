<?php

namespace App\Controller;

use App\Entity\Choices;
use App\Entity\PersonalInformations;
use App\Entity\PIReply;
use App\Entity\Quiz;
use App\Entity\Reply;

use App\Entity\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ReplyController extends AbstractController
{
    /**
     * @Route("/reply", name="reply")
     */
    public function index(): Response
    {
        return $this->render('reply/index.html.twig', [
            'controller_name' => 'ReplyController',
        ]);
    }
    /**
     * @Route("/AddReply/{id}/{check}/{end}", name="addreply")
     */
    public function addreply($id,SessionInterface $sessionQuiz,$check,$end): Response
    {    if(is_null($sessionQuiz->get('userQuiz'))){
        return $this->redirectToRoute('QuizPI',['id'=>$id]);
    }
        $Reply=new Reply();
       $ch=$this->getDoctrine()->getRepository(Choices::class)->find($id);
       $Reply->setReply($ch);
       $Reply->setCryptedId($sessionQuiz->get('userQuiz'));
       $this->getDoctrine()->getManager()->persist($Reply);
       $this->getDoctrine()->getManager()->flush();

       $response = new JsonResponse();
        $response->setStatusCode(200);
       if($check==$end-1){
           $response->setData(['etat'=>true]);
       }else{
           $response->setData(['etat'=>false]);

       }
        return $response;
    }
    /**
     * @Route("/endtest", name="endtest")
     */
    public function addResult(SessionInterface $sessionQuiz,MailerInterface $mailer): Response
    {

        if(is_null($sessionQuiz->get('userQuiz'))){
        return $this->redirectToRoute('QuizPI',['id'=>$sessionQuiz->get('Quiz')]);}
        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->find($sessionQuiz->get('Quiz'));
        $reply=$this->getDoctrine()->getRepository(Reply::class)->findBy(['cryptedId'=>$sessionQuiz->get('userQuiz')]);
        $datetime1 = strtotime(date('Y-m-d H:i:s'));
        $datetime2 = strtotime($sessionQuiz->get('timebegin'));

        $now=$datetime1-$datetime2;
        $now=date('H:i:s', $now);
        $PI=$this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$Quiz,'information'=>"duration"])[0];
        $PIReply=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['PI'=>$PI,'cryptedId'=>$sessionQuiz->get('userQuiz')])[0];
        $PIReply->setReply($now);
        $em=$this->getDoctrine()->getManager()->flush();
        $Replies=array();
        foreach ($reply as $r){
            array_push($Replies,$r->getReply()->getId());
        }
        $Questions=$Quiz->getQuestions();
        $S=0;
        foreach($Questions as $Q)
        {$choices=$Q->getChoices();
        $qs=true;
        foreach ($choices as $c )
         { $key=array_search($c->getId(),$Replies);
          if($key==0){
              $key++;
          }
         if (($c->getState()==true)&&(($key)==false))
         { $qs=false ;
         }
         else if(($key)&&($c->getState()==false))
         {$qs=false;
          }


        }
            if($qs==true)
            {$S=$S+1;}
        }
        $Score=$S*100/($Questions->count());

        $Result=new Result();
        $PIrep=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['cryptedId'=>$sessionQuiz->get('userQuiz')]);


        $Result->setResult($Score);
        foreach ($PIrep as $PIRe)
        {$Result->addPIReply($PIRe);}
        $Result->setQuiz($Quiz);

        $em=$this->getDoctrine()->getManager();
        $em->persist($Result);
        $em->flush();
        $Participator=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['cryptedId'=>$sessionQuiz->get('userQuiz'),]);
        $Participatormail="TacQuiz@gmail.com";
        foreach($Participator as $P)
        {if($P->getPI()->getInformation()=="email"){
            $Participatormail=$P->getReply();}}
        $text1="You will find below the the Result of a new submitter of {$Quiz->getTitle()} in the category {$Quiz->getCategory()->getTitle()},  ";
        $text2="";

        foreach ($Participator as $P )
        {$text2=$text2."{$P->getPI()->getInformation()} : {$P->getReply()} ";

        }
            if($Quiz->getPublicResult())
        {$email = (new Email())
            ->from('TacQuiz@gmail.com')
            ->to("{$Quiz->getUser()->getEmail()}")
            ->subject('TacQuiz')
            ->text("{$text1} {$text2} Score: {$Score}% ")
            ->html("<h2>{$text1} {$text2} Score: {$Score}% </h2>");

            $mailer->send($email);
            $email = (new Email())
                ->from('TacQuiz@gmail.com')
                ->to("{$Participatormail}")
                ->subject('TacQuiz')
                ->text("Hello ! You will find below the the Result of  {$Quiz->getTitle()}  : {$Score} % ")
                ->html("<h2>Hello ! You will find below the the Result of  {$Quiz->getTitle()}  : {$Score} %</h2>");
            $mailer->send($email);

        }else {$email = (new Email())
            ->from('TacQuiz@gmail.com')
            ->to("{$Quiz->getUser()->getEmail()}")
            ->subject('TacQuiz')
            ->text("{$text1} {$text2} Score: {$Score}% ")
            ->html("<h2>{$text1} {$text2} Score: {$Score}% </h2>");


            $mailer->send($email);}
            $sessionQuiz->set('userQuiz',null);

        return $this->render('reply/endtest.html.twig', [
            'controller_name' => 'ReplyController',
        ]);
    }
}
