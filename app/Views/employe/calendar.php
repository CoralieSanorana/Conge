<?php
$pageTitle = 'Calendrier de congés';
$employe = $employe ?? [];
$departement = $departement ?? null;
$nomComplet = $nomComplet ?? trim(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? ''));
$departementNom = $departementNom ?? ($departement['nom'] ?? 'Aucun département');
$totalDemandes = $totalDemandes ?? 0;
$initiales = strtoupper(substr($employe['prenom'] ?? 'E', 0, 1) . substr($employe['nom'] ?? 'M', 0, 1));
?>

<?= $this->include('Layouts/header') ?>
<div class="app-wrap">
    <?= $this->include('Layouts/sidebar') ?>
    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Calendrier</div>
                <div class="topbar-breadcrumb">Accueil / Calendrier</div>
            </div>
            <div class="topbar-actions">
                <a href="<?= site_url('employe/conge/demande') ?>" class="btn-forest"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
            </div>
        </div>

        <div class="content">
            <div class="data-card">
                <div class="data-card-head"><h3>Mon calendrier hebdomadaire</h3></div>
                <div style="padding:1rem">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('Layouts/footer') ?>

<!-- FullCalendar CSS/JS (local for offline) -->
<link href="<?= base_url('assets/css/fullcalendar.min.css') ?>" rel="stylesheet">
<script src="<?= base_url('assets/js/fullcalendar.min.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        displayEventTime: true,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        events: '<?= site_url('employe/conge/events') ?>',
        eventDidMount: function(info) {
            const props = info.event.extendedProps || {};
            info.el.setAttribute('title', (props.type ? props.type + ' - ' : '') + (props.statut || ''));
        },
        eventClick: function(info) {
            const props = info.event.extendedProps || {};
            let msg = 'Type : ' + (props.type || '') + '\n';
            msg += 'Statut : ' + (props.statut || '') + '\n';
            msg += 'Du : ' + info.event.startStr + '\n';
            msg += 'Au : ' + info.event.endStr + '\n';
            if (props.motif) msg += '\nMotif : ' + props.motif;
            alert(msg);
        }
    });

    calendar.render();

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function(err){
            console.warn('SW registration failed:', err);
        });
    }
});
</script>
