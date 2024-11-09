<!-- Modal edita registro -->
<div class="modal fade" id="editaModalVisita" tabindex="-1" aria-labelledby="editaModalVisitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editaModalVisitaLabel">Editar visita</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="actualizaVisitas.php" method="post" enctype="multipart/form-data">

                    <input type="hidden" id="id_visita" name="id_visita">
                    <input type="hidden" id="fk_visita_tarjeta" name="fk_visita_tarjeta">

                    <div class="mb-3">
                        <label for="fecha_visita" class="form-label">Fecha</label><label class="form-label" style="color: red">*:</label>
                        <input  type="date" name="fecha_visita" id="fecha_visita"  class="form-control" required>                        
                    </div>

                    <div class="mb-3">
                        <label for="result_visita" class="form-label">Resultado</label><label class="form-label" style="color: red">*:</label>
                        <select name="result_visita" id="result_visita" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($row_listaresultado = $listaresultado->fetch_assoc()) { ?>
                            <option value="<?php echo $row_listaresultado["id_resultado"]; ?>"><?= $row_listaresultado["rv_concepto"] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="personal_visita" class="form-label">Personal</label><label class="form-label" style="color: red">*:</label>
                        <select name="personal_visita" id="personal_visita" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($row_listapersonalvisita = $listapersonalvisita->fetch_assoc()) { ?>
                            <option value="<?php echo $row_listapersonalvisita["id_personalvisita"]; ?>"><?= $row_listapersonalvisita["pv_concepto"] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end pt2">
                        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary ms-1"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>