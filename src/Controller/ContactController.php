<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Form\ProductSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailer, Security $security): Response
    {
        $user = $security->getUser();

        $contactForm = $this->createForm(ContactType::class, null, [
            'current_user' => $user,
        ]);

        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $data = $contactForm->getData();
            $userEmail = $user ? $user->getEmail() : 'unknown@example.com';

            $email = (new Email())
                ->from($userEmail)
                ->to('admin@admin.ro')
                ->subject('SoundVault - New Feedback')
                ->html("
                    {$data['message']}
                ");

            // Send the email
            $mailer->send($email);
            $this->addFlash('success', 'Thank you for your feedback!');

            return $this->redirectToRoute('contact');
        }

        return $this->render('default/contact.html.twig', [
            'contactForm' => $contactForm->createView(),
        ]);
    }
}
