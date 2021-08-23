<?php

namespace App\Controller;


use App\Entity\Category;

use App\Entity\User;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(SessionInterface $session): Response
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        return $this->render('category/displayCategory.html.twig', [
            'controller_name' => 'CategoryController',
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/Displaycat", name="Displaycat")
     */
    public function displayCat(SessionInterface $session)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $Cat = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('category/displayAllCategory.html.twig', ['listCat' => $Cat,'user'=>$user,]);
    }
    /**
     * @Route("/DisplaycatUser", name="DisplaycatUser")
     */
    public function displayCatuser(SessionInterface $session)
    {$user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $user= $this->getDoctrine()->getRepository(User::class)->find($user->getId());
        $Cat = $this->getDoctrine()->getRepository(Category::class)->findBy(['User'=>$user]);
        return $this->render('category/displayCategory.html.twig', ['listCat' => $Cat,'user'=>$user,]);
    }
    /**
     * @Route("/DisplaycatUserQuiz", name="DisplaycatUserQuiz")
     */
    public function displayCatuserQuiz(SessionInterface $session)
    {$user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user= $this->getDoctrine()->getRepository(User::class)->find($user->getId());
        $Cat = $this->getDoctrine()->getRepository(Category::class)->findBy(['User'=>$user]);
        return $this->render('category/displayCategoryQuiz.html.twig', ['listCat' => $Cat,'user'=>$user,]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/Addcat", name="AddCat")
     */
    public function addCat(Request $request,SessionInterface $session)
    {$user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user= $this->getDoctrine()->getRepository(User::class)->find($user->getId());
        $Cat = new Category();
        $form = $this->createForm(CategoryType::class, $Cat);
        $form->add('Submit', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() ) {
            $catFind=$this->getDoctrine()->getRepository(Category::class)->findOneBy(['Title'=>$Cat->getTitle(),'User'=>$user]);
            if ($catFind==null){
            $Cat->setUser($user);
            $em = $this->getDoctrine()->getManager();

            $em->persist($Cat);
            $em->flush();
            return $this->redirectToRoute('DisplaycatUser',['user'=>$user,]);}
            else {
                $this->addFlash('success', 'This category already exists!');
            }

        }
        return $this->render('category/addCategory.html.twig', ['form' => $form->createView(),'user'=>$user,]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/Deletecat/{id}" , name="Deletecat")
     */
    public function Deletecat($id, SessionInterface $session)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $catFind = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($catFind);
        $em->flush();
        return $this->redirectToRoute('DisplaycatUser',['user'=>$user,]);

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/Updatecat/{id}" , name="Updatecat")
     */
    public function Updatecat($id, Request $request, SessionInterface $session)
    {$user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $catFind = $this->getDoctrine()->getRepository(Category::class)->findBy(['id' => $id])[0];
        $title=$catFind->getTitle();
        $form = $this->createForm(CategoryType::class, $catFind);
        $form->add('Update', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() ) {
            $categoryFind=$this->getDoctrine()->getRepository(Category::class)->findOneBy(['Title'=>$catFind->getTitle(),'User'=>$user]);
            if($categoryFind==null or $categoryFind->getTitle()==$title) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirectToRoute('DisplaycatUser', ['user' => $user,]);
            }else {$this->addFlash('success','This category already exists! ');}
        }
        return $this->render('category/editCategory.html.twig', ['form' => $form->createView(),'user'=>$user,]);

    }
    /**
     * @Route("/DisplayCatRes", name="DisplayCatRes")
     */
    public function displayCatRes(SessionInterface $session)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());
        $user= $this->getDoctrine()->getRepository(User::class)->find($user->getId());
        $Cat = $this->getDoctrine()->getRepository(Category::class)->findBy(['User'=>$user]);

        return $this->render('category/displayCatRes.html.twig', ['listCat' => $Cat,'user'=>$user,]);
    }
}
