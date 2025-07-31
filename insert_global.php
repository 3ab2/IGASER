<?php
$host = 'localhost';
$dbname = 'igaser_db';
$user = 'root';
$pass = ''; // عدلها حسب الإعدادات عندك

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Commencer une transaction
    $pdo->beginTransaction();

    // 1. Ajouter le projet
    $stmt = $pdo->prepare("INSERT INTO projet (nom, lieu, budget, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['projet_nom'],
        $_POST['projet_lieu'],
        $_POST['projet_budget'],
        $_POST['projet_date_debut'],
        $_POST['projet_date_fin']
    ]);
    $id_projet = $pdo->lastInsertId();

    // 2. Ajouter le chantier lié au projet
    $stmt = $pdo->prepare("INSERT INTO chantier (nom, lieu, etat, id_projet) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['chantier_nom'],
        $_POST['chantier_lieu'],
        $_POST['chantier_etat'],
        $id_projet
    ]);
    $id_chantier = $pdo->lastInsertId();

    // 3. Ajouter un employé (si rempli)
    if (!empty($_POST['employe_nom']) && !empty($_POST['employe_poste'])) {
        $stmt = $pdo->prepare("INSERT INTO employe (nom, poste, id_chantier) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['employe_nom'],
            $_POST['employe_poste'],
            $id_chantier
        ]);
    }

    // 4. Ajouter un engin (si rempli)
    if (!empty($_POST['engin_nom'])) {
        $stmt = $pdo->prepare("INSERT INTO engin (nom, type, etat, disponibilite, id_chantier) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['engin_nom'],
            $_POST['engin_type'],
            $_POST['engin_etat'],
            $_POST['engin_disponibilite'],
            $id_chantier
        ]);
    }

    // 5. Ajouter un matériel (si rempli)
    if (!empty($_POST['materiel_nom'])) {
        $stmt = $pdo->prepare("INSERT INTO materiel (nom, type, etat, id_chantier) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['materiel_nom'],
            $_POST['materiel_type'],
            $_POST['materiel_etat'],
            $id_chantier
        ]);
    }

    // 6. Ajouter un fournisseur (si rempli)
    $id_fournisseur = null;
    if (!empty($_POST['fournisseur_nom'])) {
        $stmt = $pdo->prepare("INSERT INTO fournisseur (nom, contact, specialite) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['fournisseur_nom'],
            $_POST['fournisseur_contact'],
            $_POST['fournisseur_specialite']
        ]);
        $id_fournisseur = $pdo->lastInsertId();
    }

    // 7. Ajouter une commande (si fournisseur rempli)
    $id_commande = null;
    if ($id_fournisseur && !empty($_POST['commande_date'])) {
        $stmt = $pdo->prepare("INSERT INTO commande (date_commande, id_fournisseur, id_chantier) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['commande_date'],
            $id_fournisseur,
            $id_chantier
        ]);
        $id_commande = $pdo->lastInsertId();
    }

    // 8. Ajouter une facture (si commande créée)
    if ($id_commande && !empty($_POST['facture_montant'])) {
        $stmt = $pdo->prepare("INSERT INTO facture (montant, date_facture, id_commande) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['facture_montant'],
            $_POST['facture_date'],
            $id_commande
        ]);
    }

    // Commit final
    $pdo->commit();

    echo "<h2>✅ Toutes les informations ont été enregistrées avec succès !</h2>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "<h2>❌ Une erreur est survenue : " . $e->getMessage() . "</h2>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Succès</title>
    <meta http-equiv="refresh" content="3;url=formulaire_global.php">
    <link rel="stylesheet" href="style.css"> <!-- ملف CSS مستقل إذا بغيت -->
    <style>
        body {
            background-color: #f5f5f5;
            text-align: center;
            padding-top: 100px;
            font-family: Arial, sans-serif;
        }
        .success-box {
            background-color: #d4edda;
            color: #155724;
            display: inline-block;
            padding: 20px 30px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
            font-size: 18px;
        }
    </style>
</head>
<body>

    <div class="success-box">
        ✅ Toutes les informations ont été enregistrées avec succès !<br>
        ⏳ Redirection vers le formulaire dans 3 secondes...
    </div>