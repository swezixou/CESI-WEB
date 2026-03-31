<?php
namespace App\Controllers;
use App\Core\Controller;
class LegalController extends Controller {
    public function __construct() { parent::__construct(); }
    public function mentions(): void { $this->render('legal/mentions', ['pageTitle' => 'Mentions légales – ' . APP_NAME]); }
    public function confidentialite(): void { $this->render('legal/confidentialite', ['pageTitle' => 'Confidentialité – ' . APP_NAME]); }
    public function cookies(): void { $this->render('legal/cookies', ['pageTitle' => 'Cookies – ' . APP_NAME]); }
}
