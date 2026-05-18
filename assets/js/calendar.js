/**
 * Calendario: navigazione, fetch AJAX, rendering e modale evento.
 */
(function ($) {
    'use strict';

    var state = {
        view: 'month',
        cursor: new Date(),
        items: []
    };

    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    // Restituisce la data formattata come stringa ISO (AAAA-MM-GG)
    function isoDate(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    // Calcola e restituisce il lunedì della settimana in cui cade la data fornita
    function startOfMonday(d) {
        var x = new Date(d.getFullYear(), d.getMonth(), d.getDate());
        var dow = (x.getDay() + 6) % 7;
        x.setDate(x.getDate() - dow);
        return x;
    }

    function addDays(d, n) {
        var x = new Date(d.getTime());
        x.setDate(x.getDate() + n);
        return x;
    }

    // Recupera i metadati relativi al mese (es. primo e ultimo giorno, totale giorni)
    function monthMeta(year, month) {
        var first = new Date(year, month - 1, 1);
        var last = new Date(year, month, 0);
        var startPad = (first.getDay() + 6) % 7;
        var totalDays = last.getDate();
        return { first: first, last: last, startPad: startPad, totalDays: totalDays };
    }

    function rangeForView() {
        var v = state.view;
        var c = state.cursor;
        if (v === 'day') {
            var s = new Date(c.getFullYear(), c.getMonth(), c.getDate());
            return { start: s, end: s };
        }
        if (v === 'week') {
            var mon = startOfMonday(c);
            return { start: mon, end: addDays(mon, 6) };
        }
        var meta = monthMeta(c.getFullYear(), c.getMonth() + 1);
        var gridStart = addDays(meta.first, -meta.startPad);
        var gridEnd = addDays(gridStart, 41);
        return { start: gridStart, end: gridEnd };
    }

    function filteredItems() {
        var cat = $('#calFilterCategory').val();
        var typ = $('#calFilterType').val();
        return (state.items || []).filter(function (it) {
            if (typ === 'event' && it.type !== 'event') {
                return false;
            }
            if (typ === 'lesson' && it.type !== 'lesson') {
                return false;
            }
            if (cat !== 'all' && it.category !== cat) {
                return false;
            }
            return true;
        });
    }

    function itemsForDay(iso) {
        return filteredItems().filter(function (it) {
            var s = String(it.start || '').replace('T', ' ');
            return s.slice(0, 10) === iso;
        });
    }

    // Carica i dati del calendario dal server tramite richiesta AJAX
    function loadCalendar(cb) {
        var r = rangeForView();
        var qs = $.param({ start: isoDate(r.start), end: isoDate(r.end) });
        $.getJSON(App.url('ajax/load_calendar.php?' + qs), function (res) {
            if (res.success) {
                state.items = res.items || [];
            } else {
                state.items = [];
                App.toast(res.message || 'Errore calendario', 'danger');
            }
            if (cb) {
                cb();
            } else {
                render();
            }
        }).fail(function () {
            App.toast('Errore di rete', 'danger');
        });
    }

    function setTitle() {
        var c = state.cursor;
        var r = rangeForView();
        var t = '';
        if (state.view === 'month') {
            t = c.toLocaleDateString('it-IT', { month: 'long', year: 'numeric' });
        } else if (state.view === 'week') {
            t = 'Settimana dal ' + r.start.toLocaleDateString('it-IT') + ' al ' + r.end.toLocaleDateString('it-IT');
        } else {
            t = c.toLocaleDateString('it-IT', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });
        }
        $('#calTitle').text(t);
        $('#calRangeHint').text('Intervallo caricato: ' + isoDate(r.start) + ' → ' + isoDate(r.end));
    }

    // Genera l'HTML per la vista mensile del calendario
    function renderMonth() {
        var c = state.cursor;
        var y = c.getFullYear();
        var m = c.getMonth() + 1;
        var meta = monthMeta(y, m);
        var gridStart = addDays(meta.first, -meta.startPad);

        var dow = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
        var $wrap = $('<div class="calendar-grid"></div>');
        var $rowh = $('<div class="row g-2 mb-2"></div>');
        dow.forEach(function (d) {
            $rowh.append('<div class="col"><div class="small text-muted text-center fw-semibold">' + d + '</div></div>');
        });
        $wrap.append($rowh);

        var todayIso = isoDate(new Date());
        for (var w = 0; w < 6; w++) {
            var $row = $('<div class="row g-2 mb-2"></div>');
            for (var d = 0; d < 7; d++) {
                var cellDate = addDays(gridStart, w * 7 + d);
                var iso = isoDate(cellDate);
                var inMonth = cellDate.getMonth() === meta.first.getMonth();
                var cls = 'cal-cell h-100 ' + (inMonth ? '' : 'muted ') + (iso === todayIso ? 'is-today' : '');
                var $col = $('<div class="col"></div>');
                var $cell = $('<div class="' + cls + '" data-date="' + iso + '"></div>');
                $cell.append('<div class="d-flex justify-content-between align-items-center mb-1"><span class="fw-semibold">' + cellDate.getDate() + '</span></div>');
                var list = itemsForDay(iso);
                var $ev = $('<div></div>');
                list.slice(0, 4).forEach(function (it) {
                    var pill = $('<div class="cal-event-pill text-white" style="background:' + (it.color || '#64748b') + '"></div>');
                    pill.text(it.title);
                    pill.attr('data-item-type', it.type);
                    if (it.type === 'event') {
                        pill.attr('data-event-id', it.id);
                    }
                    $ev.append(pill);
                });
                if (list.length > 4) {
                    $ev.append('<div class="small text-muted">+' + (list.length - 4) + '</div>');
                }
                $cell.append($ev);
                $col.append($cell);
                $row.append($col);
            }
            $wrap.append($row);
        }
        $('#calBody').empty().append($wrap);
    }

    function renderWeek() {
        var mon = startOfMonday(state.cursor);
        var dow = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
        var $row = $('<div class="row g-2"></div>');
        for (var i = 0; i < 7; i++) {
            var d = addDays(mon, i);
            var iso = isoDate(d);
            var $col = $('<div class="col-md"></div>');
            var card = $('<div class="week-col p-2"></div>');
            card.append('<div class="fw-semibold mb-2">' + dow[i] + ' ' + d.getDate() + '/' + pad(d.getMonth() + 1) + '</div>');
            itemsForDay(iso).forEach(function (it) {
                var b = $('<div class="cal-event-pill text-white mb-1" style="background:' + (it.color || '#64748b') + '"></div>');
                b.text(it.title);
                if (it.type === 'event') {
                    b.attr('data-event-id', it.id);
                }
                card.append(b);
            });
            $col.append(card);
            $row.append($col);
        }
        $('#calBody').empty().append($row);
    }

    function renderDay() {
        var d = new Date(state.cursor.getFullYear(), state.cursor.getMonth(), state.cursor.getDate());
        var iso = isoDate(d);
        var $box = $('<div class="week-col p-3"></div>');
        $box.append('<h3 class="h6 text-muted mb-3">' + d.toLocaleDateString('it-IT', { weekday: 'long', day: '2-digit', month: 'long' }) + '</h3>');
        var list = itemsForDay(iso);
        if (!list.length) {
            $box.append('<p class="text-muted mb-0">Nessun impegno in questa giornata.</p>');
        } else {
            list.forEach(function (it) {
                var row = $('<div class="d-flex align-items-start gap-2 border rounded-3 p-2 mb-2"></div>');
                row.append('<span class="rounded-circle mt-1" style="width:10px;height:10px;background:' + (it.color || '#64748b') + '"></span>');
                var txt = $('<div class="small flex-grow-1"></div>');
                txt.append('<div class="fw-semibold">' + $('<div>').text(it.title).html() + '</div>');
                txt.append('<div class="text-muted">' + (it.start || '').replace('T', ' ') + ' · ' + $('<span>').text(it.category || '').html() + '</div>');
                row.append(txt);
                if (it.type === 'event') {
                    row.attr('data-event-id', it.id);
                    row.addClass('cal-day-event-row');
                }
                $box.append(row);
            });
        }
        $('#calBody').empty().append($box);
    }

    function render() {
        setTitle();
        if (state.view === 'month') {
            renderMonth();
        } else if (state.view === 'week') {
            renderWeek();
        } else {
            renderDay();
        }
    }

    function openNewEventForDate(iso) {
        $('#event_id').val('');
        $('#formEvent')[0].reset();
        $('#event_start').val(iso + 'T09:00');
        $('#event_category').val('Eventi');
        App.openModal('modalEvent');
    }

    function openEditEvent(id) {
        $.getJSON(App.url('ajax/get_event.php?id=' + encodeURIComponent(id)), function (res) {
            if (!res.success || !res.event) {
                App.toast('Evento non trovato', 'danger');
                return;
            }
            var e = res.event;
            $('#event_id').val(e.id);
            $('#event_title').val(e.title);
            $('#event_start').val(e.start_local || '');
            $('#event_end').val(e.end_local || '');
            $('#event_reminder').val(e.reminder_local || '');
            $('#event_category').val(e.category);
            $('#event_priority').val(e.priority);
            $('#event_description').val(e.description || '');
            $('#event_completed').prop('checked', parseInt(e.is_completed, 10) === 1);
            App.openModal('modalEvent');
        });
    }

    $(function () {
        $('[data-cal-view]').on('click', function () {
            $('[data-cal-view]').removeClass('active');
            $(this).addClass('active');
            state.view = $(this).data('cal-view');
            loadCalendar();
        });

        $('#btnCalPrev').on('click', function () {
            if (state.view === 'month') {
                state.cursor.setMonth(state.cursor.getMonth() - 1);
            } else if (state.view === 'week') {
                state.cursor = addDays(state.cursor, -7);
            } else {
                state.cursor = addDays(state.cursor, -1);
            }
            loadCalendar();
        });

        $('#btnCalNext').on('click', function () {
            if (state.view === 'month') {
                state.cursor.setMonth(state.cursor.getMonth() + 1);
            } else if (state.view === 'week') {
                state.cursor = addDays(state.cursor, 7);
            } else {
                state.cursor = addDays(state.cursor, 1);
            }
            loadCalendar();
        });

        $('#btnCalToday').on('click', function () {
            state.cursor = new Date();
            loadCalendar();
        });

        $('#calFilterCategory, #calFilterType').on('change', function () {
            render();
        });

        $('#btnCalNewEvent').on('click', function () {
            var r = rangeForView();
            var iso = isoDate(state.view === 'month' ? new Date(state.cursor.getFullYear(), state.cursor.getMonth(), 1) : state.cursor);
            if (state.view === 'week' || state.view === 'day') {
                iso = isoDate(state.cursor);
            }
            openNewEventForDate(iso);
        });

        $('#calBody').on('click', '[data-item-type="lesson"]', function (e) {
            e.stopPropagation();
            window.location.href = App.url('pages/lessons.php');
        });

        $('#calBody').on('click', '.cal-cell', function (e) {
            if ($(e.target).closest('[data-item-type="lesson"]').length) {
                return;
            }
            var t = $(e.target);
            if (t.closest('[data-event-id]').length) {
                var id = t.closest('[data-event-id]').data('event-id');
                openEditEvent(id);
                return;
            }
            var iso = $(this).data('date');
            if (iso) {
                openNewEventForDate(iso);
            }
        });

        $('#calBody').on('click', '[data-event-id]', function (e) {
            e.stopPropagation();
            var id = $(this).data('event-id');
            if (id) {
                openEditEvent(id);
            }
        });

        $('#calBody').on('click', '.cal-day-event-row', function () {
            var id = $(this).data('event-id');
            if (id) {
                openEditEvent(id);
            }
        });

        $(document).on('app:events-changed', function () {
            loadCalendar();
        });

        $(document).on('app:lessons-changed', function () {
            loadCalendar();
        });

        loadCalendar();
    });
})(jQuery);
