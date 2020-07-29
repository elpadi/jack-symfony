<?php
namespace App\Entity\Pages;

use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\{
    Forms,
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

    protected function createContactForm(): FormInterface
    {
        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new NativeSessionTokenStorage();
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new CsrfExtension($csrfManager))
            ->getFormFactory();

        return $formFactory->createBuilder()
                           ->add('email', EmailType::class)
                           ->add('message', TextareaType::class)
                           ->add('submit', SubmitType::class)
                           ->getForm();
    }

    protected function preprocessData(&$data): void
    {
        $data['form'] = $this->createContactForm()->createView();
    }
}
