<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/admin/registration', name: 'signup')]
    #[Route('/admin/editUser/{id}', name:'updateUser')]
    public function index(Request $request, ObjectManager $manager,UserPasswordHasherInterface $passwordHasher,User $user=null
    ): Response
    {    
        if($user==null){
            $user=new User();
        }
        $form=$this->createForm(RegistrationType::class,$user);
           $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid()){
                $password=$user->getPassword();
                $passwordHashed=$passwordHasher->hashPassword($user,$password);
                $user->setPassword($passwordHashed);
                $manager->persist($user);
                $manager->flush();
            return $this->redirectToRoute('showUser');
           }
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
            'form'=>$form->createView()
        ]);
    }
    #[Route('/blog/connexion',name:"signin")]
    public function login(){
        return $this->render('/security/login.html.twig',);
    }
    #[Route('/blog/deconnexion',name:"signout")]
    public function signout(){}

    #[Route('/admin/user',name:'showUser')]
    public function showUser(UserRepository $repo){
           $users=$repo->findAll();
        return $this->render('/blog/user.html.twig',
                      ['users'=>$users]);
    }
    #[Route('/admin/deleteUser/{id}',name:'deleteUser')]
    public function deleteUser(User $user,ObjectManager $manager){
          $manager->remove($user);
          $manager->flush();
          return $this-redirectToRoute('showUser');
    }
}
