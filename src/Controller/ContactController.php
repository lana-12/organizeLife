<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use App\Service\MailerService;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'contact')]
    public function contact(Request $request, MailerService $mailer): Response
    {
        $data = new ContactDTO();

        // Decommenter pour utiliser pour les test
        // $data->name = "Vivi";
        // $data->email = "vivi@vivi.com";
        // $data->subject = "Demande de contact";
        // $data->message= "Voici mon message";
            

        $form= $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach ($data->collaborators as $collaborator) {

                // TODO : A revoir de grouper les emails, ex Cc, au lieu que chaque collaborateur recoivent un email
                $mailer->sendEmail(
                    $collaborator->getEmail(),
                    $data->email,
                    $data->subject,
                    'emails/contact.html.twig',
                    ['data' => $data]
                );

            }
         
            $this->addFlash('success', "Votre message a bien été envoyé");

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'formContact' => $form,
        ]);
    }
}
