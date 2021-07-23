<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/comment", name="comment.")
 */
class CommentController extends AbstractController
{
   
    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function create(Request $request, PostRepository $postRepository)
    {   
        $post = $postRepository->find( $request->get('postid') );
       
        $comment = new Comment();
        $form = $this->createForm(
            CommentType::class,
            $comment
        );
        $comment->setUser($this->getUser());
        $comment->setPost($post);  
        $comment->setCreatedAt(new \DateTime('now'));
          
        $form->handleRequest($request);
     
        if( $form->isSubmitted() && $form->isValid()){
           
         
           

            $em = $this->getDoctrine()->getManager();


            $em->persist($comment);
            $em->flush();

          return  $this->redirect($request->server->get('HTTP_REFERER'));
        }
    }
}
