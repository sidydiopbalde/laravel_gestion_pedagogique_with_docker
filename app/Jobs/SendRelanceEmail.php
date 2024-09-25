<?php

namespace App\Jobs;

use Mail; // Assurez-vous d'importer la classe Mail
use App\Mail\RelanceEmail; // Assurez-vous d'importer votre classe de mail
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail as FacadesMail;

class SendRelanceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $apprenant;

    public function __construct($apprenant)
    {
        $this->apprenant = $apprenant;
    }

    public function handle()
    {
        // Préparer les données pour l'email
        $email = $this->apprenant['email'];
        $login = $email; // Ou d'autres informations si nécessaire
        $motDePasse = 'motdepasseparDefaut'; // À remplacer par le mot de passe par défaut

        // Envoyer l'email
        FacadesMail::to($email)->send(new RelanceEmail($login, $motDePasse));
    }
}

