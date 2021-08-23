<?php

namespace App\Controller;

use App\Entity\PIReply;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\User;
use App\Form\QuizType;
use App\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/question", name="question")
     */
    public function index(): Response
    {
        return $this->render('question/index.html.twig', [
            'controller_name' => 'QuestionController',
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @Route("/DisplayQues/{id}", name="DisplayQues")
     */
    public function DisplayQuestions($id,Request $request,SessionInterface $session): Response
    {   if(is_null($session->get('user'))){
        return $this->redirectToRoute('connection');
    }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $Question= $this->getDoctrine()->getRepository(Question::class)->findBy(['quiz'=>$id]);

        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->find($id);
        $PI=$Quiz->getPersonalInformations()->count();

        $newquestion = new Question();
        $addform = $this->createForm(QuestionType::class, $newquestion);
        $addform->add('Submit', SubmitType::class);
        $addform->handleRequest($request);
        if ($addform->isSubmitted() ) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $addform['imageLink']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $newquestion->setImageLink($newFilename);
            }


            $newquestion->setAnswerType($addform['answerType']->getData());

            $newquestion->setQuiz($Quiz);
            $em = $this->getDoctrine()->getManager();
            $em->persist($newquestion);
            $em->flush();
            return $this->redirectToRoute('DisplayQues',['user'=>$user,'id'=>$id,]);
        }

        return $this->render('question/displayQuestions.html.twig', ['quiz'=>$Quiz,'question'=>$Question,'PI'=>$PI, 'user'=>$user, 'addform' => $addform->createView() ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route ("/EditQues/{id}", name="EditQues")
     */
    public function updateQuestion(Request $request,$id,SessionInterface $session){
        if(is_null($session->get('user'))){
            return $this->redirectToRoute('connection');
        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $questionEdited=$this->getDoctrine()->getRepository(Question::class)->find($id);
        $type=$questionEdited->getAnswerType();
        $editform = $this->createForm(QuestionType::class, $questionEdited);
        $editform->add('Edit', SubmitType::class);
        $editform->handleRequest($request);
        if($editform->isSubmitted()){
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $editform['imageLink']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $questionEdited->setImageLink($newFilename);}
            $questionEdited->setAnswerType($type);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('DisplayQues',['user'=>$user,'id'=>$questionEdited->getQuiz()->getId(),]);

        }
        $template = $this->render('question/Editform.html.twig',['editform'=>$editform->createView(),'id'=>$id])->getContent();
        $response = new JsonResponse();
        $response->setStatusCode(200);
        return $response->setData(['template' => $template ]);
    }
    /**
     * @param $id
     * @param SessionInterface $session
     * @param $idquiz
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/DeleteQuestion/{id}/{idquiz}" , name="DeleteQuestion")
     */
    public function DeleteQuestion($id, SessionInterface $session,$idquiz)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $QuestionFind = $this->getDoctrine()->getRepository(Question::class)->find($id);


        $em = $this->getDoctrine()->getManager();
        $em->remove($QuestionFind);
        $em->flush();
        return $this->redirectToRoute('DisplayQues',['user'=>$user, 'id'=>$idquiz,]);
    }


    /**
     * @param $id
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @Route("/QuizFront/{id}", name="QuizFront")
     */
    public function DisplayQuesFront($id,Request $request,SessionInterface $sessionQuiz): Response
    {   if(date('Y-m-d H:i:s')>date('Y-m-d H:i:s',strtotime(' +2 minutes',strtotime($sessionQuiz->get('timebegin'))))){
        $UserQuizFind = $this->getDoctrine()->getRepository(PIReply::class)->findBy(['cryptedId'=>$sessionQuiz->get('userQuiz')]);
        $em = $this->getDoctrine()->getManager();
        foreach ($UserQuizFind as $u)
        {$em->remove($u);}
        $em->flush();
        $sessionQuiz->set('userQuiz',null);
        $sessionQuiz->set('timebegin',null);
    }
        if(is_null($sessionQuiz->get('userQuiz'))){
        return $this->redirectToRoute('QuizPI',['id'=>$id]);
    }


        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->find($id);
        $sessionQuiz->set('Quizid',$id);
        $Question= $this->getDoctrine()->getRepository(Question::class)->findBy(['quiz'=>$Quiz]);
         $i=0;
        if ( $Quiz->getRandom()==true)
        {

            shuffle($Question);
        }



        return $this->render('question/displayQuestionsFront.html.twig', ['quiz'=>$Quiz,'question'=>$Question, ]);

    }
    /**
     * @param $id
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @Route("/emptytest", name="emptytest")
     */
    public function emptytest(Request $request,SessionInterface $sessionQuiz): Response
    {
        $UserQuizFind = $this->getDoctrine()->getRepository(PIReply::class)->findBy(['cryptedId'=>$sessionQuiz->get('userQuiz')]);
        $em = $this->getDoctrine()->getManager();
        foreach ($UserQuizFind as $u)
        {$em->remove($u);}
        $em->flush();
        $sessionQuiz->set('userQuiz',null);
        $sessionQuiz->set('timebegin',null);
        $id=$sessionQuiz->get('Quizid');


        return $this->redirectToRoute('QuizPI',['id'=>$id]);
    }


}
