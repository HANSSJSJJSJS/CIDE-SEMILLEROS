// ARREGLA MODALES ATASCADOS DE BOOTSTRAP
document.addEventListener('hidden.bs.modal', function () {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
});
