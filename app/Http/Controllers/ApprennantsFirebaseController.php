<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\ApprenantsFirebaseService;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exports\UserFirebaseExport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ApprennantsFirebaseController extends Controller
{
    protected $apprenantsFirebaseService;

    public function __construct(ApprenantsFirebaseService $apprenantsFirebaseService)
    {
        $this->apprenantsFirebaseService = $apprenantsFirebaseService;
    }
    public function store(Request $request)
    {
        $firebaseKey = $this->apprenantsFirebaseService->createApprenant($request->all());
        return response()->json(['message' => 'Apprenant créé avec succès', 'id' => $firebaseKey]);
    }
    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xls,xlsx',
    //         'referentiel_id' => 'required|exists:referentiels,id',
    //     ]);

    //     $file = $request->file('file');
    //     $failedApprenantsFile = $this->apprenantsFirebaseService->importApprenants($file, $request->referentiel_id);

    //     return response()->download($failedApprenantsFile)->deleteFileAfterSend(true);
    // }
    public function index()
    {
        $apprenants = $this->apprenantsFirebaseService->getAllApprenants();
        return response()->json(['apprenants' => $apprenants]);
    }
    public function filterApprenants()
    {
        $filters = request()->query('filtre', []); // Utiliser un tableau vide par défaut
        $apprenants = $this->apprenantsFirebaseService->filterApprenants($filters);
        return response()->json(['apprenants' => $apprenants]);
    }
    public function show($id)
    {
        $filters = request()->query('filtre', []);
        $apprenant = $this->apprenantsFirebaseService->findApprenantBy_ID($id,$filters);
        if (!$apprenant) {
            return response()->json(['error' => 'Apprenant non trouvé'], 404);
        }
        return response()->json(['apprenant' => $apprenant]);
    }
    public function findApprenantInactif()
    {
        $apprenantsInactifs = $this->apprenantsFirebaseService->findApprenantsInactif();
        return response()->json(['apprenants_inactifs' => $apprenantsInactifs]);
    }

    public function import(Request $request)
    {
        // Validation du fichier
        // $request->validate([
        //     'file' => 'required|file|mimes:xlsx,xls,csv',
        //     'referentiel_id' => 'required|integer', // Ajoutez la validation pour le référentiel
        // ]);
        $file = $request->file('file');
        $referentielId = $request->input('referentiel_id');

        // Appel du service d'importation
        $errorFilePath = $this->apprenantsFirebaseService->importApprenants($file, $referentielId);
        
        return response()->json([
            'message' => 'Importation des apprenants réussie',
            'error_file' => $errorFilePath ? Storage::url($errorFilePath) : null,
        ]);
    }
    

}