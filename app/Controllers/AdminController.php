<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\OfferModel;
use App\Models\CompanyModel;
use App\Models\StudentModel;
use App\Models\PilotModel;
use App\Models\ApplicationModel;
use App\Models\UserModel;
use App\Core\Flash;

/**
 * AdminController – Dashboard et gestion globale (admin only)
 */
class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin');
    }

    // Dashboard principal admin
    public function dashboard(): void
    {
        $offerModel   = new OfferModel();
        $companyModel = new CompanyModel();
        $studentModel = new StudentModel();
        $pilotModel   = new PilotModel();
        $appModel     = new ApplicationModel();

        $stats = [
            'offers'       => $offerModel->count('is_active = 1'),
            'companies'    => $companyModel->count('is_active = 1'),
            'students'     => $studentModel->count(),
            'pilots'       => $pilotModel->count(),
            'applications' => $appModel->count(),
            'pending_apps' => $appModel->count("status = 'pending'"),
        ];

        $recentApps     = $appModel->getRecent(8);
        $topOffers      = $offerModel->getTopWishlisted(5);
        $offersByDuration = $offerModel->getStatsByDuration();

        $this->render('admin/dashboard', [
            'pageTitle'        => 'Administration – ' . APP_NAME,
            'stats'            => $stats,
            'recentApps'       => $recentApps,
            'topOffers'        => $topOffers,
            'offersByDuration' => $offersByDuration,
        ]);
    }

    // Gestion des utilisateurs (vue globale)
    public function users(): void
    {
        $page   = max(1, (int)$this->get('page', 1));
        $search = $this->get('search');
        $role   = $this->get('role');

        $userModel = new UserModel();
        $users  = $userModel->search($search, $role, $page, ITEMS_PER_PAGE);
        $total  = $userModel->countSearch($search, $role);
        $pages  = (int)ceil($total / ITEMS_PER_PAGE);

        $this->render('admin/users', [
            'pageTitle' => 'Utilisateurs – ' . APP_NAME,
            'users'     => $users,
            'search'    => $search,
            'roleFilter'=> $role,
            'page'      => $page,
            'pages'     => $pages,
            'total'     => $total,
        ]);
    }

    // Activer / désactiver un compte
    public function toggleUser(string $id): void
    {
        $this->validateCsrf();
        $userModel = new UserModel();
        $user = $userModel->find((int)$id);
        if ($user) {
            $newStatus = $user['is_active'] ? 0 : 1;
            $userModel->update((int)$id, ['is_active' => $newStatus]);
            Flash::success($newStatus ? 'Compte activé.' : 'Compte désactivé.');
        }
        $this->redirect('/admin/users');
    }

    // Supprimer définitivement un utilisateur
    public function deleteUser(string $id): void
    {
        $this->validateCsrf();
        (new UserModel())->delete((int)$id);
        Flash::success('Utilisateur supprimé définitivement.');
        $this->redirect('/admin/users');
    }
}
