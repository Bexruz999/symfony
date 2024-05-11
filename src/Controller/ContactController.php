<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {

        $data = new ContactDTO();

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $mail = (new TemplatedEmail())
                    ->to($data->service)
                    ->from($data->email)
                    ->subject('New message from contact@example.com')
                    ->htmlTemplate('mails/contact.html.twig')
                    ->context(['data' => $data,]);
                $mailer->send($mail);
                $this->addFlash('success', 'Your message has been sent.');
                return $this->redirectToRoute('contact');
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Birbalo');
            }
        }

        return $this->render('contact/contact.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form,
        ]);
    }
}
