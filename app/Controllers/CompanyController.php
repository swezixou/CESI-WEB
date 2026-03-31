<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\CompanyModel;
use App\Models\StudentModel;

/**
 * CompanyController – SFx 2 à 6
 * Matrice permissions :
 *   SFx2 (voir)     : tous
 *   SFx3 (créer)    : admin, pilote
 *   SFx4 (modifier) : admin, pilote
 *   SFx5 (évaluer)  : admin, pilote (pas étudiant selon matrice)
 *   SFx6 (supprimer): admin, pilote
 */
class CompanyController extends Controller
{
    private CompanyModel $companyModel;

    public function __construct()
    {
        parent::__construct();
        $this->companyModel = new CompanyModel();
    }

    // SFx 2 – Liste + recherche (public)
    public function index(): void
    {
        $page    = max(1, (int)$this->get('page', 1));
        $search  = $this->get('search', '');
        $sector  = $this->get('sector', '');
        $sectors = $this->companyModel->getSectors();

        $companies = $this->companyModel->search($search, $sector, $page, ITEMS_PER_PAGE);
        $total     = $this->companyModel->countSearch($search, $sector);
        $pages     = (int)ceil($total / ITEMS_PER_PAGE);

        $this->render('companies/index', [
            'pageTitle' => 'Entreprises – ' . APP_NAME,
            'companies' => $companies,
            'search'    => $search,
            'sector'    => $sector,
            'sectors'   => $sectors,
            'page'      => $page,
            'pages'     => $pages,
            'total'     => $total,
        ]);
    }

    // SFx 2 – Fiche entreprise (public)
    public function show(string $id): void
    {
        $company = $this->companyModel->findWithStats((int)$id);
        if (!$company) {
            Flash::error('Entreprise introuvable.');
            $this->redirect('/companies');
        }

        $offers  = $this->companyModel->getOffers((int)$id);
        $reviews = $this->companyModel->getReviews((int)$id);

        // SFx5 : admin et pilote peuvent évaluer (selon matrice)
        $canReview = Auth::is('admin') || Auth::is('pilot');

        $this->render('companies/show', [
            'pageTitle' => $company['name'] . ' – ' . APP_NAME,
            'company'   => $company,
            'offers'    => $offers,
            'reviews'   => $reviews,
            'canReview' => $canReview,
        ]);
    }

    // SFx 3 – Formulaire création
    public function createForm(): void
    {
        $this->requireRole('admin', 'pilot');
        $this->render('companies/form', [
            'pageTitle' => 'Nouvelle entreprise – ' . APP_NAME,
            'company'   => null,
        ]);
    }

    // SFx 3 – Traitement création
    public function create(): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();

        $data   = $this->collectData();
        $errors = $this->validateData($data);

        if ($errors) {
            Flash::error(implode('<br>', $errors));
            $this->redirect('/companies/create');
        }

        $id = $this->companyModel->create($data);
        Flash::success('Entreprise créée avec succès !');
        $this->redirect('/companies/' . $id);
    }

    // SFx 4 – Formulaire modification
    public function editForm(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $company = $this->companyModel->find((int)$id);
        if (!$company) {
            Flash::error('Entreprise introuvable.');
            $this->redirect('/companies');
        }
        $this->render('companies/form', [
            'pageTitle' => 'Modifier ' . $company['name'] . ' – ' . APP_NAME,
            'company'   => $company,
        ]);
    }

    // SFx 4 – Traitement modification
    public function update(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();

        $data   = $this->collectData();
        $errors = $this->validateData($data);

        if ($errors) {
            Flash::error(implode('<br>', $errors));
            $this->redirect('/companies/' . $id . '/edit');
        }

        $this->companyModel->update((int)$id, $data);
        Flash::success('Entreprise mise à jour !');
        $this->redirect('/companies/' . $id);
    }

    // SFx 5 – Évaluer (admin + pilote selon matrice)
    public function review(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();

        $rating  = max(1, min(5, (int)$this->post('rating', 3)));
        $comment = trim($this->post('comment', ''));

        // On utilise l'ID user directement comme reviewer
        $this->companyModel->addReviewByUser((int)$id, Auth::id(), $rating, $comment);
        Flash::success('Évaluation enregistrée !');
        $this->redirect('/companies/' . $id);
    }

    // SFx 6 – Supprimer (admin + pilote selon matrice)
    public function delete(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();
        $this->companyModel->delete((int)$id);
        Flash::success('Entreprise supprimée.');
        $this->redirect('/companies');
    }

    // ── Helpers ─────────────────────────────────────────────────
    private function collectData(): array
    {
        return [
            'name'        => trim($this->post('name', '')),
            'description' => trim($this->post('description', '')),
            'email'       => filter_var($this->post('email', ''), FILTER_SANITIZE_EMAIL),
            'phone'       => trim($this->post('phone', '')),
            'city'        => trim($this->post('city', '')),
            'sector'      => trim($this->post('sector', '')),
            'is_active'   => 1,
        ];
    }

    private function validateData(array $data): array
    {
        $errors = [];
        if (empty($data['name']))                                    $errors[] = 'Le nom est obligatoire.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))      $errors[] = 'Email invalide.';
        return $errors;
    }
}
