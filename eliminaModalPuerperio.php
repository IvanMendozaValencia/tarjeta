<!-- Modal elimina registro -->
<div class="modal fade" id="eliminaModalPuerperio" tabindex="-1" aria-labelledby="eliminaModalPuerperioLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="eliminaModalPuerperioLabel">Aviso</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Desea eliminar la cita?
            </div>

            <div class="modal-footer">
                <form action="eliminaCitaPuerperio.php" method="post">
                    <input type="hidden" id="id_puerperio" name="id_puerperio">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>

        </div>
    </div>
</div>