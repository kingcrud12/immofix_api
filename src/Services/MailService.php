<?php


namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MailService
{
    private Client $client;
    private string $fromEmail;
    private string $fromName;

    public function __construct(
        #[Autowire('%env(string:MAILJET_API_KEY)%')]  string $apiKey,
        #[Autowire('%env(string:MAILJET_API_SECRET)%')] string $apiSecret,
        #[Autowire('%env(string:MAILJET_FROM_EMAIL)%')] string $fromEmail,
        #[Autowire('%env(string:MAILJET_FROM_NAME)%')]  string $fromName,
    )
    {
        $this->client = new Client($apiKey, $apiSecret, true, ['version' => 'v3.1']);
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function sendWelcomeEmail(string $toEmail, string $toName): void
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->fromEmail,
                        'Name' => $this->fromName,
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName,
                        ]
                    ],
                    'Subject' => 'Bienvenue sur Immofix 🚀',
                    'TextPart' => "Bonjour $toName, bienvenue sur Immofix !",
                    'HTMLPart' => "<h3>Bienvenue $toName !</h3><p>Merci d’avoir créé un compte sur <b>Immofix</b>.</p>",
                ]
            ]
        ];

        $response= $this->client->post(Resources::$Email, ['body' => $body]);

        if (!in_array($response->getStatus(), [200, 201], true)) {
            throw new \RuntimeException(
                'Erreur Mailjet : '.
                $response->getReasonPhrase().' '.
                json_encode($response->getBody(), JSON_PRETTY_PRINT)
            );
        }

    }
}
