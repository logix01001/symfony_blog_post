<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use App\Traits\RedirectMain;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    
    use RedirectMain;
    /**
    * @Route("/", name="index")
    */
    public function index(Request $request, PostRepository $postRepo): Response
    {
        
        

        if($request->get('id')){
            $id = $request->get('id');

            $post = $postRepo->find($request->get('id'));
            $comments =  $post->getComments();
            
            $form = $this->createForm(
                PostFormType::class,
                $postRepo->find($request->get('id')),
                [
                    'method' => 'PUT',
                    'action' => $this->generateUrl('post.update', ['id' =>  $id])
                ]
            );

            $commentform = $this->createForm(
                CommentType::class,
                null,
                [
                    'method' => 'POST',
                    'action' => $this->generateUrl('comment.create', ['postid' =>  $id])
                ]
            );
            return $this->render('post/index.html.twig', [
                'controller_name' => 'PostController',
                'form' => $form->createView(),
                'post' => $post,
                'comments' => $comments,
                'commentform' => $commentform->createView(),
            ]);


        }else{
            $form = $this->createForm(
                PostFormType::class,
                null,
                [
                    'method' => 'POST',
                    'action' => $this->generateUrl('post.create')
                ]
            );
            return $this->render('post/index.html.twig', [
                'controller_name' => 'PostController',
                'form' => $form->createView(),
            ]);

            
        }

       
    }





    /**
    * @Route("/create", name="create", methods={"POST"})
    */
    public function create(Request $request)
    {
      
        $post = new Post();

        $form = $this->createForm(
            PostFormType::class,
            $post
        );

        $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid()){
           
            $em = $this->getDoctrine()->getManager();


            $post->setUser($this->getUser());

            $post->setCreatedAt(new \DateTime('now'));


           
            $em->persist($post);

            $em->flush();

            $this->addFlash(
                'notice',
                'Post successfully created.'
            );

            return $this->redirectIndex();
        }
    }


    /**
    * @Route("/update/{id}", name="update", methods={"PUT"})
    */
    public function update(Request $request, PostRepository $postRepo,int $id)
    {
      
        $em = $this->getDoctrine()->getManager();
        $post = $postRepo->find($id);
        $form = $this->createForm(
            PostFormType::class,
            $post,
            [
                'method' => 'PUT'
            ]
        );

        $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid()){

            $em->persist($form->getData());

            $em->flush();

            $this->addFlash(
                'notice',
                'Post successfully updated.'
            );

            return $this->redirectIndex();
        }
    }

}
