/**
 * Dashboard: caricamento dati aggregati e rendering liste.
 */
(function ($) {
    'use strict';

    function fmtDate(d) {
        if (!d) {
            return '';
        }
        var x = new Date(d.replace(' ', 'T'));
        return isNaN(x.getTime()) ? d : x.toLocaleDateString('it-IT', { weekday: 'short', day: '2-digit', month: 'short' });
    }

    function fmtTime(t) {
        if (!t) {
            return '';
        }
        return String(t).slice(0, 5);
    }

    function lessonStatusLabel(s) {
        if (s === 'completed') {
            return 'Completata';
        }
        if (s === 'cancelled') {
            return 'Annullata';
        }
        return 'Programmata';
    }

    function priorityLabel(p) {
        var m = { low: 'Bassa', medium: 'Media', high: 'Alta', urgent: 'Urgente' };
        return m[p] || p;
    }

    // Genera i contenuti della dashboard utilizzando i dati caricati
    function renderDashboard(data) {
        if (!data.success) {
            App.toast(data.message || 'Errore caricamento', 'danger');
            return;
        }

        $('#dashTodayBadge').text(data.today || '');

        var st = data.stats || {};
        $('#statSubjects').text(st.subjects != null ? st.subjects : '0');
        $('#statLessonsWeek').text(st.lessons_week != null ? st.lessons_week : '0');
        $('#statTasksOpen').text(st.tasks_open != null ? st.tasks_open : '0');
        $('#statEventsUp').text(st.events_upcoming != null ? st.events_upcoming : '0');

        var lt = data.lessons_today || [];
        var $l = $('#dashLessonsToday').empty();
        if (!lt.length) {
            $l.append('<li class="text-muted">Nessuna lezione oggi</li>');
        } else {
            lt.forEach(function (row) {
                var line = (row.lesson_time ? fmtTime(row.lesson_time) + ' · ' : '') + $('<div>').text(row.title).html();
                $l.append('<li class="mb-2"><span class="badge rounded-pill me-1" style="background:' + row.color + '">&nbsp;</span>' + line + '</li>');
            });
        }

        var et = data.events_today || [];
        var $e = $('#dashEventsToday').empty();
        if (!et.length) {
            $e.append('<li class="text-muted">Nessun evento oggi</li>');
        } else {
            et.forEach(function (row) {
                var when = fmtDate(row.start_datetime) + ' ' + (row.start_datetime ? row.start_datetime.slice(11, 16) : '');
                $e.append('<li class="mb-2"><strong>' + $('<div>').text(row.title).html() + '</strong><br><span class="text-muted">' + $('<div>').text(when).html() + '</span></li>');
            });
        }

        var tasks = (data.tasks_open || []).filter(function (t) {
            return t.priority === 'urgent' || t.priority === 'high';
        });
        var $tu = $('#dashTasksUrgent').empty();
        if (!tasks.length) {
            $tu.append('<p class="text-muted small mb-0">Nessun task urgente in cima alla lista.</p>');
        } else {
            tasks.slice(0, 6).forEach(function (t) {
                var badge = t.priority === 'urgent' ? 'danger' : 'warning';
                var row = $('<div class="d-flex align-items-center justify-content-between border rounded-3 px-3 py-2 mb-2"></div>');
                row.append('<div class="me-2"><span class="badge text-bg-' + badge + ' me-2">' + priorityLabel(t.priority) + '</span><span class="small">' + $('<span>').text(t.title).html() + '</span></div>');
                var cb = $('<input type="checkbox" class="form-check-input">').prop('checked', parseInt(t.is_completed, 10) === 1);
                cb.on('change', function () {
                    App.ajaxJson({ url: App.url('ajax/task_toggle.php'), data: { id: t.id, is_completed: $(this).is(':checked') ? 1 : 0 } })
                        .done(function (res) {
                            if (res.success) {
                                App.toast('Aggiornato', 'success');
                                loadDash();
                            }
                        });
                });
                row.append(cb);
                $tu.append(row);
            });
        }

        var lu = data.lessons_upcoming || [];
        var $lu = $('#dashLessonsUp').empty();
        if (!lu.length) {
            $lu.append('<tr><td colspan="4" class="text-muted text-center py-4">Nessuna lezione programmata</td></tr>');
        } else {
            lu.forEach(function (row) {
                $lu.append(
                    '<tr><td>' + $('<div>').text(row.title).html() + '</td><td><span class="badge rounded-pill" style="background:' + row.color + '">' + $('<span>').text(row.subject_name).html() + '</span></td><td>' + $('<span>').text(row.lesson_date + ' ' + fmtTime(row.lesson_time)).html() + '</td><td>' + lessonStatusLabel(row.status) + '</td></tr>'
                );
            });
        }

        var eu = data.events_upcoming || [];
        var $eu = $('#dashEventsUp').empty();
        if (!eu.length) {
            $eu.append('<tr><td colspan="3" class="text-muted text-center py-4">Nessun evento in programma</td></tr>');
        } else {
            eu.forEach(function (row) {
                var when = row.start_datetime ? row.start_datetime.replace('T', ' ') : '';
                $eu.append('<tr><td>' + $('<div>').text(row.title).html() + '</td><td>' + $('<span>').text(row.category).html() + '</td><td>' + $('<span>').text(when).html() + '</td></tr>');
            });
        }

        var mini = data.calendar_mini || {};
        var ld = mini.lesson_days || {};
        var ed = mini.event_days || {};
        var start = new Date(data.today + 'T12:00:00');
        var $mc = $('#dashMiniCal').empty();
        var row = $('<div class="row g-2"></div>');
        function localIso(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }
        for (var i = 0; i < 14; i++) {
            var d = new Date(start.getTime());
            d.setDate(start.getDate() + i);
            var iso = localIso(d);
            var nLess = ld[iso] != null ? parseInt(ld[iso], 10) : 0;
            var nEv = ed[iso] != null ? parseInt(ed[iso], 10) : 0;
            var col = $('<div class="col-6 col-sm-4 col-md-3 col-lg-2"></div>');
            var card = $('<div class="border rounded-3 p-2 text-center small h-100 bg-white"></div>');
            card.append('<div class="fw-semibold">' + d.toLocaleDateString('it-IT', { weekday: 'short', day: '2-digit', month: 'short' }) + '</div>');
            card.append('<div class="text-muted mt-1">' + (nLess ? '<span class="me-1"><i class="bi bi-journal-text text-primary"></i>' + nLess + '</span>' : '') + (nEv ? '<span><i class="bi bi-calendar-event text-info"></i>' + nEv + '</span>' : (!nLess ? '—' : '')) + '</div>');
            col.append(card);
            row.append(col);
        }
        $mc.append(row);
    }

    // Effettua la richiesta per caricare i dati della dashboard
    function loadDash() {
        $.getJSON(App.url('ajax/dashboard_data.php'), renderDashboard);
    }

    $(function () {
        loadDash();
        $('#btnDashRefresh').on('click', loadDash);
        $(document).on('app:tasks-changed', loadDash);

        $('#btnNewTaskDash').on('click', function () {
            $('#task_id').val('');
            $('#formTask')[0].reset();
        });
    });
})(jQuery);
