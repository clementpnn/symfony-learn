<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'article', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/create', name: 'article.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreateAt(new \DateTimeImmutable());
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de la création de l\'article.');
        }
        return $this->render('article/create.html.twig', [
            'form' => $this->createForm(ArticleType::class),
        ]);
    }

    #[Route('/article/{id}', name: 'article.show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}/edit', name: 'article.edit', methods: ['GET', 'POST'])]
    public function edit(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de la modification de l\'article.');
        }
        return $this->render('article/edit.html.twig', [
            'form' => $this->createForm(ArticleType::class),
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}/delete', name: 'article.delete', methods: ['GET'])]
    public function delete(Article $article, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('article');
    }
}
