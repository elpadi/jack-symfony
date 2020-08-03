<?php

namespace App\Entity\Pages;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\{
    FormBuilderInterface,
    FormInterface
};
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType,
    TextareaType,
    EmailType,
    SubmitType
};
use Symfony\Component\Validator\Constraints\{
    NotBlank,
    Email as EmailConstraint
};

class Contact extends Page
{
    protected function getDeckImages(): array
    {
        $images = cockpit('collections:find', 'deck2016images');
        foreach ($images as &$img) {
            $path = str_replace('assets/', '', $img['image']['path']);
            $img['src'] = $this->publicPath->getUrl("img/quarter/$path");
            $img['srcset'] = $this->getImageSrcSet($path, (int) $img['width']);
        }
        return $images;
    }

    public function createContactForm(FormBuilderInterface $formBuilder): FormInterface
    {
        $formFactory = $formBuilder->getFormFactory();

        return $formFactory->createBuilder()
                           ->add(
                               'email',
                               EmailType::class,
                               [
                                   'required' => true,
                                   'constraints' => [new NotBlank(), new EmailConstraint()],
                               ]
                           )
                           ->add(
                               'message',
                               TextareaType::class,
                               [
                                   'required' => true,
                                   'constraints' => new NotBlank(),
                               ]
                           )
                           ->add('submit', SubmitType::class)
                           ->getForm();
    }

    public function canProcessForm(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        return $form->isSubmitted() && $form->isValid();
    }

    public function mailMessage(MailerInterface $mailer, array $formData): void
    {
        $email = (new Email())
            ->from($_ENV['OUTGOING_EMAIL'])
            ->to($_ENV['CONTACT_EMAIL'])
            ->subject('New message from the website contact form')
            ->text($formData['message']);

        $email->getHeaders()
            ->addMailboxListHeader('Reply-To', [$formData['email']]);

        $mailer->send($email);
    }

    protected function preprocessData(&$data): void
    {
    }
}
