<?php
namespace TokenSoft\LaravelSendinBlue\Transport;

use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;


class SendinBlueTransport extends Transport
{

    protected $publicKey;

    public function __construct()
    {
        $this->publicKey = config('tokensendinblue.api_key');
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {

        $this->beforeSendPerformed($message);

        $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key',  $this->publicKey);

        $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
            new \GuzzleHttp\Client(),
            $config
        );
        $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();
        $sendSmtpEmail['subject'] = $message->getSubject();
        $sendSmtpEmail['htmlContent'] = $message->getBody();
        $sendSmtpEmail['sender'] = array('name' => config('mail.from.name'), 'email' => config('mail.from.address'));
        $sendSmtpEmail['to'] = $this->getTo($message);

        $sendSmtpEmail['replyTo'] = array('email' => config('mail.from.address'), 'name' => config('mail.from.name'));


        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the "to" payload field for the API request.
     *
     * @param \Swift_Mime_SimpleMessage $message
     * @return string
     */
    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        return collect($this->allContacts($message))->map(function ($display, $address) {
            return $display ? [
                'email' => $address,
                'name' => $display
            ] : [
                'email' => $address,
                'name' => config('mail.from.name')
            ];

        })->values()->toArray();
    }

    /**
     * Get all of the contacts for the message.
     *
     * @param \Swift_Mime_SimpleMessage $message
     * @return array
     */
    protected function allContacts(Swift_Mime_SimpleMessage $message)
    {
        return array_merge(
            (array)$message->getTo(), (array)$message->getCc(), (array)$message->getBcc()
        );
    }
}
