<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\ApplicationModel;
use App\Models\StudentModel;
use App\Models\OfferModel;

/**
 * ApplicationController – SFx 20, 21, 22
 */
class ApplicationController extends Controller
{
    private ApplicationModel $appModel;

    public function __construct()
    {
        parent::__construct();
        $this->appModel = new ApplicationModel();
    }

    // SFx 20 – Postuler à une offre
    public function apply(string $offerId): void
    {
        $this->requireRole('student');
        
        // 🔥 CRUCIAL : Vérifier d'abord si le fichier dépasse la limite PHP
        // Si le fichier dépasse upload_max_filesize, $_FILES sera vide ou avec une erreur
        // et les données POST peuvent être tronquées
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le fichier dépasse la limite PHP
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_INI_SIZE) {
                Flash::error('Le CV dépasse la taille maximale autorisée (5 Mo). Veuillez réduire la taille de votre fichier.');
                $this->redirect('/offers/' . $offerId);
                return;
            }
            
            // Vérifier si aucun fichier n'a été uploadé
            if (empty($_FILES['cv']['name']) || $_FILES['cv']['error'] === UPLOAD_ERR_NO_FILE) {
                Flash::error('Le CV est obligatoire.');
                $this->redirect('/offers/' . $offerId);
                return;
            }
        }
        
        // Maintenant on peut vérifier le CSRF
        $this->validateCsrf();

        $student = (new StudentModel())->findByUserId(Auth::id());
        if (!$student) {
            Flash::error('Profil étudiant introuvable.');
            $this->redirect('/offers/' . $offerId);
            return;
        }

        if ($this->appModel->exists($student['id'], (int)$offerId)) {
            Flash::warning('Vous avez déjà postulé à cette offre.');
            $this->redirect('/offers/' . $offerId);
            return;
        }

        // Validation lettre de motivation
        $coverLetter = $this->post('cover_letter');
        if (strlen($coverLetter) < 50) {
            Flash::error('La lettre de motivation doit faire au moins 50 caractères.');
            $this->redirect('/offers/' . $offerId);
            return;
        }

        // Upload du CV
        $cvPath = null;
        if (!empty($_FILES['cv']['name']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
            $cvPath = $this->uploadCv($_FILES['cv']);
            if ($cvPath === false) {
                $this->redirect('/offers/' . $offerId);
                return;
            }
        }

        $this->appModel->create([
            'student_id'   => $student['id'],
            'offer_id'     => (int)$offerId,
            'cover_letter' => $coverLetter,
            'cv_path'      => $cvPath,
        ]);

        Flash::success('Candidature envoyée avec succès !');
        $this->redirect('/student/applications');
    }

    // SFx 21 – Mes candidatures (étudiant)
    public function myApplications(): void
    {
        $this->requireRole('student');
        $student = (new StudentModel())->findByUserId(Auth::id());
        $apps    = $student ? $this->appModel->getByStudent($student['id']) : [];

        $this->render('applications/student_list', [
            'pageTitle' => 'Mes candidatures – ' . APP_NAME,
            'apps'      => $apps,
        ]);
    }

    // SFx 22 – Candidatures de la promo (pilote)
    public function pilotApplications(): void
    {
        $this->requireRole('pilot');
        $pilot = (new \App\Models\PilotModel())->findByUserId(Auth::id());
        $apps  = $pilot ? $this->appModel->getByPilot($pilot['id']) : [];

        $this->render('applications/pilot_list', [
            'pageTitle' => 'Candidatures de ma promo – ' . APP_NAME,
            'apps'      => $apps,
        ]);
    }

    // ── Upload CV ────────────────────────────────────────────────
    private function uploadCv(array $file): string|false
    {
        // Vérifier l'erreur PHP upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE   => 'Le fichier dépasse la taille maximale autorisée (5 Mo).',
                UPLOAD_ERR_FORM_SIZE  => 'Le fichier dépasse la taille maximale autorisée.',
                UPLOAD_ERR_PARTIAL    => 'Le fichier n\'a été que partiellement téléchargé.',
                UPLOAD_ERR_NO_FILE    => 'Aucun fichier n\'a été téléchargé.',
                UPLOAD_ERR_NO_TMP_DIR => 'Erreur de configuration du serveur.',
                UPLOAD_ERR_CANT_WRITE => 'Erreur lors de la sauvegarde du fichier.',
                UPLOAD_ERR_EXTENSION  => 'Type de fichier non autorisé.',
            ];
            $errorMsg = $errors[$file['error']] ?? 'Erreur inconnue lors de l\'upload.';
            Flash::error('Erreur CV : ' . $errorMsg);
            return false;
        }

        // Vérifier la taille (5 Mo max)
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $sizeInMb = round($file['size'] / 1024 / 1024, 2);
            Flash::error("Erreur CV : le fichier dépasse 5 Mo (taille : {$sizeInMb} Mo)");
            return false;
        }

        // Vérifier l'extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            Flash::error('Erreur CV : seuls les fichiers PDF sont acceptés.');
            return false;
        }

        // Vérifier le MIME réel
        if (function_exists('finfo_open')) {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeReal = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        } else {
            $mimeReal = mime_content_type($file['tmp_name']);
        }

        $allowedMimes = ['application/pdf', 'application/x-pdf'];
        if (!in_array($mimeReal, $allowedMimes, true)) {
            Flash::error('Erreur CV : le fichier n\'est pas un PDF valide.');
            return false;
        }

        // Créer le dossier uploads s'il n'existe pas
        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0755, true);
        }

        // Vérifier les permissions du dossier
        if (!is_writable(UPLOAD_PATH)) {
            Flash::error('Erreur technique : dossier d\'upload inaccessible.');
            return false;
        }

        $filename = 'cv_' . Auth::id() . '_' . time() . '.pdf';
        $dest     = UPLOAD_PATH . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Flash::error('Erreur lors de l\'enregistrement du fichier.');
            return false;
        }

        return $filename;
    }
}
