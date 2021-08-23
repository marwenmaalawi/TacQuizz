<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Choices;
use App\Entity\Quiz;
use App\Entity\User;
use App\Form\ChoicesImageType;
use App\Form\ChoicesTextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class ChoicesController extends AbstractController
{
    /**
     * @Route("/choices", name="choices")
     */
    public function index(): Response
    {
        return $this->render('choices/index.html.twig', [
            'controller_name' => 'ChoicesController',
        ]);
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @Route("/EditchoicesImage", name="EditchoicesImage")
     */
    public function EditImage(Request $request, SessionInterface $session): Response
    {   $newchoice= new Choices();
        $addform = $this->createForm(ChoicesImageType::class, $newchoice);
        $addform->add('Submit', SubmitType::class);


        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());


        $optionEdit=$request->get('OptionEdit');

        if ($optionEdit=="true"){

            $optionEdit=true;
        }
        else if ($optionEdit=="false") {
            $optionEdit=false;
        }


        $Id=$request->get('InputId');
        $choiceFind=$this->getDoctrine()->getRepository(Choices::class)->find($Id);
        $idQuestion=$choiceFind->getQuestion()->getId();
        $idquiz=$choiceFind->getQuestion()->getQuiz()->getId();
        $choices=$choiceFind->getQuestion()->getChoices();
        $Question=$choiceFind->getQuestion();

        $uploadedFile = $request->files->get('myfile');

        if ($uploadedFile) {
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $choiceFind->setChoice($newFilename);
        }


            $choiceFind->setState($optionEdit);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('DisplayChoices',['id'=>$idQuestion,'idquiz'=>$idquiz,]);


    }
    /**
     * @Route("/Editchoices", name="Editchoices")
     */
    public function Edit(Request $request, SessionInterface $session): Response
    {   $newchoice= new Choices();
        $addform = $this->createForm(ChoicesImageType::class, $newchoice);
        $addform->add('Submit', SubmitType::class);


        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $optionEdit=$request->get('OptionEdit');

        if ($optionEdit=="true"){

            $optionEdit=true;
        }
        else if ($optionEdit=="false") {
            $optionEdit=false;
        }

        $inputEdit=$request->get('InputEdit');
        $Id=$request->get('InputId');
        $choiceFind=$this->getDoctrine()->getRepository(Choices::class)->find($Id);
        $idQuestion=$choiceFind->getQuestion()->getId();
        $idquiz=$choiceFind->getQuestion()->getQuiz()->getId();
        $choices=$choiceFind->getQuestion()->getChoices();
        $Question=$choiceFind->getQuestion();
        if($inputEdit==null ){
            return $this->render('choices/displayChoices.html.twig', ['addform' => $addform->createView(),'message'=>'', 'user'=>$user,'messages'=>'','id'=>$idQuestion,'idquiz'=>$idquiz,'quiz'=>$choiceFind->getQuestion()->getQuiz(),'choices'=>$choices,'question'=>$Question,]);
        }
        else {
            $choiceFind->setChoice($inputEdit);
            $choiceFind->setState($optionEdit);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('DisplayChoices',['id'=>$idQuestion,'idquiz'=>$idquiz,]);
        }
        return $this->render('choices/index.html.twig', [
            'controller_name' => 'ChoicesController',
        ]);
    }
    /**
     * @param $id
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @Route("/DisplayChoices/{id}/{idquiz}", name="DisplayChoices")
     */
    public function DisplayChoices($idquiz,$id,Request $request,SessionInterface $session): Response
    {   if(is_null($session->get('user'))){
        return $this->redirectToRoute('connection');
    }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $Question= $this->getDoctrine()->getRepository(Question::class)->find($id);

        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->find($idquiz);
        $choices= $this->getDoctrine()->getRepository(Choices::class)->findBy(['question'=>$id]);
        $newchoice = new Choices();
        if ($Question->getAnswerType()== "image"){
            $addform = $this->createForm(ChoicesImageType::class, $newchoice);
            $addform->add('Submit', SubmitType::class);
            $addform->handleRequest($request);
            if ($addform->isSubmitted() ) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $addform['choice']->getData();
                if ($uploadedFile) {
                    $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                    $uploadedFile->move(
                        $destination,
                        $newFilename
                    );
                    $newchoice->setChoice($newFilename);
                }else {
                    return $this->render('choices/displayChoices.html.twig', ['addform' => $addform->createView(),'message'=>'Please select an image', 'user'=>$user,'messages'=>'','id'=>$id,'idquiz'=>$idquiz,'quiz'=>$Quiz,'choices'=>$choices,'question'=>$Question,]);
                }
                $newchoice->setState($addform['state']->getData());
                $newchoice->setQuestion($Question);
                $em = $this->getDoctrine()->getManager();
                $em->persist($newchoice);
                $em->flush();
                return $this->redirectToRoute('DisplayChoices',['user'=>$user,'id'=>$id,'idquiz'=>$idquiz,'message'=>'','messages'=>'',]);}
        } if ($Question->getAnswerType()== "text"){
            $addform = $this->createForm(ChoicesTextType::class, $newchoice);
            $addform->add('Submit', SubmitType::class);
            $addform->handleRequest($request);
            if ($addform->isSubmitted() ) {
            if($newchoice->getChoice()==null) {
                    return $this->render('choices/displayChoices.html.twig', ['addform' => $addform->createView(),'message'=>'', 'user'=>$user,'messages'=>'Please enter a choice','id'=>$id,'idquiz'=>$idquiz,'quiz'=>$Quiz,'choices'=>$choices,'question'=>$Question,]);
                }
                $newchoice->setState($addform['state']->getData());
                $newchoice->setQuestion($Question);
                $em = $this->getDoctrine()->getManager();
                $em->persist($newchoice);
                $em->flush();
                return $this->redirectToRoute('DisplayChoices',['user'=>$user,'id'=>$id,'idquiz'=>$idquiz,'message'=>'','messages'=>'',]);
            }
        }
        return $this->render('choices/displayChoices.html.twig', ['quiz'=>$Quiz,'question'=>$Question, 'user'=>$user,'choices'=>$choices , 'addform' => $addform->createView(),'message'=>'','messages'=>'', ]);

    }

    /**
     * @param $id
     * @param SessionInterface $session
     * @param $idquiz
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/DeleteChoice/{id}/{idquiz}" , name="DeleteChoice")
     */
    public function DeleteChoice($id, SessionInterface $session,$idquiz)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }

        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $choiceFind = $this->getDoctrine()->getRepository(Choices::class)->find($id);
        $question=$choiceFind->getQuestion();


        $em = $this->getDoctrine()->getManager();
        $em->remove($choiceFind);
        $em->flush();
        return $this->redirectToRoute('DisplayChoices',['user'=>$user, 'id'=>$question->getId(), 'idquiz'=>$idquiz, ]);
    }
}
