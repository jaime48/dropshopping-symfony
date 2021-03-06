<?php

namespace App\Controller\Auth;

use App\Entity\PasswordReset;
use App\Repository\CustomersRepository;
use App\Repository\PasswordResetRepository;
use App\Validator\Constraints\ConstraintEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordResetController extends AbstractController
{
    public $customersRepository;
    public $passwordResetRepository;
    private $router;
    private $urlGenerator;

    public function __construct(CustomersRepository $customersRepository,
                                PasswordResetRepository $passwordResetRepository,
                                RouterInterface $router,
                                UrlGeneratorInterface $urlGenerator)
    {
        $this->customersRepository = $customersRepository;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->router = $router;
        $this->urlGenerator = $urlGenerator;
    }

    public function reset(Request $request, \Swift_Mailer $mailer)
    {
        if ($request->isMethod('post')) {
            if ($request->get('email')) {
                $product =  $this->customersRepository->findOneBy(['email' => $request->get('email')]);
                if ($product) {
                    $token = $this->passwordResetRepository->generatePasswordResetLink( $request->get('email'));

                    $em = $this->getDoctrine()->getManager();
                    $RAW_QUERY = "DELETE FROM password_reset where password_reset.email = \"".$request->get('email')."\"";
                    $statement = $em->getConnection()->prepare($RAW_QUERY);
                    $statement->execute();

                    $password_reset = new PasswordReset();
                    $password_reset->setEmail($request->get('email'));
                    $password_reset->setToken($token);
                    $password_reset->setCreatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($password_reset);
                    $em->flush();

                    $link = $this->urlGenerator->generate('password_reset_verify', [
                        'token' => $token,
                    ], UrlGeneratorInterface::ABSOLUTE_URL);

                    $message = (new \Swift_Message('Password Rese'))
                        ->setSubject('Password Reset')
                        ->setFrom([$this->getParameter('email_from') => $this->getParameter('email_from_name')])
                        ->setTo($request->get('email'))
                        ->setBody(
                            $this->renderView(
                                'security/emails/reset.html.twig',
                                [
                                    'link' => $link
                                ]
                            ),
                            'text/html'
                        );

                    $mailer->send($message);

                    $this->addFlash('success', 'Reset link sent, please check your email');

                    return $this->redirectToRoute('index');

                }
            }
        }

        return $this->render('security/reset.html.twig');
    }

    public function resetVerify($token) {
        $customer = $this->passwordResetRepository->findOneBy(['token' => $token]);
        if($customer) {
            return $this->render('security/reset_verify.html.twig', ['email' => $customer->getEmail()]);
        }else {
            return new Response(
                '<html><body>Link Invalid</body></html>'
            );
        }
    }

    public function resetSubmit(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator) {
        $errors = $validator->validate(
            $request->get('password'),
            [
                new Regex([
                    'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                ]),
                new EqualTo( $request->get('confirm_password'))
            ]

        );

        if (0 !== count($errors)) {
            $referer = $request->headers->get('referer');
            $this->addFlash('error', 'Password not right format');
            return $this->redirect($referer);
        }
        $customer =  $this->customersRepository->findOneBy(['email' => $request->get('email')]);
        $password = $passwordEncoder->encodePassword($customer, $request->get('password'));
        $customer->setPassword($password);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($customer);
        $entityManager->flush();

        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = "DELETE FROM password_reset where password_reset.email = \"".$request->get('email')."\"";
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $this->get('security.token_storage')->setToken(null);
        $this->addFlash('success', 'You have successfully reset your password, please login');

        return $this->redirectToRoute('index');
    }
}