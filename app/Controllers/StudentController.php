<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\StudentModel;
use App\Models\UserModel;

/**
 * StudentController – SFx 16 à 19 + dashboard étudiant
 * Matrice : admin + pilote peuvent gérer les étudiants
 */
class StudentController extends Controller
{
    private StudentModel $studentModel;
    private UserModel    $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->studentModel = new StudentModel();
        $this->userModel    = new UserModel();
    }

    // Dashboard étudiant (SFx dashboard)
    public function dashboard(): void
    {
        $this->requireRole('student');
        $user    = Auth::user();
        $student = $this->studentModel->findByUserId(Auth::id());

        $apps     = $student ? (new \App\Models\ApplicationModel())->getByStudent($student['id']) : [];
        $wishlist = $student ? (new \App\Models\WishlistModel())->getByStudent($student['id']) : [];

        $this->render('student/dashboard', [
            'pageTitle' => 'Mon espace – ' . APP_NAME,
            'user'      => $user,
            'student'   => $student,
            'apps'      => $apps,
            'wishlist'  => $wishlist,
        ]);
    }

    // SFx 16 – Liste étudiants (admin/pilote)
    public function index(): void
    {
        $this->requireRole('admin', 'pilot');

        $page    = max(1, (int)$this->get('page', 1));
        $search  = trim($this->get('search', ''));

        $students = $this->studentModel->search($search, $page, ITEMS_PER_PAGE);
        $total    = $this->studentModel->countSearch($search);
        $pages    = max(1, (int)ceil($total / ITEMS_PER_PAGE));

        $this->render('students/index', [
            'pageTitle' => 'Gestion des étudiants – ' . APP_NAME,
            'students'  => $students,
            'search'    => $search,
            'page'      => $page,
            'pages'     => $pages,
            'total'     => $total,
        ]);
    }

    // SFx 16 – Fiche étudiant
    public function show(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $student = $this->studentModel->findWithUser((int)$id);
        if (!$student) {
            Flash::error('Étudiant introuvable.');
            $this->redirect('/students');
        }
        $apps = (new \App\Models\ApplicationModel())->getByStudent((int)$id);
        $this->render('students/show', [
            'pageTitle' => $student['firstname'] . ' ' . $student['lastname'] . ' – ' . APP_NAME,
            'student'   => $student,
            'apps'      => $apps,
        ]);
    }

    // SFx 17 – Formulaire création
    public function createForm(): void
    {
        $this->requireRole('admin', 'pilot');
        $pilots = (new \App\Models\PilotModel())->getAllWithUser();
        $this->render('students/form', [
            'pageTitle' => 'Nouvel étudiant – ' . APP_NAME,
            'student'   => null,
            'pilots'    => $pilots,
        ]);
    }

    // SFx 17 – Traitement création
    public function create(): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();

        $data   = $this->collectData();
        $errors = $this->validateData($data, true); // true = création (password obligatoire)

        if ($errors) {
            Flash::error(implode('<br>', $errors));
            $this->redirect('/students/create');
        }

        if ($this->userModel->emailExists($data['email'])) {
            Flash::error('Cet email est déjà utilisé.');
            $this->redirect('/students/create');
        }

        $userId = $this->userModel->create([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'password'  => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'      => 'student',
        ]);

        $this->studentModel->create([
            'user_id'      => $userId,
            'pilot_id'     => $data['pilot_id'] ?: null,
            'stage_status' => 'searching',
        ]);

        Flash::success('Compte étudiant créé avec succès !');
        $this->redirect('/students');
    }

    // SFx 18 – Formulaire modification
    public function editForm(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $student = $this->studentModel->findWithUser((int)$id);
        if (!$student) {
            Flash::error('Étudiant introuvable.');
            $this->redirect('/students');
        }
        $pilots = (new \App\Models\PilotModel())->getAllWithUser();
        $this->render('students/form', [
            'pageTitle' => 'Modifier étudiant – ' . APP_NAME,
            'student'   => $student,
            'pilots'    => $pilots,
        ]);
    }

    // SFx 18 – Traitement modification
    public function update(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();

        $student = $this->studentModel->findWithUser((int)$id);
        if (!$student) {
            Flash::error('Étudiant introuvable.');
            $this->redirect('/students');
        }

        $data = $this->collectData();

        $userUpdate = [
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
        ];
        // Changer le mot de passe seulement si fourni
        if (!empty($data['password'])) {
            $userUpdate['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update($student['user_id'], $userUpdate);
        $this->studentModel->update((int)$id, [
            'pilot_id'     => $data['pilot_id'] ?: null,
            'stage_status' => $data['stage_status'] ?? 'searching',
        ]);

        Flash::success('Étudiant mis à jour !');
        $this->redirect('/students/' . $id);
    }

    // SFx 19 – Supprimer (admin seulement selon matrice)
    public function delete(string $id): void
    {
        $this->requireRole('admin', 'pilot');
        $this->validateCsrf();

        $student = $this->studentModel->find((int)$id);
        if ($student) {
            $this->userModel->delete($student['user_id']); // cascade SQL supprime students
        }
        Flash::success('Compte étudiant supprimé.');
        $this->redirect('/students');
    }

    // ── Helpers ─────────────────────────────────────────────────
    private function collectData(): array
    {
        return [
            'firstname'    => trim($this->post('firstname', '')),
            'lastname'     => trim($this->post('lastname', '')),
            'email'        => filter_var($this->post('email', ''), FILTER_SANITIZE_EMAIL),
            'password'     => $this->post('password', ''),
            'pilot_id'     => (int)$this->post('pilot_id', 0),
            'stage_status' => $this->post('stage_status', 'searching'),
        ];
    }

    private function validateData(array $data, bool $isCreate = false): array
    {
        $errors = [];
        if (empty($data['firstname']))                               $errors[] = 'Le prénom est obligatoire.';
        if (empty($data['lastname']))                                $errors[] = 'Le nom est obligatoire.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))      $errors[] = 'Email invalide.';
        if ($isCreate && empty($data['password']))                   $errors[] = 'Le mot de passe est obligatoire.';
        if ($isCreate && strlen($data['password']) < 6)             $errors[] = 'Le mot de passe doit faire au moins 6 caractères.';
        return $errors;
    }
}
