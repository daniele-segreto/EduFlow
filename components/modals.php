<?php
/**
 * Modali Bootstrap condivise: evento, materia, lezione, task.
 * I form inviano dati via jQuery AJAX agli endpoint in /ajax.
 */
declare(strict_types=1);
$cats = defined('EVENT_CATEGORIES') ? EVENT_CATEGORIES : [];
?>
<!-- Modal: Evento -->
<div class="modal fade" id="modalEvent" tabindex="-1" aria-labelledby="modalEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-5 fw-semibold" id="modalEventLabel">Evento</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="formEvent">
                    <input type="hidden" name="id" id="event_id" value="">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-medium" for="event_title">Titolo</label>
                            <input type="text" class="form-control" id="event_title" name="title" required maxlength="200">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium" for="event_start">Inizio</label>
                            <input type="datetime-local" class="form-control" id="event_start" name="start_datetime" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium" for="event_end">Fine (opzionale)</label>
                            <input type="datetime-local" class="form-control" id="event_end" name="end_datetime">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium" for="event_category">Categoria</label>
                            <select class="form-select" id="event_category" name="category">
                                <?php foreach (array_keys($cats) as $cat): ?>
                                    <option value="<?= h($cat) ?>"><?= h($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium" for="event_priority">Priorità</label>
                            <select class="form-select" id="event_priority" name="priority">
                                <option value="low">Bassa</option>
                                <option value="medium" selected>Media</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium" for="event_reminder">Promemoria (opzionale)</label>
                            <input type="datetime-local" class="form-control" id="event_reminder" name="reminder_datetime">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="event_completed" name="is_completed" value="1">
                                <label class="form-check-label" for="event_completed">Completato</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium" for="event_description">Descrizione</label>
                            <textarea class="form-control" id="event_description" name="description" rows="3" maxlength="5000"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-danger me-auto d-none" id="btnDeleteEvent"><i class="bi bi-trash me-1"></i>Elimina</button>
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary px-4" id="btnSaveEvent">Salva</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Materia -->
<div class="modal fade" id="modalSubject" tabindex="-1" aria-labelledby="modalSubjectLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-5 fw-semibold" id="modalSubjectLabel">Materia</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="formSubject">
                    <input type="hidden" name="id" id="subject_id" value="">
                    <div class="mb-3">
                        <label class="form-label small fw-medium" for="subject_name">Nome</label>
                        <input type="text" class="form-control" id="subject_name" name="name" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium" for="subject_description">Descrizione</label>
                        <textarea class="form-control" id="subject_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-medium" for="subject_color">Colore</label>
                        <input type="color" class="form-control form-control-color w-100" id="subject_color" name="color" value="#0ea5e9" title="Colore">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary px-4" id="btnSaveSubject">Salva</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Lezione -->
<div class="modal fade" id="modalLesson" tabindex="-1" aria-labelledby="modalLessonLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-5 fw-semibold" id="modalLessonLabel">Lezione</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="formLesson">
                    <input type="hidden" name="id" id="lesson_id" value="">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-medium" for="lesson_title">Titolo</label>
                            <input type="text" class="form-control" id="lesson_title" name="title" required maxlength="200">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium" for="lesson_subject_id">Materia</label>
                            <select class="form-select" id="lesson_subject_id" name="subject_id" required></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-medium" for="lesson_date">Data</label>
                            <input type="date" class="form-control" id="lesson_date" name="lesson_date" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-medium" for="lesson_time">Orario</label>
                            <input type="time" class="form-control" id="lesson_time" name="lesson_time">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium" for="lesson_status">Stato</label>
                            <select class="form-select" id="lesson_status" name="status">
                                <option value="scheduled">Programmata</option>
                                <option value="completed">Completata</option>
                                <option value="cancelled">Annullata</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium" for="lesson_notes">Note</label>
                            <textarea class="form-control" id="lesson_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary px-4" id="btnSaveLesson">Salva</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Task -->
<div class="modal fade" id="modalTask" tabindex="-1" aria-labelledby="modalTaskLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-5 fw-semibold" id="modalTaskLabel">Task</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="formTask">
                    <input type="hidden" name="id" id="task_id" value="">
                    <div class="mb-3">
                        <label class="form-label small fw-medium" for="task_title">Titolo</label>
                        <input type="text" class="form-control" id="task_title" name="title" required maxlength="200">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium" for="task_due">Scadenza</label>
                        <input type="date" class="form-control" id="task_due" name="due_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium" for="task_priority">Priorità</label>
                        <select class="form-select" id="task_priority" name="priority">
                            <option value="low">Bassa</option>
                            <option value="medium" selected>Media</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-medium" for="task_description">Descrizione</label>
                        <textarea class="form-control" id="task_description" name="description" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary px-4" id="btnSaveTask">Salva</button>
            </div>
        </div>
    </div>
</div>
