<?php
// Source code derived from:
// http://symfony.com/doc/2.1/book/security.html#book-security-form-login

namespace UiucCms\Bundle\UserBundle\Controller;

use UiucCms\Bundle\UserBundle\Form\Type\UserType;
use UiucCms\Bundle\UserBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SecurityController extends Controller
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'UiucCmsUserBundle:Security:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }

    public function registerAction() 
    {
		$User = new User();
        $form = $this->createForm(new UserType(), $User, array('action' => $this->generateUrl('uiuc_cms_user_submit'),));

        return $this->render(
            'UiucCmsUserBundle:Security:register.html.twig',
            array('form' => $form->createView())
        );
    }

    public function submitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();             
        $form = $this->createForm(new UserType(), new User());
        $form->handleRequest($request);

        $user = $form->getData();

        $em->persist($user);
        $em->flush();
        return new Response('Successfully added element '.$user->getId().' to database.');
    }

 

}
