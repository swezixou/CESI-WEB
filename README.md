# StageConnect – Plateforme de stages CESI
## Projet Web4All · Architecture MVC PHP

---

## 🗂️ Structure du projet

```
stageconnect/
├── app/
│   ├── Controllers/        ← Contrôleurs MVC
│   │   ├── AuthController.php      (connexion par rôle)
│   │   ├── HomeController.php
│   │   ├── OfferController.php     (SFx 7–11)
│   │   ├── CompanyController.php   (SFx 2–6)
│   │   ├── ApplicationController.php (SFx 20–22)
│   │   ├── WishlistController.php  (SFx 23–25)
│   │   ├── StudentController.php   (SFx 16–19)
│   │   ├── PilotController.php     (SFx 12–15)
│   │   └── AdminController.php
│   ├── Models/             ← Modèles (PDO)
│   │   ├── UserModel.php
│   │   ├── OfferModel.php
│   │   ├── CompanyModel.php
│   │   ├── StudentModel.php
│   │   ├── PilotModel.php
│   │   ├── ApplicationModel.php
│   │   ├── WishlistModel.php
│   │   └── SkillModel.php
│   ├── Views/              ← Templates PHP
│   │   ├── layouts/        (main.php, auth.php, error.php)
│   │   ├── auth/           (login.php – 3 onglets de rôle)
│   │   ├── home/
│   │   ├── offers/
│   │   ├── companies/
│   │   ├── students/
│   │   ├── pilots/
│   │   ├── admin/
│   │   ├── applications/
│   │   └── wishlist/
│   ├── Core/               ← Noyau MVC
│   │   ├── Router.php      (routage URL propre)
│   │   ├── Controller.php  (base)
│   │   ├── Model.php       (base)
│   │   ├── View.php        (moteur de template)
│   │   ├── Database.php    (singleton PDO)
│   │   ├── Auth.php        (session + rôles)
│   │   ├── Csrf.php        (protection CSRF)
│   │   └── Flash.php       (messages flash)
│   └── routes.php          ← Table de routage
├── config/
│   ├── config.php
│   └── database.php        ← ⚠️ Adapter vos credentials
├── database/
│   └── schema.sql          ← Schéma + données de démo
├── public/                 ← Document root Apache
│   ├── index.php           (front controller)
│   ├── .htaccess
│   └── assets/
│       ├── css/app.css
│       ├── js/app.js
│       └── uploads/        (CVs uploadés)
└── tests/
    └── AuthControllerTest.php (PHPUnit – STx 14)
```

---

## ⚙️ Installation

### 1. Prérequis
- PHP 8.1+
- MySQL / MariaDB 10.5+
- Apache + mod_rewrite activé

### 2. Créer la base de données
```sql
mysql -u root -p < database/schema.sql
```

### 3. Configurer la connexion
Éditer `config/database.php` :
```php
define('DB_USER', 'votre_user');
define('DB_PASS', 'votre_mot_de_passe');
```

### 4. Configurer Apache (vhost)
```apache
<VirtualHost *:80>
    ServerName stageconnect.local
    ServerAlias www.stageconnect.local
    DocumentRoot /var/www/stageconnect/public

    # Redirection vers HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}$1 [R=301,L]

    <Directory /var/www/stageconnect/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Gestion du index.php (Front Controller)
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [QSA,L]
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/stageconnect_error.log
    CustomLog ${APACHE_LOG_DIR}/stageconnect_access.log combined
</VirtualHost>
```

```apache
<VirtualHost *:443>
    ServerName stageconnect.local
    ServerAlias www.stageconnect.local
    DocumentRoot /var/www/stageconnect/public

    # SSL Configuration
    SSLEngine on
    SSLCertificateFile    /etc/ssl/stageconnect/stageconnect.crt
    SSLCertificateKeyFile /etc/ssl/stageconnect/stageconnect.key

    # Sécurité SSL standard
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite HIGH:!aNULL:!MD5

    <Directory /var/www/stageconnect/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # Gestion du index.php (Front Controller)
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [QSA,L]
    </Directory>

    # Headers de sécurité
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    
    ErrorLog ${APACHE_LOG_DIR}/stageconnect_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/stageconnect_ssl_access.log combined
</VirtualHost>
```

