<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvoiMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $first_name, $last_name, $titre_email, $description_email;
    public function __construct($first_name, $last_name, $titre_email, $description_email)
    {
        //
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->titre_email = $titre_email;
        $this->description_email = $description_email;
       
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('cage-batiment@gmail.com')
        ->markdown('emails.envoi_mail', [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'titre_email' => $this->titre_email,
            'description_email' => $this->description_email,
        ]);

    }
}
