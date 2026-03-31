<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\OfferModel;
use App\Models\CompanyModel;
use App\Models\StudentModel;

class HomeController extends Controller
{
    public function index(): void
    {
        $offerModel   = new OfferModel();
        $companyModel = new CompanyModel();
        $studentModel = new StudentModel();

        $latestOffers = $offerModel->getLatest(6);
        $stats = [
            'offers'    => $offerModel->count('is_active = 1'),
            'companies' => $companyModel->count('is_active = 1'),
            'students'  => $studentModel->count(),
        ];

        $this->render('home/index', [
            'pageTitle'    => APP_NAME . ' – Votre plateforme de stages CESI',
            'latestOffers' => $latestOffers,
            'stats'        => $stats,
        ]);
    }
}
