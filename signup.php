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

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom    = strtoupper(trim($_POST['nom']));
    $prenom = ucfirst(strtolower(trim($_POST['prenom'])));
    $sexe   = $_POST['sexe'] ?? '';   // H ou F
    $email  = trim($_POST['email']);

    $indicatif        = trim($_POST['indicatif']);
    $telephone_local  = trim($_POST['telephone_local']);
    $telephone        = $indicatif . $telephone_local;

    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $date_naissance   = $_POST['date_naissance'];
    $annee_promotion  = trim($_POST['annee_promotion']);
    $etablissement    = trim($_POST['etablissement']);
    $ville            = trim($_POST['ville']);
    $pays             = trim($_POST['pays'] ?? '');

    /* ==========================
       Validation civilité
       ========================== */
    if (!in_array($sexe, ['H', 'F'])) {
        $error = "Veuillez sélectionner Monsieur ou Madame.";
    }

    /* ==========================
       Upload preuve scolarité
       ========================== */
    $preuve_scolarite = '';
    $maxFileSize = 5 * 1024 * 1024;
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];

    if (empty($error)) {
        if (isset($_FILES['preuve_scolarite']) && $_FILES['preuve_scolarite']['error'] === UPLOAD_ERR_OK) {

            $file_tmp  = $_FILES['preuve_scolarite']['tmp_name'];
            $file_name = basename($_FILES['preuve_scolarite']['name']);
            $file_size = $_FILES['preuve_scolarite']['size'];
            $file_type = mime_content_type($file_tmp);

            if (!in_array($file_type, $allowedTypes)) {
                $error = "Format de fichier non autorisé (PDF, JPG, PNG).";
            } elseif ($file_size > $maxFileSize) {
                $error = "Fichier trop volumineux (5 Mo max).";
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
    }

    /* ==========================
       Validation mots de passe + email
       ========================== */
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
                    (nom, prenom, email, telephone, password, role,
                     date_naissance, annee_promotion, preuve_scolarite,
                     etablissement, ville, pays, sexe)
                    VALUES (?, ?, ?, ?, ?, 'user', ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "ssssssssssss",
                    $nom,
                    $prenom,
                    $email,
                    $telephone,
                    $hashed_password,
                    $date_naissance,
                    $annee_promotion,
                    $preuve_scolarite,
                    $etablissement,
                    $ville,
                    $pays
                    $sexe,          // H ou F
                );

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

<a href="choix_role.php" class="return-link">← Retour à l'accueil</a>

<div class="form-wrapper">
    <div class="form-box">

        <form class="form" action="signup.php" method="POST" enctype="multipart/form-data">

            <span class="title">Créer un compte</span>

            <?php if ($error): ?>
                <p style="color:red;text-align:center"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p style="color:green;text-align:center"><?= $success ?></p>
            <?php endif; ?>

            <div class="form-container">

                <input type="text" name="nom" class="input" placeholder="Nom" required>
                <input type="text" name="prenom" class="input" placeholder="Prénom" required>

                <!-- Civilité -->
                <select name="sexe" class="input" required>
                    <option value="">-- Civilité --</option>
                    <option value="H">Monsieur</option>
                    <option value="F">Madame</option>
                </select>

                <input type="email" name="email" class="input" placeholder="Email" required>

                <div class="telephone-group">
                    <div class="telephone-row">
                        <select name="indicatif" class="select-indicatif input" required>
                            <option value="+33">🇫🇷 +33</option>
<option value="+93">🇦🇫 +93</option>
<option value="+355">🇦🇱 +355</option>
<option value="+213">🇩🇿 +213</option>
<option value="+244">🇦🇴 +244</option>
<option value="+54">🇦🇷 +54</option>
<option value="+374">🇦🇲 +374</option>
<option value="+61">🇦🇺 +61</option>
<option value="+43">🇦🇹 +43</option>
<option value="+994">🇦🇿 +994</option>

<option value="+973">🇧🇭 +973</option>
<option value="+880">🇧🇩 +880</option>
<option value="+375">🇧🇾 +375</option>
<option value="+32">🇧🇪 +32</option>
<option value="+229">🇧🇯 +229</option>
<option value="+591">🇧🇴 +591</option>
<option value="+387">🇧🇦 +387</option>
<option value="+267">🇧🇼 +267</option>
<option value="+55">🇧🇷 +55</option>

<option value="+673">🇧🇳 +673</option>
<option value="+359">🇧🇬 +359</option>
<option value="+855">🇰🇭 +855</option>
<option value="+237">🇨🇲 +237</option>
<option value="+1">🇨🇦 +1</option>
<option value="+236">🇨🇫 +236</option>
<option value="+235">🇹🇩 +235</option>
<option value="+56">🇨🇱 +56</option>
<option value="+86">🇨🇳 +86</option>

<option value="+57">🇨🇴 +57</option>
<option value="+506">🇨🇷 +506</option>
<option value="+385">🇭🇷 +385</option>
<option value="+53">🇨🇺 +53</option>
<option value="+357">🇨🇾 +357</option>
<option value="+420">🇨🇿 +420</option>
<option value="+45">🇩🇰 +45</option>
<option value="+253">🇩🇯 +253</option>
<option value="+1-809">🇩🇴 +1-809</option>

<option value="+593">🇪🇨 +593</option>
<option value="+20">🇪🇬 +20</option>
<option value="+503">🇸🇻 +503</option>
<option value="+240">🇬🇶 +240</option>
<option value="+291">🇪🇷 +291</option>
<option value="+372">🇪🇪 +372</option>
<option value="+251">🇪🇹 +251</option>
<option value="+679">🇫🇯 +679</option>
<option value="+358">🇫🇮 +358</option>

<option value="+241">🇬🇦 +241</option>
<option value="+220">🇬🇲 +220</option>
<option value="+995">🇬🇪 +995</option>
<option value="+49">🇩🇪 +49</option>
<option value="+233">🇬🇭 +233</option>
<option value="+30">🇬🇷 +30</option>
<option value="+502">🇬🇹 +502</option>
<option value="+224">🇬🇳 +224</option>

<option value="+509">🇭🇹 +509</option>
<option value="+504">🇭🇳 +504</option>
<option value="+36">🇭🇺 +36</option>
<option value="+354">🇮🇸 +354</option>
<option value="+91">🇮🇳 +91</option>
<option value="+62">🇮🇩 +62</option>
<option value="+98">🇮🇷 +98</option>
<option value="+964">🇮🇶 +964</option>
<option value="+353">🇮🇪 +353</option>

<option value="+972">🇮🇱 +972</option>
<option value="+39">🇮🇹 +39</option>
<option value="+1-876">🇯🇲 +1-876</option>
<option value="+81">🇯🇵 +81</option>
<option value="+962">🇯🇴 +962</option>
<option value="+7">🇰🇿 +7</option>
<option value="+254">🇰🇪 +254</option>
<option value="+965">🇰🇼 +965</option>
<option value="+961">🇱🇧 +961</option>

<option value="+231">🇱🇷 +231</option>
<option value="+218">🇱🇾 +218</option>
<option value="+370">🇱🇹 +370</option>
<option value="+352">🇱🇺 +352</option>
<option value="+261">🇲🇬 +261</option>
<option value="+265">🇲🇼 +265</option>
<option value="+60">🇲🇾 +60</option>
<option value="+960">🇲🇻 +960</option>
<option value="+223">🇲🇱 +223</option>

<option value="+356">🇲🇹 +356</option>
<option value="+222">🇲🇷 +222</option>
<option value="+230">🇲🇺 +230</option>
<option value="+52">🇲🇽 +52</option>
<option value="+373">🇲🇩 +373</option>
<option value="+976">🇲🇳 +976</option>
<option value="+382">🇲🇪 +382</option>
<option value="+212">🇲🇦 +212</option>
<option value="+258">🇲🇿 +258</option>

<option value="+264">🇳🇦 +264</option>
<option value="+977">🇳🇵 +977</option>
<option value="+31">🇳🇱 +31</option>
<option value="+64">🇳🇿 +64</option>
<option value="+505">🇳🇮 +505</option>
<option value="+227">🇳🇪 +227</option>
<option value="+234">🇳🇬 +234</option>
<option value="+850">🇰🇵 +850</option>
<option value="+389">🇲🇰 +389</option>

<option value="+47">🇳🇴 +47</option>
<option value="+968">🇴🇲 +968</option>
<option value="+92">🇵🇰 +92</option>
<option value="+970">🇵🇸 +970</option>
<option value="+507">🇵🇦 +507</option>
<option value="+595">🇵🇾 +595</option>
<option value="+51">🇵🇪 +51</option>
<option value="+63">🇵🇭 +63</option>
<option value="+48">🇵🇱 +48</option>

<option value="+351">🇵🇹 +351</option>
<option value="+974">🇶🇦 +974</option>
<option value="+40">🇷🇴 +40</option>
<option value="+7">🇷🇺 +7</option>
<option value="+250">🇷🇼 +250</option>
<option value="+966">🇸🇦 +966</option>
<option value="+221">🇸🇳 +221</option>
<option value="+381">🇷🇸 +381</option>
<option value="+248">🇸🇨 +248</option>

<option value="+232">🇸🇱 +232</option>
<option value="+65">🇸🇬 +65</option>
<option value="+421">🇸🇰 +421</option>
<option value="+386">🇸🇮 +386</option>
<option value="+27">🇿🇦 +27</option>
<option value="+82">🇰🇷 +82</option>
<option value="+211">🇸🇸 +211</option>
<option value="+34">🇪🇸 +34</option>
<option value="+94">🇱🇰 +94</option>

<option value="+249">🇸🇩 +249</option>
<option value="+597">🇸🇷 +597</option>
<option value="+268">🇸🇿 +268</option>
<option value="+46">🇸🇪 +46</option>
<option value="+41">🇨🇭 +41</option>
<option value="+963">🇸🇾 +963</option>
<option value="+886">🇹🇼 +886</option>
<option value="+992">🇹🇯 +992</option>
<option value="+255">🇹🇿 +255</option>

<option value="+66">🇹🇭 +66</option>
<option value="+228">🇹🇬 +228</option>
<option value="+216">🇹🇳 +216</option>
<option value="+90">🇹🇷 +90</option>
<option value="+993">🇹🇲 +993</option>
<option value="+256">🇺🇬 +256</option>
<option value="+380">🇺🇦 +380</option>
<option value="+971">🇦🇪 +971</option>
<option value="+44">🇬🇧 +44</option>

<option value="+1">🇺🇸 +1</option>
<option value="+598">🇺🇾 +598</option>
<option value="+998">🇺🇿 +998</option>
<option value="+58">🇻🇪 +58</option>
<option value="+84">🇻🇳 +84</option>
<option value="+967">🇾🇪 +967</option>
<option value="+260">🇿🇲 +260</option>
<option value="+263">🇿🇼 +263</option>

                        </select>
                        <input type="text"
                               name="telephone_local"
                               class="input"
                               placeholder="Numéro"
                               required
                               onkeypress="onlyNumbers(event)"
                               maxlength="15">
                    </div>
                </div>

                <label>Date de naissance</label>
                <input type="date" name="date_naissance" required>

                <input type="text" name="annee_promotion" class="input"
                       placeholder="Année de promotion" required>

                <label>Preuve de scolarité (PDF ou image)</label>
                <input type="file" name="preuve_scolarite"
                       class="input" accept=".pdf,.jpg,.jpeg,.png" required>

                <select name="pays" class="input" required> 
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

                <input type="text" name="ville" class="input" placeholder="Ville" required>
                <input type="text" name="etablissement" class="input"
                       placeholder="Établissement d’études supérieures" required>

                <input type="password" name="password" class="input" placeholder="Mot de passe" required>
                <input type="password" name="confirm_password" class="input"
                       placeholder="Confirmer le mot de passe" required>

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
