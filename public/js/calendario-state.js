// Estado global del calendario
window.calendarState = {
    currentDate: new Date(),
    currentView: 'month',
    eventos: [],
    editingEventId: null,
};

// Funciones para manipular el estado global
window.setCurrentDate = function(date) {
    window.calendarState.currentDate = date;
    if (typeof updatePeriodLabel === 'function') {
        updatePeriodLabel();
    }
};

window.setCurrentView = function(view) {
    window.calendarState.currentView = view;
    if (typeof renderView === 'function') {
        renderView();
    }
};

window.setEventos = function(eventos) {
    window.calendarState.eventos = eventos;
    if (typeof renderView === 'function') {
        renderView();
    }
};

// Inicialización del calendario
document.addEventListener('DOMContentLoaded', function() {
    // Asegurarnos de que las variables globales estén disponibles
    window.currentDate = window.calendarState.currentDate;
    window.currentView = window.calendarState.currentView;
    window.eventos = window.calendarState.eventos;
    
    // Cargar eventos iniciales
    if (typeof cargarEventos === 'function') {
        cargarEventos();
    }
});