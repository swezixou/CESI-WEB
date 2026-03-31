<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\WishlistModel;
use App\Models\StudentModel;

/**
 * WishlistController – SFx 23, 24, 25
 */
class WishlistController extends Controller
{
    private WishlistModel $wlModel;

    public function __construct()
    {
        parent::__construct();
        $this->wlModel = new WishlistModel();
    }

    // SFx 23 – Afficher la wish-list
    public function index(): void
    {
        $this->requireRole('student');
        $student = (new StudentModel())->findByUserId(Auth::id());
        $offers  = $student ? $this->wlModel->getByStudent($student['id']) : [];

        $this->render('wishlist/index', [
            'pageTitle' => 'Ma wish-list – ' . APP_NAME,
            'offers'    => $offers,
        ]);
    }

    // SFx 24 – Ajouter à la wish-list (AJAX ou redirect)
    public function add(string $offerId): void
    {
        $this->requireRole('student');
        $this->validateCsrf();

        $student = (new StudentModel())->findByUserId(Auth::id());
        if ($student) {
            $this->wlModel->add($student['id'], (int)$offerId);
        }

        if ($this->isAjax()) {
            $this->json(['status' => 'added']);
        } else {
            Flash::success('Offre ajoutée à votre wish-list !');
            $this->redirect('/offers/' . $offerId);
        }
    }

    // SFx 25 – Retirer de la wish-list
    public function remove(string $offerId): void
    {
        $this->requireRole('student');
        $this->validateCsrf();

        $student = (new StudentModel())->findByUserId(Auth::id());
        if ($student) {
            $this->wlModel->remove($student['id'], (int)$offerId);
        }

        if ($this->isAjax()) {
            $this->json(['status' => 'removed']);
        } else {
            Flash::success('Offre retirée de votre wish-list.');
            $this->redirect('/wishlist');
        }
    }

    private function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }
}
