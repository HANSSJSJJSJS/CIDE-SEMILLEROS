<!-- Modal para crear/editar evento -->
<div class="modal" id="eventModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Nueva Reunión</h2>
            <button class="close-btn" id="closeModal">&times;</button>
        </div>

        <div class="selected-date-display" id="selectedDateDisplay"></div>

        <form id="eventForm">
            @csrf
            <input type="hidden" name="id" id="event-id">

            <div class="form-group">
                <label for="eventTitle">Título de la reunión *</label>
                <input type="text" id="eventTitle" name="titulo" required placeholder="Ej: Reunión de proyecto">
            </div>

            <div class="form-group">
                <label for="eventTime">Hora *</label>
                <input type="time" id="eventTime" name="hora" required>
            </div>

            <div class="form-group">
                <label for="eventDuration">Duración (minutos) *</label>
                <select id="eventDuration" name="duracion" required>
                    <option value="30">30 minutos</option>
                    <option value="60" selected>1 hora</option>
                    <option value="90">1.5 horas</option>
                    <option value="120">2 horas</option>
                </select>
            </div>

            <div class="form-group">
                <label for="eventType">Tipo de reunión *</label>
                <select id="eventType" name="tipo" required>
                    <option value="presencial">Presencial</option>
                    <option value="virtual">Virtual</option>
                    <option value="hibrida">Híbrida</option>
                </select>
            </div>

            <div class="form-group">
                <label for="eventLocation">Ubicación</label>
                <input type="text" id="eventLocation" name="ubicacion" placeholder="Ej: Sala de juntas, Zoom, etc.">
            </div>

            <div class="form-group">
                <label for="eventDescription">Descripción</label>
                <textarea id="eventDescription" name="descripcion" placeholder="Detalles adicionales de la reunión..."></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancelar</button>
                <button type="button" class="btn btn-danger" id="deleteBtn" style="display: none;">Eliminar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
