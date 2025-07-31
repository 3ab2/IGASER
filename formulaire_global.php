<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
    <title>Formulaire Global - IGASER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="form-container">
    <h2>Ajouter un nouveau Projet avec tous les détails</h2>
    <form action="insert_global.php" method="post">

        <h3>Projet</h3>
        <label>Nom du projet:</label><br>
        <input type="text" name="projet_nom" required><br>
        <label>Lieu:</label><br>
        <input type="text" name="projet_lieu" required><br>
        <label>Budget:</label><br>
        <input type="number" step="0.01" name="projet_budget" required><br>
        <label>Date début:</label><br>
        <input type="date" name="projet_date_debut" required><br>
        <label>Date fin:</label><br>
        <input type="date" name="projet_date_fin" required><br>

        <h3>Chantier</h3>
        <label>Nom:</label><br>
        <input type="text" name="chantier_nom" required><br>
        <label>Lieu:</label><br>
        <input type="text" name="chantier_lieu" required><br>
        <label>État:</label><br>
        <input type="text" name="chantier_etat" required><br>

        <h3>Employé</h3>
        <label>Nom:</label><br>
        <input type="text" name="employe_nom"><br>
        <label>Poste:</label><br>
        <input type="text" name="employe_poste"><br>

        <h3>Engin</h3>
        <label>Nom:</label><br>
        <input type="text" name="engin_nom"><br>
        <label>Type:</label><br>
        <input type="text" name="engin_type"><br>
        <label>État:</label><br>
        <input type="text" name="engin_etat"><br>
        <label>Disponibilité:</label><br>
        <input type="text" name="engin_disponibilite"><br>

        <h3>Matériel</h3>
        <label>Nom:</label><br>
        <input type="text" name="materiel_nom"><br>
        <label>Type:</label><br>
        <input type="text" name="materiel_type"><br>
        <label>État:</label><br>
        <input type="text" name="materiel_etat"><br>

        <h3>Fournisseur</h3>
        <label>Nom:</label><br>
        <input type="text" name="fournisseur_nom"><br>
        <label>Contact:</label><br>
        <input type="text" name="fournisseur_contact"><br>
        <label>Spécialité:</label><br>
        <input type="text" name="fournisseur_specialite"><br>

        <h3>Commande</h3>
        <label>Date de commande:</label><br>
        <input type="date" name="commande_date"><br>

        <h3>Facture</h3>
        <label>Montant:</label><br>
        <input type="number" step="0.01" name="facture_montant"><br>
        <label>Date de facture:</label><br>
        <input type="date" name="facture_date"><br><br>

        <input type="submit" value="Enregistrer tout">
    </form>
    </div>
</body>
</html>
