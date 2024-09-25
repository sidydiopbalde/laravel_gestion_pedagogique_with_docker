<?php

namespace App\Services;

use App\Exports\ApprenantsErreursExport;
use App\Jobs\SendAuthEmailJob;
use App\Repository\ApprenantsFirebaseRepository;
use App\Repository\ReferentielFirebaseRepository;
use App\Jobs\SendApprenantCredentials;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Endroid\QrCode\QrCode; 
use SendApprenantCredentials as GlobalSendApprenantCredentials;

class ApprenantsFirebaseService implements ApprenantsFirebaseServiceInterface
{
    protected $apprenantsRepository;
    protected $qrCodeService;
    protected $pdfService;
    protected $referentielService;
    public function __construct(ApprenantsFirebaseRepository $apprenantsRepository,ReferentielFirebaseService  $referentielService, QrCodeService $qrCodeService, PdfService $pdfService)
    {
        $this->apprenantsRepository = $apprenantsRepository;
        $this->referentielService = $referentielService;
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
    }
    public function createApprenant(array $data)
    {
       
        if (!isset($data['user_id'])) {
            return ['error' => 'L\'ID de l\'utilisateur est requis.'];
        }
    
        $userData = $this->apprenantsRepository->findApprenantById($data['user_id']);
        $referentielData = $this->referentielService->findRefById($data['referentiel_id']);
    
        if (!$userData) {
            return ['error' => 'Utilisateur non trouvé.'];
        }
    
        $matricule = $this->generateMatricule();
    
        // Préparer les données Firebase
        $firebaseData = [
            'user' => [
                'id' => $userData['id'],
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'email' => $userData['email'],
                'photoCouverture' => $userData['photo'] ?? null,
                'fonction' => $userData['fonction_id'],
                'adresse' => $userData['adresse'],
                'telephone' => $userData['telephone'],
                'statut' => $userData['statut'],
                'referentiel_id' => $data['referentiel_id'],
            ],
            'referentiels' => $referentielData,
            'presences' => [],  // Initialisation vide
            'statut' => 'En attente',
            'matricule' => $matricule,
        ];
    
        // Ajouter les présences
        if (isset($data['presences'])) {
            foreach ($data['presences'] as $mois => $dates) {
                foreach ($dates as $date => $emargements) {
                    // Remplacer / par - dans les dates
                    $formattedDate = str_replace('/', '-', $date);
                    $firebaseData['presences'][$mois][$formattedDate] = $emargements;  // Assurez-vous d'ajouter les emargements
                }
            }
        }
    
        // Enregistrer dans Firebase
        $firebaseKey = $this->apprenantsRepository->create($firebaseData);
    
        if (isset($firebaseKey['error'])) {
            return $firebaseKey;
        }
    
        // Envoi de l'email d'authentification
        $defaultPassword = 'passer123';
        SendAuthEmailJob::dispatch("sididiop53@gmail.com", $defaultPassword, $userData['nom'], $userData['prenom'], $this->qrCodeService, $this->pdfService);
    
        return $firebaseKey; 
    }
    public function importApprenants($file, $referentielId)
    {
        $apprenants = Excel::toArray([], $file)[0]; // Assurez-vous que Maatwebsite/Laravel-Excel est installé

        $failedApprenants = [];
        
        foreach ($apprenants as $data) {
            $apprenant = $this->apprenantsRepository->create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'date_naissance' => $data['date_naissance'],
                'sexe' => $data['sexe'],
                'email' => $data['email'],
                'referentiel_id' => $referentielId,
            ]);
            $matricule = $this->generateMatricule();
            $apprenant->matricule = $matricule;      
            $qrCodePath = $this->qrCodeService->generateQrCode($apprenant->id, $apprenant->matricule);
            $defaultPassword = 'defaultPassword123!'; 
            GlobalSendApprenantCredentials::dispatch($apprenant, $defaultPassword);
        }
        return $this->createErrorFile($failedApprenants);
    }


    protected function createErrorFile($apprenantsAvecErreurs)
    {
        // Crée un fichier Excel avec les erreurs et stocke-le
        $filePath = 'apprenants_erreurs.xlsx';
        Excel::store(new ApprenantsErreursExport($apprenantsAvecErreurs), $filePath);

        return Storage::path($filePath);
    }

    private function generateMatricule()
    {
        return 'MATRICULE_' . uniqid();
    }
  
    
    public function findApprenantsById($id){
        return $this->apprenantsRepository->find($id);  // Retourne l'apprenant trouvé ou null si non trouvé
    }
  
    public function findApprenantsInactif()
    {
        return $this->apprenantsRepository->findInactifs();
    }
    public function updateApprenants(string $id, array $data)
    {
        return $this->apprenantsRepository->update($id, $data);
    }

    public function deleteApprenants(string $id)
    {
        return $this->apprenantsRepository->delete($id);
    }

    public function findApprenants(string $id)
    {
        return $this->apprenantsRepository->find($id);
    }
    public function findApprenantBy_ID(string $id, array $filters){
        return $this->apprenantsRepository->findApprenantBy_ID($id,$filters);  // Retourne l'apprenant trouvé ou null si non trouvé
    
    }
    public function filterApprenants(array $filters)
    {
        return $this->apprenantsRepository->filterByReferentielsAndStatus($filters);
    }
    public function getAllApprenants()
    {
        return $this->apprenantsRepository->getAll();
    }

    public function findApprenantsByEmail(string $email)
    {
        return $this->apprenantsRepository->findUserByEmail($email); 
    }
    public function findUserByPhone(string $telephone)
    {
        return $this->apprenantsRepository->findUserByPhone($telephone); 
    }
    public function createUserWithEmailAndPassword($email,$password)
    {
        return $this->apprenantsRepository->createUserWithEmailAndPassword($email,$password); 
    }
    public function uploadImageToStorage($filePath, $fileName)
    {
        return $this->apprenantsRepository->uploadImageToStorage($filePath, $fileName); 
    }
}
