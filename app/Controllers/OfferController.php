<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\OfferModel;
use App\Models\CompanyModel;
use App\Models\SkillModel;
use App\Models\ApplicationModel;
use App\Models\WishlistModel;

/**
 * OfferController – SFx 7 à 11
 */
class OfferController extends Controller
{
    private OfferModel $offerModel;

    public function __construct()
    {
        parent::__construct();
        $this->offerModel = new OfferModel();
    }

    // SFx 7 – Liste + recherche (public)
    public function index(): void
    {
        $page     = max(1, (int)$this->get('page', 1));
        $search   = $this->get('search');
        $skillId  = (int)$this->get('skill');
        $duration = (int)$this->get('duration');

        $offers = $this->offerModel->search($search, $skillId, $duration, $page, ITEMS_PER_PAGE);
        $total  = $this->offerModel->countSearch($search, $skillId, $duration);
        $pages  = (int)ceil($total / ITEMS_PER_PAGE);

        $skills = (new SkillModel())->all();

        $this->render('offers/index', [
            'pageTitle' => 'Offres de stage – ' . APP_NAME,
            'offers'    => $offers,
            'skills'    => $skills,
            'search'    => $search,
            'skillId'   => $skillId,
            'duration'  => $duration,
            'page'      => $page,
            'pages'     => $pages,
            'total'     => $total,
        ]);
    }

    // SFx 7 – Afficher une offre
    public function show(string $id): void
    {
        $offer = $this->offerModel->findWithDetails((int)$id);
        if (!$offer) {
            Flash::error('Offre introuvable.');
            $this->redirect('/offers');
        }

        $isWishlisted = false;
        $hasApplied   = false;

        if (Auth::is('student')) {
            $student = (new \App\Models\StudentModel())->findByUserId(Auth::id());
            if ($student) {
                $isWishlisted = (new WishlistModel())->exists($student['id'], (int)$id);
                $hasApplied   = (new ApplicationModel())->exists($student['id'], (int)$id);
            }
        }

        $this->render('offers/show', [
            'pageTitle'    => $offer['title'] . ' – ' . APP_NAME,
            'offer'        => $offer,
            'isWishlisted' => $isWishlisted,
            'hasApplied'   => $hasApplied,
        ]);
    }

    // SFx 8 – Formulaire création (pilote / admin)
    public function createForm(): void
    {
        $this->requireRole('admin', 'pilot');
        $this->render('offers/form', [
            'pageTitle' => 'Nouvelle offre – ' . APP_NAME,
            'offer'     => null,
            'companies' => (new CompanyModel())->all(),
            'skills'    => (new SkillModel())->all(),
        ]);
    }

    // SFx 8 – Traitement création
    public function create(): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();

        $data   = $this->collectOfferData();
        $errors = $this->validateOfferData($data);

        if ($errors) {
            Flash::error(implode('<br>', $errors));
            $this->redirect('/offers/create');
        }

        // Associer le pilote courant si rôle pilot
        if (Auth::is('pilot')) {
            $pilot = (new \App\Models\PilotModel())->findByUserId(Auth::id());
            $data['pilot_id'] = $pilot['id'] ?? null;
        }

        $offerId = $this->offerModel->create($data);

        if (!empty($_POST['skills'])) {
            $this->offerModel->syncSkills($offerId, array_map('intval', $_POST['skills']));
        }

        Flash::success('Offre créée avec succès !');
        $this->redirect('/offers/' . $offerId);
    }

    // SFx 9 – Formulaire modification
    public function editForm(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $offer = $this->offerModel->findWithDetails((int)$id);
        if (!$offer) {
            Flash::error('Offre introuvable.');
            $this->redirect('/offers');
        }

        $this->render('offers/form', [
            'pageTitle' => 'Modifier l\'offre – ' . APP_NAME,
            'offer'     => $offer,
            'companies' => (new CompanyModel())->all(),
            'skills'    => (new SkillModel())->all(),
        ]);
    }

    // SFx 9 – Traitement modification
    public function update(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();

        $data   = $this->collectOfferData();
        $errors = $this->validateOfferData($data);

        if ($errors) {
            Flash::error(implode('<br>', $errors));
            $this->redirect('/offers/' . $id . '/edit');
        }

        $this->offerModel->update((int)$id, $data);
        $this->offerModel->syncSkills((int)$id, array_map('intval', $_POST['skills'] ?? []));

        Flash::success('Offre mise à jour !');
        $this->redirect('/offers/' . $id);
    }

    // SFx 10 – Supprimer une offre
    public function delete(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();
        $this->offerModel->delete((int)$id);
        Flash::success('Offre supprimée.');
        $this->redirect('/offers');
    }

    // SFx 11 – Statistiques des offres
    public function stats(): void
    {
        $this->requireRole('admin', 'pilot');
        $stats = $this->offerModel->getStats();
        $this->render('offers/stats', [
            'pageTitle' => 'Statistiques des offres – ' . APP_NAME,
            'stats'     => $stats,
        ]);
    }

    // ── Helpers ─────────────────────────────────────────────────
    private function collectOfferData(): array
    {
        return [
            'company_id'  => (int)$this->post('company_id'),
            'title'       => $this->post('title'),
            'description' => $this->post('description'),
            'salary'      => (float)$this->post('salary'),
            'duration'    => (int)$this->post('duration'),
            'location'    => $this->post('location'),
            'offer_date'  => $this->post('offer_date'),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function validateOfferData(array $data): array
    {
        $errors = [];
        if (empty($data['title']))       $errors[] = 'Le titre est obligatoire.';
        if (empty($data['company_id']))  $errors[] = 'L\'entreprise est obligatoire.';
        if (empty($data['description'])) $errors[] = 'La description est obligatoire.';
        if ($data['duration'] <= 0)      $errors[] = 'La durée doit être positive.';
        return $errors;
    }
}
