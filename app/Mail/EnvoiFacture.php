<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvoiFacture extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $first_name, $last_name, $email, $telephone;
    public function __construct($first_name, $last_name, $email, $telephone)
    {
        //
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->telephone = $telephone;
       
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('cage-batiment@gmail.com')
        ->markdown('emails.email_facture', [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'telephone' => $this->telephone,
        ]);

        return $this->from('cage-batiment@gmail.com')
        ->markdown('emails.email_alert_admin',[
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'telephone' => $this->telephone,
        ]);
    }
}
