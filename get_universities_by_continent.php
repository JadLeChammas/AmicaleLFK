<?php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    exit;
}

$continent_countries = [
    'Europe' => [
        'Albanie', 'Allemagne', 'Andorre', 'Arménie', 'Autriche', 'Azerbaïdjan', 'Belgique', 'Biélorussie', 'Bosnie-Herzégovine', 'Bulgarie',
        'Chypre', 'Croatie', 'Danemark', 'Espagne', 'Estonie', 'Finlande', 'France', 'Géorgie', 'Grèce', 'Hongrie', 'Irlande', 'Islande',
        'Italie', 'Kazakhstan', 'Kosovo', 'Lettonie', 'Liechtenstein', 'Lituanie', 'Luxembourg', 'Macédoine du Nord', 'Malte', 'Moldavie',
        'Monaco', 'Monténégro', 'Norvège', 'Pays-Bas', 'Pologne', 'Portugal', 'République tchèque', 'Roumanie', 'Royaume-Uni', 'Russie',
        'Saint-Marin', 'Serbie', 'Slovaquie', 'Slovénie', 'Suède', 'Suisse', 'Ukraine', 'Vatican'
    ],
    'Afrique' => [
        'Afrique du Sud', 'Algérie', 'Angola', 'Bénin', 'Botswana', 'Burkina Faso', 'Burundi', 'Cameroun', 'Cap-Vert', 'République centrafricaine',
        'Comores', 'République du Congo', 'République démocratique du Congo', 'Côte d\'Ivoire', 'Djibouti', 'Égypte', 'Érythrée', 'Eswatini',
        'Éthiopie', 'Gabon', 'Gambie', 'Ghana', 'Guinée', 'Guinée-Bissau', 'Guinée équatoriale', 'Kenya', 'Lesotho', 'Libéria', 'Libye',
        'Madagascar', 'Malawi', 'Mali', 'Maroc', 'Maurice', 'Mauritanie', 'Mozambique', 'Namibie', 'Niger', 'Nigeria', 'Ouganda', 'Rwanda',
        'São Tomé-et-Principe', 'Sénégal', 'Seychelles', 'Sierra Leone', 'Somalie', 'Soudan', 'Soudan du Sud', 'Tanzanie', 'Tchad', 'Togo',
        'Tunisie', 'Zambie', 'Zimbabwe'
    ],
    'Asie' => [
        'Afghanistan', 'Arabie saoudite', 'Arménie', 'Azerbaïdjan', 'Bahreïn', 'Bangladesh', 'Bhoutan', 'Birmanie', 'Brunei', 'Cambodge', 'Chine',
        'Corée du Nord', 'Corée du Sud', 'Émirats arabes unis', 'Géorgie', 'Inde', 'Indonésie', 'Irak', 'Iran', 'Israël', 'Japon', 'Jordanie',
        'Kazakhstan', 'Kirghizistan', 'Koweït', 'Laos', 'Liban', 'Malaisie', 'Maldives', 'Mongolie', 'Népal', 'Oman', 'Ouzbékistan', 'Pakistan',
        'Palestine', 'Philippines', 'Qatar', 'Russie', 'Singapour', 'Sri Lanka', 'Syrie', 'Tadjikistan', 'Taïwan', 'Thaïlande', 'Timor oriental',
        'Turkménistan', 'Turquie', 'Viêt Nam', 'Yémen'
    ],
    'Amérique du Nord' => [
        'Antigua-et-Barbuda', 'Bahamas', 'Barbade', 'Belize', 'Canada', 'Costa Rica', 'Cuba', 'Dominique', 'République dominicaine', 'El Salvador',
        'États-Unis', 'Grenade', 'Guatemala', 'Haïti', 'Honduras', 'Jamaïque', 'Mexique', 'Nicaragua', 'Panama', 'Saint-Christophe-et-Niévès',
        'Sainte-Lucie', 'Saint-Vincent-et-les-Grenadines', 'Trinité-et-Tobago'
    ],
    'Amérique du Sud' => [
        'Argentine', 'Bolivie', 'Brésil', 'Chili', 'Colombie', 'Équateur', 'Guyana', 'Paraguay', 'Pérou', 'Suriname', 'Uruguay', 'Venezuela'
    ],
    'Océanie' => [
        'Australie', 'Fidji', 'Kiribati', 'Îles Marshall', 'Micronésie', 'Nauru', 'Nouvelle-Zélande', 'Palaos', 'Papouasie-Nouvelle-Guinée',
        'Samoa', 'Salomon', 'Tonga', 'Tuvalu', 'Vanuatu'
    ]
];

$continent = $_GET['continent'] ?? '';
if (!isset($continent_countries[$continent])) {
    echo "Continent inconnu.";
    exit;
}

$pays_list = $continent_countries[$continent];
$placeholders = implode(',', array_fill(0, count($pays_list), '?'));

$sql = "SELECT nom, prenom, email, annee_promotion, pays, ville, etablissement
        FROM membres
        WHERE pays IN ($placeholders) AND etablissement IS NOT NULL";

$stmt = $conn->prepare($sql);
$types = str_repeat('s', count($pays_list));
$stmt->bind_param($types, ...$pays_list);
$stmt->execute();
$result = $stmt->get_result();

// Regrouper par pays > ville > établissement
$data = [];
while ($row = $result->fetch_assoc()) {
    $p = $row['pays'];
    $v = $row['ville'];
    $e = $row['etablissement'];
    $data[$p][$v][$e][] = [
        'nom' => $row['nom'],
        'prenom' => $row['prenom'],
        'email' => $row['email'],
        'promo' => $row['annee_promotion']
    ];
}

// Affichage uniquement des pays avec au moins un membre
foreach ($data as $pays => $villes) {
    $pID = md5($pays);
    echo "<p><a href='#' class='toggle-block' data-target='p-$pID'>▶ $pays</a></p>";
    echo "<div id='p-$pID' class='toggle-content' style='display:none; margin-left:20px;'>";

    foreach ($villes as $ville => $etabs) {
        $vID = md5($pays . $ville);
        echo "<p><a href='#' class='toggle-block' data-target='v-$vID'>▶ $ville</a></p>";
        echo "<div id='v-$vID' class='toggle-content' style='display:none; margin-left:20px;'>";

        foreach ($etabs as $etab => $membres) {
            $eID = md5($pays . $ville . $etab);
            echo "<p><a href='#' class='toggle-block' data-target='e-$eID'>▶ $etab</a></p>";
            echo "<ul id='e-$eID' class='toggle-content member-list' style='display:none; margin-left:20px;'>";

            foreach ($membres as $m) {
                $fullName = htmlspecialchars($m['prenom'] . ' ' . $m['nom']);
                $email = htmlspecialchars($m['email']);
                $promo = htmlspecialchars($m['promo'] ?? 'N/A');
                echo "<li><strong>$fullName</strong> – <a href='mailto:$email'>$email</a> – Promo $promo</li>";
            }

            echo "</ul>";
        }

        echo "</div>";
    }

    echo "</div>";
}

$stmt->close();
$conn->close();
?>
