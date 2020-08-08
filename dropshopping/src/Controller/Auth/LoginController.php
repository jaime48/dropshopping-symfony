<?php

namespace App\Controller\Auth;

use App\Security\CustomerAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    private $customerAuthenticator;

    public function __construct(CustomerAuthenticator $customerAuthenticator)
    {
        $this->customerAuthenticator = $customerAuthenticator;
    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request): Response
    {
        if ($request->getSession()->get( Security::LAST_USERNAME)) {
            return $this->redirectToRoute('index');
        }

        if ($request->isMethod('post')) {
            $credentials = $this->customerAuthenticator->getCredentials($request);
            $customer = $this->customerAuthenticator->getCustomer($credentials);
            $auth = $this->customerAuthenticator->checkCredentials($credentials, $customer);
            if ($auth) {
                $request->getSession()->set(
                    Security::LAST_USERNAME,
                    [
                        'name' => $customer->getUsername(),
                        'email' =>  $customer->getEmail()
                    ]
                );
                return $this->render('index.html.twig');
            } else {
                return $this->render('security/login.html.twig', ['last_username' => '', 'error' => 'wrong credential']);
            }

        }
        return $this->render('security/login.html.twig', ['last_username' => '', 'error' => '']);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
