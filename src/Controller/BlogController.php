<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(ArticleRepository $repo,ObjectManager $manager): Response
    {
      $req=$manager->createQuery('select A from App\Entity\Article A where exists (select c from App\Entity\Category c where c.id=A.category and c.id=21)');
         
        $articles=$req->getResult();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=>$articles
        ]);
    }
    #[Route('/',name:"home")]
    public function home(){
        return $this->render("/blog/home.html.twig");
    }
    #[Route('/editor/add',name:"add")]
    public function add(Request $request,ObjectManager $manager){
             $article=new Article();
             $form=$this->createFormBuilder($article)
                  ->add('title')
                  ->add('category',EntityType::class,[
                    'class'=>Category::class,
                    'query_builder'=>function (EntityRepository $er){
                        return $er->createQueryBuilder('u')->orderby('u.id','desc');
                    },
                    'choice_label'=>'title'
                  ])
                  ->add('content')
                  ->add('image')
                  ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid() ){
                $article->setCreatedAt(new \DateTime());
                $manager->persist($article);
                $manager->flush();
             return $this->redirectToRoute('app_blog');
            }

        return $this->render('/blog/add.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/blog/show/{id}',name:"show")]
    public function show(Article $article,Request $request, ObjectManager $manager){
           $comment=new Comment();
           $form=$this->createFormBuilder($comment)
                   ->add('author')
                   ->add('content')
                   ->getForm();
           $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid()){
               $comment->setCreatedAt(new \DateTime);
               $manager->persist($comment);
               $manager->flush();
           }
        return $this->render('/blog/show.html.twig',[
            'article'=>$article,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/blog/edit/{id}',name:"edit")]
    public function edit(Article $article,ObjectManager $manager,Request $request){
            $form=$this->createFormBuilder($article)
                  ->add('title')
                  ->add('category',EntityType::class,[
                        'class'=>Category::class,
                        'choice_label'=>'title'
                  ])
                  ->add('content')
                  ->add('image')
                  ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $manager->persist($article);
                $manager->flush();
            return $this->redirectToRoute('app_blog');
            }
        return $this->render('/blog/add.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    #[Route('/editor/list', name:'list')]
    public function lister(ArticleRepository $repo){
                $articles=$repo->findAll(); 
        return $this->render('/blog/list.html.twig',
                     ['articles'=>$articles]);
    }
    #[Route('/editor/delete/{id}', name:'delete')]
    public function delete(ObjectManager $manager,Article $article){
         $manager->remove($article);
         $manager->flush();
         return $this->redirectToRoute('list');

    }
    
}
