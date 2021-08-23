<?php

namespace App\Controller;

use App\Entity\PersonalInformations;
use App\Entity\PIReply;
use App\Entity\Quiz;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PIReplyType;
use App\Form\ReplypiType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use	Symfony\Component\Validator\Constraints\DateTime;

class PIReplyController extends AbstractController
{
    /**
     * @Route("/p/i/reply", name="p_i_reply")
     */
    public function index(): Response
    {
        return $this->render('pi_reply/index.html.twig', [
            'controller_name' => 'PIReplyController',
        ]);
    }

    /**
     * @Route ("/QuizPI/{id}",name="QuizPI")
     */
  public function displayPI(Request $request,$id,SessionInterface $sessionQuiz):Response
  {
      $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->find($id);
      $PI=$this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$id]);

      $addPIform= $this->createForm(ReplypiType::class);
      if ($Quiz!=null and $Quiz->getPublic()==true){
      foreach  ($PI as $P )
      {
          if($P->getType()=="text") {$addPIform->add($P->getInformation());}
       if($P->getType()=="image") {$addPIform->add($P->getInformation(),FileType::class, [
           'mapped' => false,
           'required' => false,
       ]);}
       if(($P->getType()=="date") and ($P->getInformation() != "testDate") and ($P->getInformation() != "duration")) {$addPIform->add($P->getInformation(), DateType::class);
          }
       if($P->getType()=="bool") {$addPIform->add($P->getInformation(), CheckboxType::class, [
           'label_attr' => ['class' => 'switch-custom'],'required' => false,
       ]);}
       if($P->getInformation() == "testDate") {$addPIform->add($P->getInformation(), HiddenType::class);}
          if($P->getInformation() == "duration") {$addPIform->add($P->getInformation(), HiddenType::class);}

      };
      $idParticipator=uniqid().'Part'.uniqid();
      $addPIform->add('Submit', SubmitType::class);
      $addPIform->handleRequest($request);

      if ($addPIform->isSubmitted() ){

      foreach  ($PI as $P )

      {if(($addPIform[$P->getInformation()]->getdata()==null)and ($P->getInformation() != "testDate") and ($P->getInformation() != "duration") )
        {
            $this->addFlash('success','Please complete all fields ');
            break;
        }else {
          $newReply = new PIReply();
          $newReply->setCryptedId($idParticipator);
          $newReply->setPI($P);

          if ($P->getType() == "image") {
              /** @var UploadedFile $uploadedFile */
              $uploadedFile = $addPIform[$P->getInformation()]->getdata();
              if ($uploadedFile) {
                  $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
                  $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                  $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                  $uploadedFile->move(
                      $destination,
                      $newFilename
                  );
                  $newReply->setReply($newFilename);
              }

          } elseif (($P->getType() == "date" ) and ($P->getInformation() != "testDate") and ($P->getInformation() != "duration")) {
              $newFormat = $addPIform->get($P->getInformation())->getdata()->format("Y/m/d");

              $newReply->setReply($newFormat);
          } elseif($P->getInformation() == "testDate")
          {   $today = date("Y-m-d");
              $newReply->setReply($today);
          }
          elseif($P->getInformation() == "duration")
          {
              $newReply->setReply(null);
          }
          else {
              $newReply->setReply($addPIform->get($P->getInformation())->getdata());
          }
          $sessionQuiz->set('timebegin',date('Y-m-d H:i:s'));
          $sessionQuiz->set('userQuiz',$idParticipator);
          $sessionQuiz->set('Quiz',$id);
          $em = $this->getDoctrine()->getManager();
          $em->persist($newReply);
          $em->flush();

      }}  return  $this->redirectToRoute('QuizFront',['id'=>$id,]);}
      return $this->render('pi_reply/displayPIReply.html.twig', ['form' => $addPIform->createView(), 'quiz'=>$Quiz, 'PI'=>$PI,]);
      }else {
          return $this->render('pi_reply/404.html.twig');

      }
  }

}
