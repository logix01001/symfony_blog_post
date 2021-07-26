<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PagesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $postsRepo, Request $request): Response
    {   
        $page = $request->get('page') ? $request->get('page') : 1;
        $pageSize = $request->get('page-size') ? $request->get('page-size') : 5;

        $posts = $postsRepo->getPostByPages($page, $pageSize);

        //get total pages
        $pagesCount = ceil($postsRepo->getTotalRecord() /  $pageSize);


    
       
        return $this->render('pages/index.html.twig', [
            'controller_name' => 'PagesController',
            'posts' => $posts,
            'pagesCount' => $pagesCount,
            'page' => $page,
        ]);
    }
}
