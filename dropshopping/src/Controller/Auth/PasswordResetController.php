<?php

namespace App\Controller\Auth;

use App\Repository\CustomersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PasswordResetController extends AbstractController
{
    public $customersRepository;

    public function __construct(CustomersRepository $customersRepository)
    {
        $this->customersRepository = $customersRepository;
    }
    public function reset(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->get('email')) {
                $product =  $this->customersRepository->findOneBy(['email' => $request->get('email')]);
                if ($product) {
                    $this->customersRepository->generatePasswordResetLink( $request->get('email'));
                }
            }

        }
        return $this->render('security/reset.html.twig');
    }
}