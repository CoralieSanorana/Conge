<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <main>
        <?php if (session()->getFlashdata('error')) : ?>
                <div class="flash flash-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="flash flash-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
        <form action="<?= base_url('admin/departement/form') ?>" method="post">
            <div class="form-section">
                <h3><i class="bi bi-building" style="color:var(--forest);margin-right:6px"></i>Ajouter un département</h3>
                <div class="f-group">
                    <label class="f-label">Nom du département</label>
                    <input type="text" name="nom" class="f-input" placeholder="Ex: Informatique" value="<?= esc(old('nom', '')) ?>"/>
                </div>
            </div>
            <div class="form-actions">
                <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre</button>
                <a href="<?= site_url('admin/dashboard') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
            </div>
        </form>
    </main>
</body>
</html>