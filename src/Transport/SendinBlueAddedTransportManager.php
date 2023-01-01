<?php
namespace TokenSoft\LaravelSendinBlue\Transport;

use Illuminate\Mail\TransportManager;

class SendinBlueAddedTransportManager extends TransportManager
{
    protected function createSendinblueDriver()
    {
        return new SendinBlueTransport;
    }
}
