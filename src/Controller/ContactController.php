<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactController extends AbstractController
{
    #[Route('/contact-form-submit', name: 'contact_form_submit', methods: ['POST'])]
    public function submit(
        Request $request, 
        MailerInterface $mailer, 
        ValidatorInterface $validator,
        RateLimiterFactory $contactLimiter
    ): Response {
        // CSRF-Schutz
        if (!$this->isCsrfTokenValid('contact-form', $request->request->get('_token'))) {
            $this->addFlash('error', 'Ungültiges Formular-Token.');
            return $this->redirect($request->headers->get('referer', '/'));
        }

        // Rate Limiting
        $limiter = $contactLimiter->create($request->getClientIp());
        if (false === $limiter->consume(1)->isAccepted()) {
            $this->addFlash('error', 'Zu viele Anfragen. Bitte versuchen Sie es später erneut.');
            return $this->redirect($request->headers->get('referer', '/'));
        }

        $name = trim($request->request->get('name', ''));
        $email = trim($request->request->get('email', ''));
        $phone = trim($request->request->get('phone', ''));
        $message = trim($request->request->get('message', ''));
        $honeypot = $request->request->get('website', ''); // Honeypot-Feld

        // Honeypot-Prüfung
        if (!empty($honeypot)) {
            return $this->redirect($request->headers->get('referer', '/'));
        }

        // Email-Validierung
        $emailConstraint = new Assert\Email();
        $emailErrors = $validator->validate($email, $emailConstraint);

        // Validierung
        if (!$name || !$email || !$message || count($emailErrors) > 0) {
            $this->addFlash('error', 'Bitte füllen Sie alle Pflichtfelder korrekt aus.');
            return $this->redirect($request->headers->get('referer', '/'));
        }

        // Längenüberprüfung
        if (strlen($name) > 100 || strlen($message) > 1000 || strlen($phone) > 20) {
            $this->addFlash('error', 'Eingaben überschreiten die maximal erlaubte Länge.');
            return $this->redirect($request->headers->get('referer', '/'));
        }

        try {
            $emailObj = (new Email())
                ->from('info@fotografie-reisueber.de')
                ->replyTo($email)
                ->to('info@fotografie-reisueber.de')
                ->subject('Neue Kontaktanfrage von ' . htmlspecialchars($name))
                ->text("Name: " . htmlspecialchars($name) . "\n" .
                      "Email: " . htmlspecialchars($email) . "\n" .
                      "Telefon: " . htmlspecialchars($phone) . "\n\n" .
                      "Nachricht:\n" . htmlspecialchars($message));

            $mailer->send($emailObj);

            $this->addFlash('success', 'Vielen Dank für Ihre Nachricht. Wir werden uns bald bei Ihnen melden.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Entschuldigung, es gab einen Fehler beim Senden Ihrer Nachricht. Bitte versuchen Sie es später erneut.');
        }

        return $this->redirect($request->headers->get('referer', '/'));
    }
}
