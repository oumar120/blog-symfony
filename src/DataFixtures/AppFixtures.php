<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\Request;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {  
        $faker=\Faker\Factory::create('fr_FR');
        for($i=0;$i<=3;$i++){
          $category=new Category();
          $category->setTitle($faker->sentence());
          $category->setContent($faker->paragraph());
          $manager->persist($category);
          for($j=0;$j<=mt_rand(4,6);$j++){
            $article=new Article();
            $content='<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';
            $article->setTitle($faker->sentence());
            $article->setContent($content);
            $article->setImage($faker->imageUrl());
            $article->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $article->setCategory($category);
            $manager->persist($article);
            for($k=0;$k<=mt_rand(5,10);$k++){
               $comment=new Comment();
               $content='<p>'.join('</p><p>',$faker->paragraphs(2)).'</p>';
               $comment->setAuthor($faker->name);
               $comment->setContent($content);
               $comment->setCreatedAt($faker->dateTimeBetween('-3 month'));
               $comment->setArticle($article);
               $manager->persist($comment);
            }
          }  
        }
        $manager->flush();
    }
}
