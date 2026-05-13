<?php
$showFooter = $showFooter ?? true;
$footerText = $footerText ?? (date('Y') . ' TechMada RH');
$footerBrand = $footerBrand ?? 'Projet CodeIgniter 4';
$footerNote = $footerNote ?? '';
?>
<?php if ($showFooter) : ?>
<div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc($footerText) ?> <span><?= esc($footerBrand) ?></span><?= $footerNote !== '' ? ' — ' . esc($footerNote) : '' ?></div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click',e=>{
    const t=document.querySelector(a.getAttribute('href'));
    if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'})}
  });
});
</script>
</body>
</html>
