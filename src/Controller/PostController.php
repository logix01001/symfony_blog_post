<?php

namespace App\Controller;

use App\ClassFile\CheckUserOwner;
use App\Entity\Post;

use App\Form\CommentType;
use App\Form\PostFormType;
use App\Traits\RedirectMain;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    
    use RedirectMain;

    /**
    * @Route("/test/{id}", name="test")
    * 
    */
    public function test(Post $post)
    {   

        
        if($post == null){
            throw 'No POST FOUND';
        }
        return dd($post);
    }

    /**
    * @Route("/", name="index")
    * 
    */
    public function index(Request $request, PostRepository $postRepo): Response
    {
      
        
        if($request->get('id')){
            $id = $request->get('id');

            $post = $postRepo->find($request->get('id'));

            /** 
             *  ADDING CHECKPOINT IF USER IS THE AUTHOR OF THE POST
             *  OTHERWISE REDIRECT TO MAIN PAGE.
             * */
           
            if( CheckUserOwner::CheckOwnerShip($post,$this->getUser()->getName()) ){
                $this->addFlash(
                    'notice',
                    'Cannot edit this post because you are not the author.'
                );
    
                return $this->redirectToRoute('index');
            }


            $comments =  $post->getComments();
            
            $form = $this->createForm(
                PostFormType::class,
                $postRepo->find($request->get('id')),
                [
                    'method' => 'PUT',
                    'action' => $this->generateUrl('post.update', ['id' =>  $id])
                ]
            );

           
            return $this->render('post/index.html.twig', [
                'controller_name' => 'PostController',
                'form' => $form->createView(),
                'post' => $post,
                'comments' => $comments
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
    * @Route("/show/{id}", name="show")
    * 
    */
    public function show(Post $post): Response
    {
        //use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
        //PostRepository $postRepo
        //$post = $postRepo->find($id);

        
        if($post != null){

            $comments = $post->getComments();

            $commentform = $this->createForm(
                CommentType::class,
                null,
                [
                    'method' => 'POST',
                    'action' => $this->generateUrl('comment.create', ['postid' =>  $post->getId() ])
                ]
            );
            return $this->render('post/show.html.twig',
            [
                'post' => $post,
                'comments' => $comments,
                'commentform' => $commentform->createView()
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
    * 
    */
    public function update(Request $request,post $post)
    {
        // PostRepository $postRepo
        // $post = $postRepo->find($id);


        if( CheckUserOwner::CheckOwnerShip($post,$this->getUser()->getName()) ){
            $this->addFlash(
                'notice',
                'Cannot edit this post because you are not the author.'
            );

            return $this->redirectToRoute('index');
        }

        $em = $this->getDoctrine()->getManager();
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


    /**
    * @Route("/delete/{id}", name="delete", methods={"DELETE"})
    * 
    */
    public function destroy(Post $post)
    {
      
        $em = $this->getDoctrine()->getManager();

        //PostRepository $postRepo
        //$post = $postRepo->find($id);
      
        if($post != null){


            if( CheckUserOwner::CheckOwnerShip($post,$this->getUser()->getName()) ){
                $this->addFlash(
                    'notice',
                    'Cannot delete this post because you are not the author.'
                );
    
                return $this->redirectToRoute('index');
            }
            $em->remove( $post );

            $em->flush();

            $this->addFlash(
                'notice',
                'Post successfully deleted.'
            );

            return $this->redirectIndex();
        }
    }

}
