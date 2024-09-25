<?php

namespace App\Models;

use App\Models\FirebaseModel;

class ApprenantFirebaseModel extends FirebaseModel
{
    protected $path = 'apprenants';
    protected $fillable = ['nom', 'prenom', 'adresse', 'telephone', 'fonction_id', 'email', 'photo', 'statut'];
    public $email;
    public $matricule;
    public $nom;
    public $prenom;

    // public function import(Request $request)
    // {
    //     try {
    //         // Vérification du fichier Excel
    //         if (!$request->hasFile('file')) {
    //             return response()->json(['error' => 'Fichier non trouvé'], 400);
    //         }

    //         // Lecture du fichier Excel
    //         $file = $request->file('file');
    //         $import = new ApprenantsImport();
    //         Excel::import($import, $file);

    //         // Récupérer les apprenants importés et les traiter
    //         $apprenants = $import->getApprenants();

    //         $apprenantsAvecErreurs = [];
    //         foreach ($apprenants as $apprenant) {
    //             try {
    //                 // Générer un matricule
    //                 $matricule = $this->generateMatricule();

    //                 // Générer un QR code
    //                 $qrCode = $this->generateQrCode($matricule, $apprenant);

    //                 // Inscription dans Firebase
    //                 $firebaseUser = $this->firebaseAuth->createUserWithEmailAndPassword(
    //                     $apprenant['email'],
    //                     'defaultPassword123!'
    //                 );

    //                 // Enregistrer l'apprenant dans la base de données (si nécessaire)
    //                 $apprenantModel = Apprenant::create([
    //                     'nom' => $apprenant['nom'],
    //                     'prenom' => $apprenant['prenom'],
    //                     'date_naissance' => $apprenant['date_naissance'],
    //                     'sexe' => $apprenant['sexe'],
    //                     'matricule' => $matricule,
    //                     'qr_code' => $qrCode,
    //                     'firebase_uid' => $firebaseUser->uid,
    //                 ]);

    //                 // Créer un Job pour envoyer les identifiants par email
    //                 SendApprenantCredentials::dispatch($apprenantModel);

    //             } catch (\Exception $e) {
    //                 // Ajouter l'apprenant dans la liste des erreurs
    //                 $apprenant['erreur'] = $e->getMessage();
    //                 $apprenantsAvecErreurs[] = $apprenant;
    //             }
    //         }

    //         // Si des apprenants ont des erreurs, les retourner dans un fichier Excel
    //         if (!empty($apprenantsAvecErreurs)) {
    //             $filePath = $this->createErrorFile($apprenantsAvecErreurs);
    //             return response()->download($filePath, 'apprenants_erreurs.xlsx');
    //         }

    //         return response()->json(['message' => 'Importation réussie'], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Erreur lors de l\'importation'], 500);
    //     }
    // }

    // protected function generateMatricule()
    // {
    //     return 'MATR-' . strtoupper(uniqid());
    // }

    // protected function generateQrCode($matricule, $apprenant)
    // {
    //     // Utilise une librairie de génération de QR code
    //     return QrCode::format('png')->size(300)->generate(json_encode([
    //         'matricule' => $matricule,
    //         'nom' => $apprenant['nom'],
    //         'prenom' => $apprenant['prenom'],
    //     ]));
    // }

    // protected function createErrorFile($apprenantsAvecErreurs)
    // {
    //     // Crée un fichier Excel avec les erreurs et stocke-le
    //     $filePath = 'apprenants_erreurs.xlsx';
    //     Excel::store(new ApprenantsErreursExport($apprenantsAvecErreurs), $filePath);

    //     return Storage::path($filePath);
    // }
}
