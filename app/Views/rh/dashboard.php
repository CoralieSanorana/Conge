<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord RH</title>
</head>
<body>
    <div style="padding:24px;font-family:Arial, sans-serif">
        <h1>Tableau de bord RH</h1>
        <p>Demandes en attente: <?= esc($pendingCount ?? 0) ?></p>
        <p>Demandes approuvées: <?= esc($approvedCount ?? 0) ?></p>
        <p>Demandes refusées: <?= esc($rejectedCount ?? 0) ?></p>
        <p><a href="<?= site_url('login') ?>">Retour à la connexion</a></p>
    </div>
</body>
</html>
