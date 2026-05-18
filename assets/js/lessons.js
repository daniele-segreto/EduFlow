/**
 * Lezioni: filtri, tabella AJAX, modale salvataggio.
 */
(function ($) {
    'use strict';

    var tLesson = null;

    function statusLabel(s) {
        if (s === 'completed') {
            return 'Completata';
        }
        if (s === 'cancelled') {
            return 'Annullata';
        }
        return 'Programmata';
    }

    function loadSubjectFilter() {
        $.getJSON(App.url('ajax/subject_list.php'), function (res) {
            var $f = $('#filterLessonSubject');
            var cur = $f.val();
            $f.find('option:not(:first)').remove();
            (res.subjects || []).forEach(function (s) {
                $f.append($('<option>').val(s.id).text(s.name));
            });
            if (cur) {
                $f.val(cur);
            }
        });
    }

    // Carica l'elenco delle lezioni in base ai filtri e aggiorna la tabella
    function loadLessons() {
        var params = {
            subject_id: $('#filterLessonSubject').val(),
            status: $('#filterLessonStatus').val(),
            q: $('#lessonSearch').val()
        };
        $.getJSON(App.url('ajax/lesson_list.php'), params, function (res) {
            var $tb = $('#lessonTable').empty();
            if (!res.success) {
                App.toast(res.message || 'Errore', 'danger');
                return;
            }
            if (!(res.lessons || []).length) {
                $tb.append('<tr><td colspan="6" class="text-center text-muted py-5">Nessuna lezione trovata</td></tr>');
                return;
            }
            (res.lessons || []).forEach(function (l) {
                var time = l.lesson_time ? String(l.lesson_time).slice(0, 5) : '—';
                var tr = $('<tr></tr>');
                tr.append('<td class="fw-medium">' + $('<div>').text(l.title).html() + '</td>');
                tr.append('<td><span class="badge rounded-pill text-white" style="background:' + (l.subject_color || '#0ea5e9') + '">' + $('<span>').text(l.subject_name).html() + '</span></td>');
                tr.append('<td>' + $('<span>').text(l.lesson_date).html() + '</td>');
                tr.append('<td>' + $('<span>').text(time).html() + '</td>');
                tr.append('<td>' + statusLabel(l.status) + '</td>');
                var actions = $('<td class="text-end"></td>');
                actions.append('<button class="btn btn-sm btn-light border me-1 btn-edit-lesson" data-id="' + l.id + '">Modifica</button>');
                actions.append('<button class="btn btn-sm btn-outline-danger btn-del-lesson" data-id="' + l.id + '">Elimina</button>');
                tr.append(actions);
                $tb.append(tr);
            });
        });
    }

    $(function () {
        loadSubjectFilter();
        loadLessons();

        $('#filterLessonSubject, #filterLessonStatus').on('change', loadLessons);
        $('#lessonSearch').on('input', function () {
            clearTimeout(tLesson);
            tLesson = setTimeout(loadLessons, 250);
        });

        $('#btnNewLesson').on('click', function () {
            $('#lesson_id').val('');
            $('#formLesson')[0].reset();
            App.refreshSubjectOptions();
        });

        $('#lessonTable').on('click', '.btn-edit-lesson', function () {
            var id = $(this).data('id');
            $.getJSON(App.url('ajax/lesson_get.php?id=' + encodeURIComponent(id)), function (res) {
                if (!res.success || !res.lesson) {
                    App.toast('Lezione non trovata', 'danger');
                    return;
                }
                var l = res.lesson;
                App.refreshSubjectOptions();
                setTimeout(function () {
                    $('#lesson_id').val(l.id);
                    $('#lesson_title').val(l.title);
                    $('#lesson_subject_id').val(String(l.subject_id));
                    $('#lesson_date').val(l.lesson_date);
                    $('#lesson_time').val(l.lesson_time || '');
                    $('#lesson_notes').val(l.notes || '');
                    $('#lesson_status').val(l.status);
                    App.openModal('modalLesson');
                }, 200);
            });
        });

        $('#lessonTable').on('click', '.btn-del-lesson', function () {
            var id = $(this).data('id');
            if (!window.confirm('Eliminare questa lezione?')) {
                return;
            }
            App.ajaxJson({ url: App.url('ajax/lesson_delete.php'), data: { id: id } })
                .done(function (res) {
                    if (res.success) {
                        App.toast(res.message, 'success');
                        loadLessons();
                        $(document).trigger('app:lessons-changed');
                    } else {
                        App.toast(res.message || 'Errore', 'danger');
                    }
                });
        });

        $(document).on('app:lessons-changed', function () {
            loadLessons();
            loadSubjectFilter();
        });

        $(document).on('app:subjects-changed', function () {
            loadSubjectFilter();
        });
    });
})(jQuery);
