<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\PersonalInformations;
use App\Entity\User;
use App\Entity\Quiz;
use App\Form\CategoryType;
use App\Form\QuizTimerType;
use App\Form\QuizType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    /**
     * @Route("/quiz", name="quiz")
     */
    public function index(SessionInterface $session): Response
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        return $this->render('quiz/index.html.twig', [
            'controller_name' => 'QuizController',
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/DisplayQuiz", name="DisplayQuiz")
     */
    public function DisplayQuiz( SessionInterface $session)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $Quiz = $this->getDoctrine()->getRepository(Quiz::class)->findAll();
        return $this->render('quiz/displayAllQuiz.html.twig', ['Quiz' => $Quiz, 'user'=>$user,]);
    }

    /**
     * @param $id
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @Route ("/DisplayQuizCat/{id}",name="DisplayQuizCat")
     */

    public function DisplayQuizCat($id,Request $request,SessionInterface $session): Response
    {   if(is_null($session->get('user'))){
        return $this->redirectToRoute('connection');
    }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $cat= $this->getDoctrine()->getRepository(Category::class)->find($id);
        $Quizfind = $this->getDoctrine()->getRepository(Quiz::class)->findBy(array('Category'=>$cat));

        return $this->render('quiz/displayQuizCat.html.twig', ['Quiz' => $Quizfind, 'user'=>$user,]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/Addquiz", name="Addquiz")
     */
    public function Addquiz(Request $request,SessionInterface $session)
    {
        $user = $session->get('user');
        if (is_null($user)) {
            return $this->redirectToRoute('connection');

        }

        $user = $this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $users = $this->getDoctrine()->getRepository(User::class)->find($user->getId());

        $Quiz = new Quiz();

        $Quiz->setUser($users);

        $form = $this->createForm(QuizType::class, $Quiz);


        $form->add('Submit', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $Quiz->setMinScore(($form['minScore']->getData())) ;
            $cat = $this->getDoctrine()->getRepository(Category::class)->find($form['Category']->getData()->getId());
            $quiz = $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(array('Title' => $form['Title']->getData(), 'Category' => $cat));

            if ($quiz != null) {
                $this->addFlash('success', 'This title already exists');
            } else {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $form['image']->getData();
                if ($uploadedFile) {
                    $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                    $uploadedFile->move(
                        $destination,
                        $newFilename
                    );
                    $Quiz->setImage($newFilename);
                    if ($Quiz->getTitle() == null) {

                        $this->addFlash('success', 'Please complete all fields ');
                    } else {

                        if($Quiz->getTimer()==true)
                        {if((is_numeric($form['Quiztime']->getData())and($form['Quiztime']->getData()!=null) )) {
                            $Quiz->setQuiztime($form['Quiztime']->getData());
                            $Cat = $Quiz->getCategory()->getId();

                            $Quiz->setPublic(false);
                            $newPI = new PersonalInformations();
                            $newPII = new PersonalInformations();
                            $newPIII = new PersonalInformations();
                            $newPI->setQuiz($Quiz);
                            $newPI->setType("text");
                            $newPI->setInformation("email");
                            $newPII->setType("date");
                            $newPII->setInformation("testDate");
                            $newPIII->setQuiz($Quiz);
                            $newPIII->setType("date");
                            $newPIII->setInformation("duration");
                            $newPII->setQuiz($Quiz);
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($Quiz);
                            $em->flush();
                            $eme = $this->getDoctrine()->getManager();
                            $eme->persist($newPI);
                            $eme->flush();
                            $emee = $this->getDoctrine()->getManager();
                            $emee->persist($newPII);
                            $emee->flush();
                            $emeee = $this->getDoctrine()->getManager();
                            $emeee->persist($newPIII);
                            $emeee->flush();
                            return $this->redirectToRoute('DisplayQuizCat', ['id' => $Cat, 'user' => $user,]);
                        }else {$this->addFlash('success', 'Quiztime should be integer of minutes');}}
                        else{
                            $Cat = $Quiz->getCategory()->getId();

                            $Quiz->setPublic(false);
                            $newPI = new PersonalInformations();
                            $newPII = new PersonalInformations();
                            $newPI->setQuiz($Quiz);
                            $newPI->setType("text");
                            $newPI->setInformation("email");
                            $newPII->setType("date");
                            $newPII->setInformation("testDate");
                            $newPII->setQuiz($Quiz);

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($Quiz);
                            $em->flush();
                            $eme = $this->getDoctrine()->getManager();
                            $eme->persist($newPI);
                            $eme->flush();
                            $emee = $this->getDoctrine()->getManager();
                            $emee->persist($newPII);
                            $emee->flush();
                            return $this->redirectToRoute('DisplayQuizCat', ['id' => $Cat, 'user' => $user,]);}}
                } elseif ($uploadedFile == null) {
                    $this->addFlash('success', 'Please complete all fields');
                } elseif ($Quiz->getTitle() == null) {

                    $this->addFlash('success', 'Please complete all fields ');
                }
            }
        }
        return $this->render('quiz/addQuiz.html.twig', ['form' => $form->createView(), 'user' => $user,]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/DeleteQuiz/{id}" , name="DeleteQuiz")
     */
    public function DeleteQuiz($id, SessionInterface $session)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');
        }

        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $QuizFind = $this->getDoctrine()->getRepository(Quiz::class)->find($id);
        $idCat =$QuizFind->getCategory()->getId();
        $em = $this->getDoctrine()->getManager();
        $em->remove($QuizFind);
        $em->flush();
        if($user->getType()=="user")
        {return $this->redirectToRoute('DisplayQuizCat',['id'=>$idCat, 'user'=>$user,]);
        }else {
            return $this->redirectToRoute('DisplayQuiz');
        }
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/UpdateQuiz/{id}" , name="UpdateQuiz")
     */
    public function UpdateQuiz($id, Request $request, SessionInterface $session)
    {$user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');
        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $quizFind = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['id' => $id])[0];
        $quiztitle=$quizFind->getTitle();

        $form = $this->createForm(QuizType::class, $quizFind);
        $form->add('Update', SubmitType::class);
        $R=$quizFind->getRandom();

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $quizFind->setQuiztime($form['Quiztime']->getData());

            $cat=$this->getDoctrine()->getRepository(Category::class)->find($form['Category']->getData()->getId());
            $quiz= $this->getDoctrine()->getRepository(Quiz::class)->findOneBy(array('Title'=>$form['Title']->getData(),'Category'=>$cat));

            if ($quiz != null and $quiztitle!=$quiz->getTitle()){

                $this->addFlash('success', 'This title already exists');
            }
            else{
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['image']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                ); $quizFind->setImage($newFilename);
            }
            if ($quizFind->getTitle()==null) {
                $this->addFlash('success', 'Please complete all fields');


            }else{  if($quizFind->getTimer()==true)
            {if((is_numeric($form['Quiztime']->getData())and($form['Quiztime']->getData()!=null) )) {
                $quizFind->setQuiztime($form['Quiztime']->getData());
                $Cat=$quizFind->getCategory()->getId();
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                if($user->getType()=="user")
                {return $this->redirectToRoute('DisplayQuizCat',['id'=>$Cat, 'user'=>$user,]);
                }else {
                    return $this->redirectToRoute('DisplayQuiz');
                }

            }else {$this->addFlash('success', 'Quiztime should be integer of minutes');}}
            else {
                $Cat = $quizFind->getCategory()->getId();
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                if($user->getType()=="user")
                {return $this->redirectToRoute('DisplayQuizCat',['id'=>$Cat, 'user'=>$user,]);
                }else {
                    return $this->redirectToRoute('DisplayQuiz');
                }

            }            }}
        }
        return $this->render('Quiz/editQuiz.html.twig', ['form' => $form->createView(),'R'=>$R, 'user'=>$user,]);

    }

    /**
     * @param $idquiz
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @Route ("/Publish/{idquiz}",name="Publish")
     */

    public function Publish(MailerInterface $mailer,$idquiz,Request $request,SessionInterface $session): Response
    {   if(is_null($session->get('user'))){
        return $this->redirectToRoute('connection');
    }

        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $Quizfind = $this->getDoctrine()->getRepository(Quiz::class)->find($idquiz);
        $Cat=$Quizfind->getCategory();
         if ($user->getType()=="admin")
         {
            $email = (new Email())
                ->from('TacQuiz@gmail.com')
                ->to("{$Quizfind->getUser()->getEmail()}")
                ->subject('TacQuiz')
                ->text("Your quiz {$Quizfind->getTitle()} in the category {$Cat->getTitle()} is unpublished because it's against our rules. ")
                ->html("<h1>Your quiz  {$Quizfind->getTitle()} in the category {$Cat->getTitle()}  is unpublished because it's against our rules.</h1>");
            $mailer->send($email);
         }

        $public=$Quizfind->getPublic();

        if($public==true) {$Quizfind->setPublic(false);

            $em = $this->getDoctrine()->getManager();
            $em->flush();}
        else {$Quizfind->setPublic(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();}

        if($user->getType()=="user")
        {  $email = (new Email())
            ->from('TacQuiz@gmail.com')
            ->to("{$Quizfind->getUser()->getEmail()}")
            ->subject('TacQuiz')
            ->text("You will find below the link of your quiz {$Quizfind->getTitle()} in the category {$Cat->getTitle()} : http://127.0.0.1:8000/QuizFront/{$Quizfind->getId()} ")
            ->html("<h1>You will find below the link of your quiz {$Quizfind->getTitle()} in the category {$Cat->getTitle()} : http://127.0.0.1:8000/QuizFront/{$Quizfind->getId()}  </h1>");
            $mailer->send($email);

            return $this->redirectToRoute('DisplayQuizCat',['id'=>$Cat->getId(), 'user'=>$user,]);
        }else {
            return $this->redirectToRoute('DisplayQuiz');
        }
    }
    /**
     * @Route("/DisplayQuizResult", name="DisplayQuizResult")
     */
    public function DisplayQuizRes( SessionInterface $session)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $Quiz = $this->getDoctrine()->getRepository(Quiz::class)->findby(['user'=>$user]);

        return $this->render('quiz/displayQuizRes.html.twig', ['Quiz' => $Quiz, 'user'=>$user,]);
    }
    /**
     * @Route("/DisplayQuizStats", name="DisplayQuizStats")
     */
    public function DisplayQuizStats( SessionInterface $session)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $Quiz = $this->getDoctrine()->getRepository(Quiz::class)->findby(['user'=>$user,'public'=>true]);

        return $this->render('quiz/displayQuizStats.html.twig', ['Quiz' => $Quiz, 'user'=>$user,]);
    }

}