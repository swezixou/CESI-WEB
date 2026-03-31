<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\PilotModel;
use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\ApplicationModel;

/**
 * PilotController – SFx 12 à 15 + dashboard pilote
 */
class PilotController extends Controller
{
    private PilotModel $pilotModel;
    private UserModel  $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->pilotModel = new PilotModel();
        $this->userModel  = new UserModel();
    }

    // Dashboard pilote
    public function dashboard(): void
    {
        $this->requireRole('pilot');
        $user    = Auth::user();
        $pilot   = $this->pilotModel->findByUserId(Auth::id());
        $students = $pilot ? (new StudentModel())->getByPilot($pilot['id']) : [];
        $apps    = $pilot ? (new ApplicationModel())->getByPilot($pilot['id']) : [];

        $this->render('pilot/dashboard', [
            'pageTitle' => 'Tableau de bord pilote – ' . APP_NAME,
            'user'      => $user,
            'pilot'     => $pilot,
            'students'  => $students,
            'apps'      => $apps,
        ]);
    }

    // SFx 12 – Liste des pilotes
    public function index(): void
    {
        $this->requireRole('admin');
        $page   = max(1, (int)$this->get('page', 1));
        $search = $this->get('search');
        $pilots = $this->pilotModel->search($search, $page, ITEMS_PER_PAGE);
        $total  = $this->pilotModel->countSearch($search);
        $pages  = (int)ceil($total / ITEMS_PER_PAGE);

        $this->render('pilots/index', [
            'pageTitle' => 'Gestion des pilotes – ' . APP_NAME,
            'pilots'    => $pilots,
            'search'    => $search,
            'page'      => $page,
            'pages'     => $pages,
            'total'     => $total,
        ]);
    }

    // SFx 12 – Fiche pilote
    public function show(string $id): void
    {
        $this->requireRole('admin');
        $pilot = $this->pilotModel->findWithUser((int)$id);
        if (!$pilot) {
            Flash::error('Pilote introuvable.');
            $this->redirect('/admin/pilots');
        }
        $students = (new StudentModel())->getByPilot((int)$id);
        $this->render('pilots/show', [
            'pageTitle' => $pilot['firstname'] . ' ' . $pilot['lastname'] . ' – ' . APP_NAME,
            'pilot'     => $pilot,
            'students'  => $students,
        ]);
    }

    // SFx 13 – Formulaire création
    public function createForm(): void
    {
        $this->requireRole('admin');
        $this->render('pilots/form', [
            'pageTitle' => 'Nouveau pilote – ' . APP_NAME,
            'pilot'     => null,
        ]);
    }

    // SFx 13 – Traitement création
    public function create(): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();

        $data   = $this->collectData();
        $errors = $this->validateData($data, true);
        if ($errors) {
            Flash::error(implode('<br>', $errors));
            $this->redirect('/admin/pilots/create');
        }

        if ($this->userModel->emailExists($data['email'])) {
            Flash::error('Cet email est déjà utilisé.');
            $this->redirect('/admin/pilots/create');
        }

        $userId = $this->userModel->create([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'password'  => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'      => 'pilot',
        ]);

        $this->pilotModel->create([
            'user_id'   => $userId,
            'promotion' => $data['promotion'],
        ]);

        Flash::success('Compte pilote créé avec succès !');
        $this->redirect('/admin/pilots');
    }

    // SFx 14 – Formulaire modification
    public function editForm(string $id): void
    {
        $this->requireRole('admin');
        $pilot = $this->pilotModel->findWithUser((int)$id);
        if (!$pilot) {
            Flash::error('Pilote introuvable.');
            $this->redirect('/admin/pilots');
        }
        $this->render('pilots/form', [
            'pageTitle' => 'Modifier pilote – ' . APP_NAME,
            'pilot'     => $pilot,
        ]);
    }

    // SFx 14 – Traitement modification
    public function update(string $id): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();

        $pilot  = $this->pilotModel->findWithUser((int)$id);
        if (!$pilot) {
            Flash::error('Pilote introuvable.');
            $this->redirect('/admin/pilots');
        }

        $data   = $this->collectData();
        $errors = $this->validateData($data, false);
        if ($errors) {
            Flash::error(implode('<br>', $errors));
            $this->redirect('/admin/pilots/' . $id . '/edit');
        }

        $this->userModel->update($pilot['user_id'], [
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
        ]);
        $this->pilotModel->update((int)$id, ['promotion' => $data['promotion']]);

        Flash::success('Pilote mis à jour !');
        $this->redirect('/admin/pilots/' . $id);
    }

    // SFx 15 – Supprimer un pilote
    public function delete(string $id): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();

        $pilot = $this->pilotModel->find((int)$id);
        if ($pilot) {
            $this->userModel->delete($pilot['user_id']);
        }
        Flash::success('Compte pilote supprimé.');
        $this->redirect('/admin/pilots');
    }

    // ── Helpers ─────────────────────────────────────────────────
    private function collectData(): array
    {
        return [
            'firstname' => $this->post('firstname'),
            'lastname'  => $this->post('lastname'),
            'email'     => filter_var($this->post('email'), FILTER_SANITIZE_EMAIL),
            'password'  => $this->post('password'),
            'promotion' => $this->post('promotion'),
        ];
    }

    private function validateData(array $data, bool $checkPassword): array
    {
        $errors = [];
        if (empty($data['firstname'])) $errors[] = 'Le prénom est obligatoire.';
        if (empty($data['lastname']))  $errors[] = 'Le nom est obligatoire.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
        if ($checkPassword && strlen($data['password']) < 8) $errors[] = 'Mot de passe : 8 caractères min.';
        return $errors;
    }
}