### 5. Permissions
```bash
chmod 755 public/assets/uploads
chown www-data:www-data public/assets/uploads
```

### 6. Fichier hosts (développement)
```
127.0.0.1  stageconnect.local
127.0.0.1  static.stageconnect.local
```

---

## 🔐 Comptes de démonstration

| Rôle          | Email                              | Mot de passe  |
|---------------|------------------------------------|---------------|
| Administrateur| admin@cesi.fr                      | password      |
| Pilote        | marie.dupont@cesi.fr               | password      |
| Pilote        | jean.martin@cesi.fr                | password      |
| Étudiant      | alice.bernard@student.cesi.fr      | password      |
| Étudiant      | lucas.petit@student.cesi.fr        | password      |
| Étudiant      | emma.robert@student.cesi.fr        | password      |

---

## 🧪 Tests unitaires (STx 14)

```bash
# Installer PHPUnit
composer require --dev phpunit/phpunit

# Lancer les tests
./vendor/bin/phpunit tests/
```

---

## 📋 Fonctionnalités implémentées

| SFx | Fonctionnalité                        | ✅ |
|-----|---------------------------------------|----|
| 1   | Authentification par rôle (3 onglets) | ✅ |
| 2   | Rechercher/afficher une entreprise    | ✅ |
| 3   | Créer une entreprise                  | ✅ |
| 4   | Modifier une entreprise               | ✅ |
| 5   | Évaluer une entreprise                | ✅ |
| 6   | Supprimer une entreprise              | ✅ |
| 7   | Rechercher/afficher une offre         | ✅ |
| 8   | Créer une offre                       | ✅ |
| 9   | Modifier une offre                    | ✅ |
| 10  | Supprimer une offre                   | ✅ |
| 11  | Statistiques des offres (carrousel)   | ✅ |
| 12  | Rechercher/afficher un pilote         | ✅ |
| 13  | Créer un compte pilote                | ✅ |
| 14  | Modifier un compte pilote             | ✅ |
| 15  | Supprimer un compte pilote            | ✅ |
| 16  | Rechercher/afficher un étudiant       | ✅ |
| 17  | Créer un compte étudiant              | ✅ |
| 18  | Modifier un compte étudiant           | ✅ |
| 19  | Supprimer un compte étudiant          | ✅ |
| 20  | Postuler à une offre (CV + LM)        | ✅ |
| 21  | Mes candidatures (étudiant)           | ✅ |
| 22  | Candidatures de la promo (pilote)     | ✅ |
| 23  | Afficher la wish-list                 | ✅ |
| 24  | Ajouter à la wish-list                | ✅ |
| 25  | Retirer de la wish-list               | ✅ |
| 27  | Pagination                            | ✅ |
| 28  | Mentions légales                      | ✅ |

---

## ✅ Spécifications techniques respectées

- **STx 1** – Architecture MVC stricte
- **STx 2** – HTML5 sémantique, CSS3 structuré, PHP POO
- **STx 3** – Validation côté front (HTML5/JS) ET back (PHP)
- **STx 4** – Aucun CMS utilisé
- **STx 5** – Aucun framework front/back utilisé
- **STx 6** – Apache / HTML5+CSS3+JS / PHP / MySQL
- **STx 7** – Moteur de template PHP (classe View)
- **STx 8** – Clés étrangères en base de données
- **STx 9** – vhost séparé pour les assets statiques
- **STx 10** – Responsive design + menu burger
- **STx 11** – Sessions sécurisées, CSRF, bcrypt, protection XSS/SQLi
- **STx 12** – Balises SEO, meta description, sitemap prévu
- **STx 13** – Routeur URL propre (front controller)
- **STx 14** – Tests PHPUnit (AuthControllerTest)
