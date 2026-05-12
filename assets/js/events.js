/**
 * Eventi: filtri dinamici, modifica in modale, toggle completamento.
 */
(function ($) {
    'use strict';

    var tEv = null;

    function priorityBadge(p) {
        var m = { low: 'secondary', medium: 'info', high: 'warning', urgent: 'danger' };
        var lbl = { low: 'Bassa', medium: 'Media', high: 'Alta', urgent: 'Urgente' };
        var c = m[p] || 'secondary';
        return '<span class="badge text-bg-' + c + '">' + (lbl[p] || p) + '</span>';
    }

    function loadEvents() {
        var params = {
            show: $('#filterEventShow').val(),
            category: $('#filterEventCategory').val(),
            q: $('#eventSearch').val()
        };
        $.getJSON(App.url('ajax/list_events.php'), params, function (res) {
            var $tb = $('#tblEvents').empty();
            if (!res.success) {
                App.toast(res.message || 'Errore', 'danger');
                return;
            }
            if (!(res.events || []).length) {
                $tb.append('<tr><td colspan="7" class="text-center text-muted py-5">Nessun evento trovato</td></tr>');
                return;
            }
            (res.events || []).forEach(function (e) {
                var done = parseInt(e.is_completed, 10) === 1;
                var tr = $('<tr></tr>');
                if (done) {
                    tr.addClass('text-muted');
                }
                var cb = $('<input type="checkbox" class="form-check-input evt-done">').prop('checked', done).data('id', e.id);
                tr.append($('<td></td>').append(cb));
                tr.append('<td><span class="fw-medium">' + $('<span>').text(e.title).html() + '</span></td>');
                tr.append('<td class="small">' + $('<span>').text((e.start_datetime || '').replace('T', ' ')).html() + '</td>');
                tr.append('<td><span class="badge rounded-pill bg-light text-body border">' + $('<span>').text(e.category).html() + '</span></td>');
                tr.append('<td>' + priorityBadge(e.priority) + '</td>');
                var rem = e.reminder_datetime ? $('<span>').text(e.reminder_datetime.replace('T', ' ')).html() : '—';
                tr.append('<td class="small">' + rem + '</td>');
                var act = $('<td class="text-end"></td>');
                act.append('<button class="btn btn-sm btn-light border me-1 btn-edit-event" data-id="' + e.id + '">Modifica</button>');
                act.append('<button class="btn btn-sm btn-outline-danger btn-del-event" data-id="' + e.id + '">Elimina</button>');
                tr.append(act);
                $tb.append(tr);
            });
        });
    }

    function openEdit(id) {
        $.getJSON(App.url('ajax/get_event.php?id=' + encodeURIComponent(id)), function (res) {
            if (!res.success || !res.event) {
                App.toast('Evento non trovato', 'danger');
                return;
            }
            var ev = res.event;
            $('#event_id').val(ev.id);
            $('#event_title').val(ev.title);
            $('#event_start').val(ev.start_local || '');
            $('#event_end').val(ev.end_local || '');
            $('#event_reminder').val(ev.reminder_local || '');
            $('#event_category').val(ev.category);
            $('#event_priority').val(ev.priority);
            $('#event_description').val(ev.description || '');
            $('#event_completed').prop('checked', parseInt(ev.is_completed, 10) === 1);
            App.openModal('modalEvent');
        });
    }

    $(function () {
        loadEvents();

        $('#filterEventShow, #filterEventCategory').on('change', loadEvents);
        $('#eventSearch').on('input', function () {
            clearTimeout(tEv);
            tEv = setTimeout(loadEvents, 250);
        });

        $('#btnNewEventPage').on('click', function () {
            $('#event_id').val('');
            $('#formEvent')[0].reset();
            var now = new Date();
            var pad = function (n) {
                return n < 10 ? '0' + n : '' + n;
            };
            var iso = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
            $('#event_start').val(iso);
        });

        $('#tblEvents').on('change', '.evt-done', function () {
            var id = $(this).data('id');
            var done = $(this).is(':checked');
            App.ajaxJson({ url: App.url('ajax/event_toggle.php'), data: { id: id, is_completed: done ? 1 : 0 } })
                .done(function (res) {
                    if (res.success) {
                        App.toast('Stato aggiornato', 'success');
                        $(document).trigger('app:events-changed');
                    } else {
                        App.toast(res.message || 'Errore', 'danger');
                    }
                })
                .fail(function () {
                    App.toast('Errore di rete', 'danger');
                });
        });

        $('#tblEvents').on('click', '.btn-edit-event', function () {
            openEdit($(this).data('id'));
        });

        $('#tblEvents').on('click', '.btn-del-event', function () {
            var id = $(this).data('id');
            if (!window.confirm('Eliminare questo evento?')) {
                return;
            }
            App.ajaxJson({ url: App.url('ajax/delete_event.php'), data: { id: id } })
                .done(function (res) {
                    if (res.success) {
                        App.toast(res.message, 'success');
                        loadEvents();
                        $(document).trigger('app:events-changed');
                    } else {
                        App.toast(res.message || 'Errore', 'danger');
                    }
                });
        });

        $(document).on('app:events-changed', loadEvents);
    });
})(jQuery);
