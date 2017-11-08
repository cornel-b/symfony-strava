<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class SecurityController extends Controller
{

    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout(Request $request)
    {
        $this->get('security.token_storage')->setToken(null);

        return $this->redirectToRoute('homepage');
    }


}