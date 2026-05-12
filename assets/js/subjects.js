/**
 * Materie: lista AJAX, ricerca live, modale CRUD.
 */
(function ($) {
    'use strict';

    var timer = null;

    function cardHtml(s) {
        var desc = s.description ? $('<div>').text(s.description).html() : '<span class="text-muted small">Nessuna descrizione</span>';
        return (
            '<div class="col-sm-6 col-xl-4">' +
            '<div class="subject-card h-100 d-flex flex-column">' +
            '<div class="color-bar" style="background:' + s.color + '"></div>' +
            '<div class="p-3 flex-grow-1 d-flex flex-column">' +
            '<div class="d-flex justify-content-between align-items-start gap-2 mb-2">' +
            '<h3 class="h5 mb-0 fw-semibold">' + $('<div>').text(s.name).html() + '</h3>' +
            '<div class="dropdown">' +
            '<button class="btn btn-sm btn-light border rounded-pill" data-bs-toggle="dropdown" aria-label="Azioni"><i class="bi bi-three-dots"></i></button>' +
            '<ul class="dropdown-menu dropdown-menu-end shadow border-0">' +
            '<li><button class="dropdown-item btn-edit-subject" data-id="' + s.id + '">Modifica</button></li>' +
            '<li><button class="dropdown-item text-danger btn-del-subject" data-id="' + s.id + '">Elimina</button></li>' +
            '</ul></div></div>' +
            '<p class="small text-muted flex-grow-1 mb-3">' + desc + '</p>' +
            '<div class="d-flex align-items-center justify-content-between mt-auto">' +
            '<span class="badge rounded-pill bg-light text-muted border"><i class="bi bi-journal-text me-1"></i>' + (s.lessons_count || 0) + ' lezioni</span>' +
            '<span class="small text-muted">ID ' + s.id + '</span>' +
            '</div></div></div></div>'
        );
    }

    function loadSubjects() {
        var q = $('#subjectSearch').val() || '';
        $.getJSON(App.url('ajax/subject_list.php'), { q: q }, function (res) {
            var $g = $('#subjectGrid').empty();
            if (!res.success) {
                App.toast(res.message || 'Errore', 'danger');
                return;
            }
            if (!(res.subjects || []).length) {
                $g.append('<div class="col-12"><div class="empty-state">Nessuna materia trovata.</div></div>');
                return;
            }
            (res.subjects || []).forEach(function (s) {
                $g.append(cardHtml(s));
            });
        });
    }

    $(function () {
        loadSubjects();

        $('#subjectSearch').on('input', function () {
            clearTimeout(timer);
            timer = setTimeout(loadSubjects, 220);
        });

        $('#btnNewSubject').on('click', function () {
            $('#subject_id').val('');
            $('#formSubject')[0].reset();
            $('#subject_color').val('#0ea5e9');
        });

        $('#subjectGrid').on('click', '.btn-edit-subject', function () {
            var id = $(this).data('id');
            $.getJSON(App.url('ajax/subject_list.php'), function (res) {
                var s = (res.subjects || []).find(function (x) {
                    return String(x.id) === String(id);
                });
                if (!s) {
                    App.toast('Materia non trovata', 'danger');
                    return;
                }
                $('#subject_id').val(s.id);
                $('#subject_name').val(s.name);
                $('#subject_description').val(s.description || '');
                $('#subject_color').val(s.color || '#0ea5e9');
                App.openModal('modalSubject');
            });
        });

        $('#subjectGrid').on('click', '.btn-del-subject', function () {
            var id = $(this).data('id');
            if (!window.confirm('Eliminare la materia? Le lezioni collegate verranno eliminate (CASCADE).')) {
                return;
            }
            App.ajaxJson({ url: App.url('ajax/subject_delete.php'), data: { id: id } })
                .done(function (res) {
                    if (res.success) {
                        App.toast(res.message, 'success');
                        loadSubjects();
                        App.refreshSubjectOptions();
                        $(document).trigger('app:subjects-changed');
                        $(document).trigger('app:lessons-changed');
                    } else {
                        App.toast(res.message || 'Errore', 'danger');
                    }
                });
        });

        $(document).on('app:subjects-changed', loadSubjects);
    });
})(jQuery);
