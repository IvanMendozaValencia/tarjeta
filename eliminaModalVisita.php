<!-- Modal elimina registro -->
<div class="modal fade" id="eliminaModalVisita" tabindex="-1" aria-labelledby="eliminaModalVisitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="eliminaModalVisitaLabel">Aviso</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Desea eliminar la visita?
            </div>

            <div class="modal-footer">
                <form action="eliminaVisitas.php" method="post">
                    <input type="hidden" id="id_visita" name="id_visita">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>

        </div>
    </div>
</div>