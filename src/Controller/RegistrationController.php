<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Traits\RedirectMain;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{

    private $passwordEncoder;

    use RedirectMain;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    /**
     * @Route("/registration", name="registration")
     */
    public function index(Request $request): Response
    {

        $user = new User();
        $form = $this->createForm(UserFormType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted()  && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $user->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $user, $user->getPassword()
                    )
                );
            $user->setRoles(['ROLE_USER']);

            
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'notice',
                'User successfully registered.'
            );
            return $this->redirectIndex();
        }


        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
            'form' => $form->createView()
        ]);
    }
}
