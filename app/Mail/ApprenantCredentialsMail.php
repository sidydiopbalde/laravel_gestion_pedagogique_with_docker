<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ApprenantFirebaseModel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Writer\PngWriter;

class ApprenantCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $apprenant;
    public $defaultPassword;
    public $qrcode;

    /**
     * Crée une nouvelle instance de la classe.
     *
     * @param ApprenantFirebaseModel $apprenant
     * @param string $defaultPassword
     */
    public function __construct(ApprenantFirebaseModel $apprenant, $defaultPassword)
    {
        $this->apprenant = $apprenant;
        $this->defaultPassword = $defaultPassword;

        // Générer le QR code avec les informations de l'apprenant
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data('Matricule: ' . $this->apprenant->matricule)
            ->encoding(new Encoding('UTF-8'))
            ->build();

        // Stocker le QR code en tant qu'image PNG
        $this->qrcode = $result->getString();
    }

    /**
     * Construire le message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Vos informations de connexion')
                    ->view('emails.apprenant_credentials')
                    ->with([
                        'nom' => $this->apprenant->nom,
                        'prenom' => $this->apprenant->prenom,
                        'email' => $this->apprenant->email,
                        'password' => $this->defaultPassword,
                        'loginLink' => route('login'),
                        'qrcode' => base64_encode($this->qrcode),
                    ])
                    // Attacher le QR code en pièce jointe
                    ->attachData($this->qrcode, 'qrcode.png', [
                        'mime' => 'image/png',
                    ]);
    }
}
