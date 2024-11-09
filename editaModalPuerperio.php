<!-- Modal edita registro -->
<div class="modal fade" id="editaModalPuerperio" tabindex="-1" aria-labelledby="editaModalPuerperioLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editaModalPuerperioLabel">Editar recien nacido</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>        
                <div class="modal-body">
                <form action="actualizaPuerperio.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="id_puerperio" name="id_puerperio">
                <input type="hidden" id="fk_id_tarjeta" name="fk_id_tarjeta">
                
                <div class="row"> 
                        <div class="col">
                            <div class="mb-3">
                                <label for="p_fecha" class="form-label">Fecha</label><label class="form-label" style="color: red">*:</label>
                                <input type="date" name="p_fecha" id="p_fecha"  class="form-control" required>                        
                            </div>    
                        </div>   
                        <div class="col">
                            <div class="mb-3">
                                <label for="p_peso" class="form-label">Peso gr</label><label class="form-label" style="color: red">*:</label>
                                <input type="text" name="p_peso" id="p_peso"  class="form-control" required>                        
                            </div> 
                        </div> 
                </div>   
                        <div class="mb-3">
                            <label for="p_sig_sin" class="form-label">Signos y Síntomas de Alarma</label><label class="form-label" style="color: red">*:</label>
                            <select name="p_sig_sin" id="p_sig_sin" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php while ($row_listassapl = $listaSSAPL->fetch_assoc()) { ?>
                                <option value="<?php echo $row_listassapl["id_sig_sin_alarma_puer_lact"]; ?>"><?= $row_listassapl["ssapl_concepto"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                                                
                        <div class="mb-3">
                            <label for="p_medicamentos" class="form-label">Medicamentos</label><label class="form-label" style="color: red">*:</label>
                            <select name="p_medicamentos" id="p_medicamentos" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php while ($row_listamedpl = $listaMEDPL->fetch_assoc()) { ?>
                                <option value="<?php echo $row_listamedpl["id_medicamentos_puer_lact"]; ?>"><?= $row_listamedpl["medpl_concepto"] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="p_enfermedades_pre" class="form-label">Enfermedades Presentes</label><label class="form-label" style="color: red">*:</label>
                            <input type="text" name="p_enfermedades_pre" id="p_enfermedades_pre"  class="form-control" required>                        
                        </div> 
        
                        <div class="mb-3">
                            <label for="p_plan_seguridad" class="form-label">Plan de Seguridad</label><label class="form-label" style="color: red">*:</label>
                            <select name="p_plan_seguridad" id="p_plan_seguridad" class="form-select"  required>
                                <option value="">Seleccionar...</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                        </div>
                    
                    <div class="d-flex justify-content-end pt2">
                        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary ms-1"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                    </div>
                 
                </div>
                </form>

            </div>

        </div>
    </div>
</div>
