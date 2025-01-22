<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact-form-submit', name: 'contact_form_submit', methods: ['POST'])]
    public function submit(Request $request, MailerInterface $mailer): Response
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');
        $message = $request->request->get('message');

        // Validierung
        if (!$name || !$email || !$message) {
            $this->addFlash('error', 'Bitte füllen Sie alle Pflichtfelder aus.');
            return $this->redirectToRoute('app.page', ['slug' => 'kontakt']);
        }

        try {
            $email = (new Email())
                ->from($email)
                ->to('info@fotografie-reisueber.de') // Hier Ihre E-Mail-Adresse eintragen
                ->subject('Neue Kontaktanfrage von ' . $name)
                ->text("Name: {$name}\nEmail: {$email}\nTelefon: {$phone}\n\nNachricht:\n{$message}");

            $mailer->send($email);

            $this->addFlash('success', 'Vielen Dank für Ihre Nachricht. Wir werden uns bald bei Ihnen melden.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Entschuldigung, es gab einen Fehler beim Senden Ihrer Nachricht. Bitte versuchen Sie es später erneut.');
        }

        return $this->redirectToRoute('app.page', ['slug' => 'kontakt']);
    }
}
