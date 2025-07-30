<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Configuration de la base de données
$config = [
    'host' => 'localhost',
    'dbname' => 'igaser_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// Connexion à la base de données
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit;
}

// Traitement de la requête
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'enregistrer_projet':
        enregistrerProjet($pdo, $input['data']);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
}

function enregistrerProjet($pdo, $data) {
    try {
        $pdo->beginTransaction();
        
        // 1. Enregistrement du projet
        $stmt = $pdo->prepare("
            INSERT INTO projet (reference, nom, description, lieu, budget, date_debut, date_fin_prevue, statut, priorite)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['projet']['reference'],
            $data['projet']['nom'],
            $data['projet']['description'] ?? null,
            $data['projet']['lieu'],
            $data['projet']['budget'],
            $data['projet']['date_debut'],
            $data['projet']['date_fin_prevue'],
            $data['projet']['statut'],
            $data['projet']['priorite']
        ]);
        $projetId = $pdo->lastInsertId();
        
        // 2. Enregistrement du chantier
        $stmt = $pdo->prepare("
            INSERT INTO chantier (reference, nom, description, lieu, etat, id_projet)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['chantier']['reference'],
            $data['chantier']['nom'],
            $data['chantier']['description'] ?? null,
            $data['chantier']['lieu'],
            $data['chantier']['etat'],
            $projetId
        ]);
        $chantierId = $pdo->lastInsertId();
        
        // 3. Enregistrement des employés
        foreach ($data['employes'] as $employe) {
            $stmt = $pdo->prepare("
                INSERT INTO employe (nom, poste, id_chantier)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $employe['nom'],
                $employe['poste'],
                $chantierId
            ]);
        }
        
        // 4. Enregistrement des engins
        foreach ($data['engins'] as $engin) {
            $stmt = $pdo->prepare("
                INSERT INTO engin (nom, type, etat, disponibilite, id_chantier)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $engin['nom'],
                $engin['type'],
                $engin['etat'],
                $engin['disponibilite'],
                $chantierId
            ]);
        }
        
        // 5. Enregistrement des matériels
        foreach ($data['materiels'] as $materiel) {
            $stmt = $pdo->prepare("
                INSERT INTO materiel (nom, type, etat, id_chantier)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $materiel['nom'],
                $materiel['type'],
                $materiel['etat'],
                $chantierId
            ]);
        }
        
        // 6. Enregistrement des fournisseurs
        $fournisseursIds = [];
        foreach ($data['fournisseurs'] as $fournisseur) {
            $stmt = $pdo->prepare("
                INSERT INTO fournisseur (nom, contact, specialite)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $fournisseur['nom'],
                $fournisseur['contact'],
                $fournisseur['specialite']
            ]);
            $fournisseursIds[$fournisseur['reference']] = $pdo->lastInsertId();
        }
        
        // 7. Enregistrement des commandes
        $commandesIds = [];
        foreach ($data['commandes'] as $commande) {
            $stmt = $pdo->prepare("
                INSERT INTO commande (date_commande, id_fournisseur, id_chantier)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $commande['date'],
                $fournisseursIds[$commande['fournisseur_reference']],
                $chantierId
            ]);
            $commandesIds[$commande['reference']] = $pdo->lastInsertId();
        }
        
        // 8. Enregistrement des factures
        foreach ($data['factures'] as $facture) {
            $stmt = $pdo->prepare("
                INSERT INTO facture (montant, date_facture, id_commande)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $facture['montant'],
                $facture['date'],
                $commandesIds[$facture['commande_reference']]
            ]);
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Projet enregistré avec succès', 'projet_id' => $projetId]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()]);
    }
}
?>