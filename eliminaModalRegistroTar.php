<!-- Modal elimina registro -->
<div class="modal fade" id="eliminaModalRegistroTar" tabindex="-1" aria-labelledby="eliminaModalRegistroTarLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="eliminaModalRegistroTarLabel">Aviso</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Desea eliminar el registro de la paciente?
            </div>

            <div class="modal-footer">
                <form action="eliminaRegistroTar.php" method="post">
                    <input type="hidden" id="id_tarjeta" name="id_tarjeta">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>

        </div>
    </div>
</div>