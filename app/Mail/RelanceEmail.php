<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RelanceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $login;
    public $motDePasse;

    public function __construct($login, $motDePasse)
    {
        $this->login = $login;
        $this->motDePasse = $motDePasse;
    }

    public function build()
    {
        return $this->view('emails.relance')
                    ->subject('Relance pour Activation de Compte')
                    ->with([
                        'login' => $this->login,
                        'motDePasse' => $this->motDePasse,
                    ]);
    }
}

