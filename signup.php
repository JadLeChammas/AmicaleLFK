<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = strtoupper(trim($_POST['nom']));
    $prenom = ucfirst(strtolower(trim($_POST['prenom'])));
    $email = trim($_POST['email']);
    $indicatif = trim($_POST['indicatif']);
    $telephone_local = trim($_POST['telephone_local']);
    $telephone = $indicatif . $telephone_local;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $date_naissance = $_POST['date_naissance'];
    $annee_promotion = trim($_POST['annee_promotion']);
    $etablissement = trim($_POST['etablissement']);
    $ville = trim($_POST['ville']);
    $pays = isset($_POST['pays']) ? trim($_POST['pays']) : '';

    $preuve_scolarite = '';
    $maxFileSize = 5 * 1024 * 1024;
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];

    if (isset($_FILES['preuve_scolarite']) && $_FILES['preuve_scolarite']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['preuve_scolarite']['tmp_name'];
        $file_name = basename($_FILES['preuve_scolarite']['name']);
        $file_size = $_FILES['preuve_scolarite']['size'];
        $file_type = mime_content_type($file_tmp);

        if (!in_array($file_type, $allowedTypes)) {
            $error = "Format de fichier non autorisé. Seuls PDF, JPG, PNG sont acceptés.";
        } elseif ($file_size > $maxFileSize) {
            $error = "Fichier trop volumineux (max 5 Mo).";
        } else {
            $upload_dir = 'uploads-proof/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $target_path = $upload_dir . uniqid() . "_" . $file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                $preuve_scolarite = $target_path;
            } else {
                $error = "Erreur lors de l'envoi du fichier.";
            }
        }
    } else {
        $error = "Veuillez joindre une preuve de scolarité.";
    }

    if (empty($error)) {
        if ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            $sql = "SELECT id FROM membres WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Cet email est déjà utilisé.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO membres 
                    (nom, prenom, email, telephone, password, role, date_naissance, annee_promotion, preuve_scolarite, etablissement, ville, pays) 
                    VALUES (?, ?, ?, ?, ?, 'user', ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssssss", $nom, $prenom, $email, $telephone, $hashed_password, $date_naissance, $annee_promotion, $preuve_scolarite, $etablissement, $ville, $pays);

                if ($stmt->execute()) {
                    $success = "Compte créé avec succès ! <a href='signin.php'>Se connecter</a>";
                } else {
                    $error = "Erreur lors de l'inscription.";
                }
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="css/signup.css">
    <link rel="icon" type="image/png" href="/img/lfk.png">
    <style>
        .return-link {
            text-align: center;
            margin-bottom: 25px;
            display: block;
            font-weight: bold;
            color: #334A5A;
            text-decoration: none;
        }
        .return-link:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function onlyNumbers(evt) {
            const charCode = evt.which ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                evt.preventDefault();
            }
        }
    </script>
</head>
<body>

<a href="choix_role.php" class="return-link">← Retour</a>

<div class="form-wrapper">
    <div class="form-box">
        <form class="form" action="signup.php" method="POST" enctype="multipart/form-data">
            <span class="title">Créer un compte</span>

            <?php if ($error): ?>
                <p style="color: red; text-align: center;"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: green; text-align: center;"><?= $success; ?></p>
            <?php endif; ?>

            <div class="form-container">
                <input type="text" name="nom" class="input" placeholder="Nom" required>
                <input type="text" name="prenom" class="input" placeholder="Prénom" required>
                <input type="email" name="email" class="input" placeholder="Email" required>

                <div class="telephone-group">
                    <div class="telephone-row">
                        <select name="indicatif" required class="select-indicatif input">
                            <?php
                            $indicatifs = [
                                ['🇫🇷', '+33'], ['🇦🇫', '+93'], ['🇦🇱', '+355'], ['🇩🇿', '+213'], ['🇦🇴', '+244'], ['🇦🇷', '+54'], ['🇦🇲', '+374'], ['🇦🇺', '+61'], ['🇦🇹', '+43'], ['🇦🇿', '+994'],
                                ['🇧🇭', '+973'], ['🇧🇩', '+880'], ['🇧🇾', '+375'], ['🇧🇪', '+32'], ['🇧🇯', '+229'], ['🇧🇴', '+591'], ['🇧🇦', '+387'], ['🇧🇼', '+267'], ['🇧🇷', '+55'],
                                ['🇧🇳', '+673'], ['🇧🇬', '+359'], ['🇰🇭', '+855'], ['🇨🇲', '+237'], ['🇨🇦', '+1'], ['🇨🇫', '+236'], ['🇹🇩', '+235'], ['🇨🇱', '+56'], ['🇨🇳', '+86'],
                                ['🇨🇴', '+57'], ['🇨🇷', '+506'], ['🇭🇷', '+385'], ['🇨🇺', '+53'], ['🇨🇾', '+357'], ['🇨🇿', '+420'], ['🇩🇰', '+45'], ['🇩🇯', '+253'], ['🇩🇴', '+1-809'],
                                ['🇪🇨', '+593'], ['🇪🇬', '+20'], ['🇸🇻', '+503'], ['🇬🇶', '+240'], ['🇪🇷', '+291'], ['🇪🇪', '+372'], ['🇪🇹', '+251'], ['🇫🇯', '+679'], ['🇫🇮', '+358'],
                                ['🇬🇦', '+241'], ['🇬🇲', '+220'], ['🇬🇪', '+995'], ['🇩🇪', '+49'], ['🇬🇭', '+233'], ['🇬🇷', '+30'], ['🇬🇹', '+502'], ['🇬🇳', '+224'],
                                ['🇭🇹', '+509'], ['🇭🇳', '+504'], ['🇭🇺', '+36'], ['🇮🇸', '+354'], ['🇮🇳', '+91'], ['🇮🇩', '+62'], ['🇮🇷', '+98'], ['🇮🇶', '+964'], ['🇮🇪', '+353'],
                                ['🇮🇱', '+972'], ['🇮🇹', '+39'], ['🇯🇲', '+1-876'], ['🇯🇵', '+81'], ['🇯🇴', '+962'], ['🇰🇿', '+7'], ['🇰🇪', '+254'], ['🇰🇼', '+965'], ['🇱🇧', '+961'],
                                ['🇱🇷', '+231'], ['🇱🇾', '+218'], ['🇱🇹', '+370'], ['🇱🇺', '+352'], ['🇲🇬', '+261'], ['🇲🇼', '+265'], ['🇲🇾', '+60'], ['🇲🇻', '+960'], ['🇲🇱', '+223'],
                                ['🇲🇹', '+356'], ['🇲🇷', '+222'], ['🇲🇺', '+230'], ['🇲🇽', '+52'], ['🇲🇩', '+373'], ['🇲🇳', '+976'], ['🇲🇪', '+382'], ['🇲🇦', '+212'], ['🇲🇿', '+258'],
                                ['🇳🇦', '+264'], ['🇳🇵', '+977'], ['🇳🇱', '+31'], ['🇳🇿', '+64'], ['🇳🇮', '+505'], ['🇳🇪', '+227'], ['🇳🇬', '+234'], ['🇰🇵', '+850'], ['🇲🇰', '+389'],
                                ['🇳🇴', '+47'], ['🇴🇲', '+968'], ['🇵🇰', '+92'], ['🇵🇸', '+970'], ['🇵🇦', '+507'], ['🇵🇾', '+595'], ['🇵🇪', '+51'], ['🇵🇭', '+63'], ['🇵🇱', '+48'],
                                ['🇵🇹', '+351'], ['🇶🇦', '+974'], ['🇷🇴', '+40'], ['🇷🇺', '+7'], ['🇷🇼', '+250'], ['🇸🇦', '+966'], ['🇸🇳', '+221'], ['🇷🇸', '+381'], ['🇸🇨', '+248'],
                                ['🇸🇱', '+232'], ['🇸🇬', '+65'], ['🇸🇰', '+421'], ['🇸🇮', '+386'], ['🇿🇦', '+27'], ['🇰🇷', '+82'], ['🇸🇸', '+211'], ['🇪🇸', '+34'], ['🇱🇰', '+94'],
                                ['🇸🇩', '+249'], ['🇸🇷', '+597'], ['🇸🇿', '+268'], ['🇸🇪', '+46'], ['🇨🇭', '+41'], ['🇸🇾', '+963'], ['🇹🇼', '+886'], ['🇹🇯', '+992'], ['🇹🇿', '+255'],
                                ['🇹🇭', '+66'], ['🇹🇬', '+228'], ['🇹🇳', '+216'], ['🇹🇷', '+90'], ['🇹🇲', '+993'], ['🇺🇬', '+256'], ['🇺🇦', '+380'], ['🇦🇪', '+971'], ['🇬🇧', '+44'],
                                ['🇺🇸', '+1'], ['🇺🇾', '+598'], ['🇺🇿', '+998'], ['🇻🇪', '+58'], ['🇻🇳', '+84'], ['🇾🇪', '+967'], ['🇿🇲', '+260'], ['🇿🇼', '+263']
                            ];
                            foreach ($indicatifs as $item) {
                                echo "<option value=\"{$item[1]}\">{$item[0]} {$item[1]}</option>";
                            }
                            ?>
                        </select>
                        <input type="text" name="telephone_local" class="input" placeholder="Numéro" required onkeypress="onlyNumbers(event)" maxlength="15">
                    </div>
                </div>
                <label for="date_naissance">Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" required>
                <input type="text" name="annee_promotion" class="input" placeholder="Année de promotion" required>

                <label>Preuve de scolarité (PDF ou image)</label>
                <input type="file" name="preuve_scolarite" class="input" accept=".pdf,.jpg,.jpeg,.png" required>
                <select name="pays" required class="input" required>
    <option value="">-- Sélectionnez votre pays de résidence --</option>
    <option value="Afghanistan">Afghanistan</option>
    <option value="Afrique du Sud">Afrique du Sud</option>
    <option value="Albanie">Albanie</option>
    <option value="Algérie">Algérie</option>
    <option value="Allemagne">Allemagne</option>
    <option value="Andorre">Andorre</option>
    <option value="Angola">Angola</option>
    <option value="Arabie Saoudite">Arabie Saoudite</option>
    <option value="Argentine">Argentine</option>
    <option value="Arménie">Arménie</option>
    <option value="Australie">Australie</option>
    <option value="Autriche">Autriche</option>
    <option value="Azerbaïdjan">Azerbaïdjan</option>
    <option value="Bahamas">Bahamas</option>
    <option value="Bahreïn">Bahreïn</option>
    <option value="Bangladesh">Bangladesh</option>
    <option value="Belgique">Belgique</option>
    <option value="Bénin">Bénin</option>
    <option value="Bhoutan">Bhoutan</option>
    <option value="Biélorussie">Biélorussie</option>
    <option value="Birmanie">Birmanie</option>
    <option value="Bolivie">Bolivie</option>
    <option value="Bosnie-Herzégovine">Bosnie-Herzégovine</option>
    <option value="Botswana">Botswana</option>
    <option value="Brésil">Brésil</option>
    <option value="Brunei">Brunei</option>
    <option value="Bulgarie">Bulgarie</option>
    <option value="Burkina Faso">Burkina Faso</option>
    <option value="Burundi">Burundi</option>
    <option value="Cambodge">Cambodge</option>
    <option value="Cameroun">Cameroun</option>
    <option value="Canada">Canada</option>
    <option value="Cap-Vert">Cap-Vert</option>
    <option value="Chili">Chili</option>
    <option value="Chine">Chine</option>
    <option value="Chypre">Chypre</option>
    <option value="Colombie">Colombie</option>
    <option value="Comores">Comores</option>
    <option value="Congo (Brazzaville)">Congo (Brazzaville)</option>
    <option value="Congo (Kinshasa)">Congo (Kinshasa)</option>
    <option value="Corée du Nord">Corée du Nord</option>
    <option value="Corée du Sud">Corée du Sud</option>
    <option value="Costa Rica">Costa Rica</option>
    <option value="Côte d’Ivoire">Côte d’Ivoire</option>
    <option value="Croatie">Croatie</option>
    <option value="Cuba">Cuba</option>
    <option value="Danemark">Danemark</option>
    <option value="Djibouti">Djibouti</option>
    <option value="Dominique">Dominique</option>
    <option value="Égypte">Égypte</option>
    <option value="Émirats Arabes Unis">Émirats Arabes Unis</option>
    <option value="Équateur">Équateur</option>
    <option value="Érythrée">Érythrée</option>
    <option value="Espagne">Espagne</option>
    <option value="Estonie">Estonie</option>
    <option value="Eswatini">Eswatini</option>
    <option value="États-Unis">États-Unis</option>
    <option value="Éthiopie">Éthiopie</option>
    <option value="Fidji">Fidji</option>
    <option value="Finlande">Finlande</option>
    <option value="France">France</option>
    <option value="Gabon">Gabon</option>
    <option value="Gambie">Gambie</option>
    <option value="Géorgie">Géorgie</option>
    <option value="Ghana">Ghana</option>
    <option value="Grèce">Grèce</option>
    <option value="Guatemala">Guatemala</option>
    <option value="Guinée">Guinée</option>
    <option value="Guinée-Bissau">Guinée-Bissau</option>
    <option value="Guinée équatoriale">Guinée équatoriale</option>
    <option value="Haïti">Haïti</option>
    <option value="Honduras">Honduras</option>
    <option value="Hongrie">Hongrie</option>
    <option value="Inde">Inde</option>
    <option value="Indonésie">Indonésie</option>
    <option value="Irak">Irak</option>
    <option value="Iran">Iran</option>
    <option value="Irlande">Irlande</option>
    <option value="Islande">Islande</option>
    <option value="Israël">Israël</option>
    <option value="Italie">Italie</option>
    <option value="Jamaïque">Jamaïque</option>
    <option value="Japon">Japon</option>
    <option value="Jordanie">Jordanie</option>
    <option value="Kazakhstan">Kazakhstan</option>
    <option value="Kenya">Kenya</option>
    <option value="Kirghizistan">Kirghizistan</option>
    <option value="Kiribati">Kiribati</option>
    <option value="Koweït">Koweït</option>
    <option value="Laos">Laos</option>
    <option value="Lesotho">Lesotho</option>
    <option value="Lettonie">Lettonie</option>
    <option value="Liban">Liban</option>
    <option value="Liberia">Liberia</option>
    <option value="Libye">Libye</option>
    <option value="Liechtenstein">Liechtenstein</option>
    <option value="Lituanie">Lituanie</option>
    <option value="Luxembourg">Luxembourg</option>
    <option value="Macédoine du Nord">Macédoine du Nord</option>
    <option value="Madagascar">Madagascar</option>
    <option value="Malaisie">Malaisie</option>
    <option value="Malawi">Malawi</option>
    <option value="Maldives">Maldives</option>
    <option value="Mali">Mali</option>
    <option value="Malte">Malte</option>
    <option value="Maroc">Maroc</option>
    <option value="Marshall">Marshall</option>
    <option value="Maurice">Maurice</option>
    <option value="Mauritanie">Mauritanie</option>
    <option value="Mexique">Mexique</option>
    <option value="Micronésie">Micronésie</option>
    <option value="Moldavie">Moldavie</option>
    <option value="Monaco">Monaco</option>
    <option value="Mongolie">Mongolie</option>
    <option value="Monténégro">Monténégro</option>
    <option value="Mozambique">Mozambique</option>
    <option value="Namibie">Namibie</option>
    <option value="Nauru">Nauru</option>
    <option value="Népal">Népal</option>
    <option value="Nicaragua">Nicaragua</option>
    <option value="Niger">Niger</option>
    <option value="Nigéria">Nigéria</option>
    <option value="Norvège">Norvège</option>
    <option value="Nouvelle-Zélande">Nouvelle-Zélande</option>
    <option value="Oman">Oman</option>
    <option value="Ouganda">Ouganda</option>
    <option value="Ouzbékistan">Ouzbékistan</option>
    <option value="Pakistan">Pakistan</option>
    <option value="Palaos">Palaos</option>
    <option value="Palestine">Palestine</option>
    <option value="Panama">Panama</option>
    <option value="Papouasie-Nouvelle-Guinée">Papouasie-Nouvelle-Guinée</option>
    <option value="Paraguay">Paraguay</option>
    <option value="Pays-Bas">Pays-Bas</option>
    <option value="Pérou">Pérou</option>
    <option value="Philippines">Philippines</option>
    <option value="Pologne">Pologne</option>
    <option value="Portugal">Portugal</option>
    <option value="Qatar">Qatar</option>
    <option value="République centrafricaine">République centrafricaine</option>
    <option value="République dominicaine">République dominicaine</option>
    <option value="République tchèque">République tchèque</option>
    <option value="Roumanie">Roumanie</option>
    <option value="Royaume-Uni">Royaume-Uni</option>
    <option value="Russie">Russie</option>
    <option value="Rwanda">Rwanda</option>
    <option value="Saint-Kitts-et-Nevis">Saint-Kitts-et-Nevis</option>
    <option value="Saint-Marin">Saint-Marin</option>
    <option value="Saint-Vincent-et-les-Grenadines">Saint-Vincent-et-les-Grenadines</option>
    <option value="Sainte-Lucie">Sainte-Lucie</option>
    <option value="Salomon">Salomon</option>
    <option value="Salvador">Salvador</option>
    <option value="Samoa">Samoa</option>
    <option value="Sao Tomé-et-Principe">Sao Tomé-et-Principe</option>
    <option value="Sénégal">Sénégal</option>
    <option value="Serbie">Serbie</option>
    <option value="Seychelles">Seychelles</option>
    <option value="Sierra Leone">Sierra Leone</option>
    <option value="Singapour">Singapour</option>
    <option value="Slovaquie">Slovaquie</option>
    <option value="Slovénie">Slovénie</option>
    <option value="Somalie">Somalie</option>
    <option value="Soudan">Soudan</option>
    <option value="Soudan du Sud">Soudan du Sud</option>
    <option value="Sri Lanka">Sri Lanka</option>
    <option value="Suède">Suède</option>
    <option value="Suisse">Suisse</option>
    <option value="Suriname">Suriname</option>
    <option value="Syrie">Syrie</option>
    <option value="Tadjikistan">Tadjikistan</option>
    <option value="Tanzanie">Tanzanie</option>
    <option value="Tchad">Tchad</option>
    <option value="Thaïlande">Thaïlande</option>
    <option value="Timor oriental">Timor oriental</option>
    <option value="Togo">Togo</option>
    <option value="Tonga">Tonga</option>
    <option value="Trinité-et-Tobago">Trinité-et-Tobago</option>
    <option value="Tunisie">Tunisie</option>
    <option value="Turkménistan">Turkménistan</option>
    <option value="Turquie">Turquie</option>
    <option value="Tuvalu">Tuvalu</option>
    <option value="Ukraine">Ukraine</option>
    <option value="Uruguay">Uruguay</option>
    <option value="Vanuatu">Vanuatu</option>
    <option value="Vatican">Vatican</option>
    <option value="Venezuela">Venezuela</option>
    <option value="Vietnam">Vietnam</option>
    <option value="Yémen">Yémen</option>
    <option value="Zambie">Zambie</option>
    <option value="Zimbabwe">Zimbabwe</option>
</select>
                <input type="text" name="ville" class="input" placeholder="Ville "required>
                <input type="text" name="etablissement" class="input" placeholder="établissement d’études supérieures"required>
                <input type="password" name="password" class="input" placeholder="Mot de passe" required>
                <input type="password" name="confirm_password" class="input" placeholder="Confirmer le mot de passe" required>
            </div>

            <button type="submit">S'inscrire</button>
        </form>

        <div class="form-section">
            <p>Déjà un compte ? <a href="signin.php">Se connecter</a></p>
        </div>
    </div>
</div>

</body>
</html>                            