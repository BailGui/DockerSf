<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PostRepository;

class MainController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(PostRepository $postRep): Response
    {
        $posts = $postRep->findAll();
        return $this->render('main/index.html.twig', [
            'title' => 'Homepage',
            'homepage_text'=> "Nous somme le ".date('d/m/Y \à H:i'),
            'posts' => $posts,
        ]);
    }

#[Route('/simplepost', name: 'simple_post')]
    public function simplePost(): Response
    {
        return $this->render('main/simple_post.html.twig', [
            'title' => 'Simple post',
            'homepage_text'=> "Et je parle encore de moi !",
        ]);
    }

#[Route('/post/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('main/simple_post.html.twig', [
            'post' => $post,
        ]);
    }

}