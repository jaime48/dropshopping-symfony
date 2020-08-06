<?php

namespace App\Controller\Auth;

use App\Form\CustomersType;
use App\Entity\Customers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegisterController extends AbstractController
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        $customer = new Customers();
        $form = $this->createForm(CustomersType::class, $customer);

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {

            $password = $passwordEncoder->encodePassword($customer, $customer->getPassword());
            $customer->setPassword($password);
            $customer->setConfirmationToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
            $entityManager = $this->getDoctrine()->getManager();
            //$entityManager->persist($customer);
            //$entityManager->flush();
            $this->addFlash('success', 'Registration completed, please confirm your email');

            $linkToConfirmation = $this->urlGenerator->generate('register_confirm', [
                'token' => $customer->getConfirmationToken(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message())
                ->setSubject('Confirm')
                ->setFrom('duyang48484848@gmail.com')
                ->setTo('duyanguk@163.com')
                ->setBody($linkToConfirmation, 'text/html');

            $mailer->send($message);
            return $this->redirectToRoute('index');
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
    }

    public function confirm() {

    }
}