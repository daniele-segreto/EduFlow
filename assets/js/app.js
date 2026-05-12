/**
 * Funzioni globali: toast, helper AJAX, URL base.
 */
(function ($) {
    'use strict';

    window.App = window.App || {};

    App.base = (typeof window.APP_BASE === 'string' ? window.APP_BASE : '').replace(/\/?$/, '/');

    App.url = function (path) {
        path = String(path || '').replace(/^\//, '');
        return App.base + path;
    };

    App.toast = function (message, type) {
        type = type || 'info';
        var bg = 'text-bg-primary';
        if (type === 'success') bg = 'text-bg-success';
        if (type === 'danger') bg = 'text-bg-danger';
        if (type === 'warning') bg = 'text-bg-warning';

        var id = 't' + Date.now();
        var html = '<div id="' + id + '" class="toast align-items-center border-0 ' + bg + '" role="alert" aria-live="assertive" aria-atomic="true">' +
            '<div class="d-flex"><div class="toast-body">' + $('<div>').text(message).html() + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>';

        var $c = $('#appToastContainer');
        $c.append(html);
        var el = document.getElementById(id);
        var t = new bootstrap.Toast(el, { delay: 3800 });
        t.show();
        el.addEventListener('hidden.bs.toast', function () {
            $(el).remove();
        });
    };

    App.ajaxJson = function (opts) {
        return $.ajax({
            url: opts.url,
            method: opts.method || 'POST',
            data: opts.data !== undefined ? JSON.stringify(opts.data) : undefined,
            contentType: 'application/json; charset=UTF-8',
            dataType: 'json'
        });
    };

    App.refreshSubjectOptions = function () {
        var $sel = $('#lesson_subject_id');
        if (!$sel.length) {
            return;
        }
        $.getJSON(App.url('ajax/subject_list.php'), function (res) {
            if (!res.success) {
                return;
            }
            $sel.empty();
            (res.subjects || []).forEach(function (s) {
                $sel.append($('<option>').val(s.id).text(s.name));
            });
        });
    };

    App.openModal = function (id) {
        var el = document.getElementById(id);
        if (!el) {
            return;
        }
        bootstrap.Modal.getOrCreateInstance(el).show();
    };

    function wireModals() {
        $('#btnSaveEvent').on('click', function () {
            var id = parseInt($('#event_id').val(), 10) || 0;
            var payload = {
                id: id,
                title: $('#event_title').val(),
                start_datetime: $('#event_start').val(),
                end_datetime: $('#event_end').val(),
                category: $('#event_category').val(),
                priority: $('#event_priority').val(),
                reminder_datetime: $('#event_reminder').val(),
                description: $('#event_description').val(),
                is_completed: $('#event_completed').is(':checked') ? 1 : 0
            };
            var url = id ? App.url('ajax/update_event.php') : App.url('ajax/add_event.php');
            App.ajaxJson({ url: url, data: payload })
                .done(function (res) {
                    if (res.success) {
                        App.toast(res.message || 'Salvato', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalEvent')).hide();
                        $(document).trigger('app:events-changed');
                    } else {
                        App.toast(res.message || 'Errore', 'danger');
                    }
                })
                .fail(function () {
                    App.toast('Errore di rete', 'danger');
                });
        });

        $('#btnSaveSubject').on('click', function () {
            var id = parseInt($('#subject_id').val(), 10) || 0;
            var payload = {
                id: id,
                name: $('#subject_name').val(),
                description: $('#subject_description').val(),
                color: $('#subject_color').val()
            };
            App.ajaxJson({ url: App.url('ajax/subject_save.php'), data: payload })
                .done(function (res) {
                    if (res.success) {
                        App.toast(res.message || 'Salvato', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalSubject')).hide();
                        App.refreshSubjectOptions();
                        $(document).trigger('app:subjects-changed');
                    } else {
                        App.toast(res.message || 'Errore', 'danger');
                    }
                })
                .fail(function () {
                    App.toast('Errore di rete', 'danger');
                });
        });

        $('#btnSaveLesson').on('click', function () {
            var id = parseInt($('#lesson_id').val(), 10) || 0;
            var payload = {
                id: id,
                title: $('#lesson_title').val(),
                subject_id: parseInt($('#lesson_subject_id').val(), 10),
                lesson_date: $('#lesson_date').val(),
                lesson_time: $('#lesson_time').val(),
                notes: $('#lesson_notes').val(),
                status: $('#lesson_status').val()
            };
            App.ajaxJson({ url: App.url('ajax/lesson_save.php'), data: payload })
                .done(function (res) {
                    if (res.success) {
                        App.toast(res.message || 'Salvato', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalLesson')).hide();
                        $(document).trigger('app:lessons-changed');
                        $(document).trigger('app:events-changed');
                    } else {
                        App.toast(res.message || 'Errore', 'danger');
                    }
                })
                .fail(function () {
                    App.toast('Errore di rete', 'danger');
                });
        });

        $('#btnSaveTask').on('click', function () {
            var id = parseInt($('#task_id').val(), 10) || 0;
            var payload = {
                id: id,
                title: $('#task_title').val(),
                due_date: $('#task_due').val(),
                priority: $('#task_priority').val(),
                description: $('#task_description').val()
            };
            App.ajaxJson({ url: App.url('ajax/task_save.php'), data: payload })
                .done(function (res) {
                    if (res.success) {
                        App.toast(res.message || 'Salvato', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalTask')).hide();
                        $(document).trigger('app:tasks-changed');
                    } else {
                        App.toast(res.message || 'Errore', 'danger');
                    }
                })
                .fail(function () {
                    App.toast('Errore di rete', 'danger');
                });
        });
    }

    $(function () {
        App.refreshSubjectOptions();
        wireModals();

        $('#modalEvent').on('show.bs.modal', function () {
            var id = parseInt($('#event_id').val(), 10) || 0;
            $('#btnDeleteEvent').toggleClass('d-none', id <= 0);
        });

        $('#btnDeleteEvent').on('click', function () {
            var id = parseInt($('#event_id').val(), 10) || 0;
            if (id <= 0) {
                return;
            }
            if (!window.confirm('Eliminare definitivamente questo evento?')) {
                return;
            }
            App.ajaxJson({ url: App.url('ajax/delete_event.php'), data: { id: id } })
                .done(function (res) {
                    if (res.success) {
                        App.toast(res.message || 'Eliminato', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('modalEvent')).hide();
                        $(document).trigger('app:events-changed');
                    } else {
                        App.toast(res.message || 'Errore', 'danger');
                    }
                })
                .fail(function () {
                    App.toast('Errore di rete', 'danger');
                });
        });

        $('#appSidebar .nav-link-app').on('click', function () {
            var oc = bootstrap.Offcanvas.getInstance(document.getElementById('appSidebar'));
            if (oc) {
                oc.hide();
            }
        });
    });
})(jQuery);
