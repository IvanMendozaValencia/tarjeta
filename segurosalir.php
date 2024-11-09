<!-- Modal elimina registro -->
<div class="modal fade" id="segurosalir" tabindex="-1" aria-labelledby="segurosalirLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="segurosalirLabel">Aviso</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Desea Cerrar Sesión?
            </div>

            <div class="modal-footer">
                <form action="logout.php" method="post">
                    <input type="hidden" id="id_tarjeta" name="id_tarjeta">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Regresar</button>
                    <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
                </form>
            </div>

        </div>
    </div>
</div>