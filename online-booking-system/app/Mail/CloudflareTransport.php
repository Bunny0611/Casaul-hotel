<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class CloudflareTransport extends AbstractTransport
{
    protected string $accountId;
    protected string $apiToken;

    public function __construct(string $accountId, string $apiToken)
    {
        parent::__construct();
        $this->accountId = $accountId;
        $this->apiToken = $apiToken;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        $fromAddress = $email->getFrom()[0]->getAddress();
        
        // Cloudflare REST API accepts 'to' as an array of strings or single string depending on schema, 
        // passing an array of email address strings resolves schema validation errors.
        $toRecipients = array_map(fn($addr) => $addr->getAddress(), $email->getTo());

        $payload = [
            'from' => $fromAddress,
            'to' => count($toRecipients) === 1 ? $toRecipients[0] : $toRecipients,
            'subject' => $email->getSubject() ?? '',
        ];

        if ($email->getTextBody()) {
            $payload['text'] = $email->getTextBody();
        }

        if ($email->getHtmlBody()) {
            $payload['html'] = $email->getHtmlBody();
        }

        $response = Http::withToken($this->apiToken)
            ->post("https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/email/sending/send", $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Cloudflare REST API Error: ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'cloudflare';
    }
}