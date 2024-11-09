<!-- Modal edita registro -->
<div class="modal fade" id="editaModalBajaemb" tabindex="-1" aria-labelledby="editaModalBajaembLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editaModalBajaembLabel">Editar registro</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="actualizaBajaEmb.php" method="post" enctype="multipart/form-data">

                    <input type="hidden" id="id_tarjeta" name="id_tarjeta">

                    <div class="mb-3">
                        <label for="fecha_baja_emb" class="form-label">Fecha</label><label class="form-label" style="color: red">*:</label>
                        <input  type="date" name="fecha_baja_emb" id="fecha_baja_emb"  class="form-control" required>                        
                    </div>

                    <div class="mb-3">
                        <label for="motivo_baja_emb" class="form-label">Motivo</label><label class="form-label" style="color: red">*:</label>
                        <select name="motivo_baja_emb" id="motivo_baja_emb" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($row_listabaja = $listabaja->fetch_assoc()) { ?>
                                <option value="<?php echo $row_listabaja["id_baja_embarazo"]; ?>"><?= $row_listabaja["be_concepto"] ?></option>
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