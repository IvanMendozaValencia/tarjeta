<!-- Modal nuevo registro -->
<div class="modal fade" id="nuevoModalCitaLactancia" tabindex="-1" aria-labelledby="nuevoModalCitaLactanciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="nuevoModalCitaLactanciaLabel">Agregar Cita Lactancia</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="guardaLactanciaCita.php" method="post" enctype="multipart/form-data">
                
                <input type="hidden" id="id_tarjeta" name="id_tarjeta">
                
                <div class="row"> 
                        <div class="col">
                            <div class="mb-3">
                                <label for="pl_fecha" class="form-label">Fecha</label><label class="form-label" style="color: red">*:</label>
                                <input type="date" name="pl_fecha" id="pl_fecha"  class="form-control" required>                        
                            </div>    
                        </div>   
                        <div class="col">
                            <div class="mb-3">
                                <label for="pl_peso" class="form-label">Peso gr</label><label class="form-label" style="color: red">*:</label>
                                <input type="text" name="pl_peso" id="pl_peso"  class="form-control" required>                        
                            </div> 
                        </div> 
                </div>   

                <div class="row"> 
                       <div class="col">
                            <div class="mb-3">
                            <label for="pl_lac_mat_exc" class="form-label">Lactancia Materna Exclusiva</label><label class="form-label" style="color: red">*:</label>
                            <select name="pl_lac_mat_exc" id="pl_lac_mat_exc" class="form-select"  required>
                                <option value="">Seleccionar...</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                            <label for="pl_leche_mat_b24x" class="form-label">Sucedáneos de Leche Mat.</label><label class="form-label" style="color: red">*:</label>
                            <select name="pl_leche_mat_b24x" id="pl_leche_mat_b24x" class="form-select"  required>
                                <option value="">Seleccionar...</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                                <option value="NO">NO APLICA</option>
                            </select>
                            </div>
                        </div>
                </div>        
                        <div class="mb-3">
                            <label for="pl_sig_sin" class="form-label">Signos y Síntomas de Alarma</label><label class="form-label" style="color: red">*:</label>
                            <select name="pl_sig_sin" id="pl_sig_sin" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php while ($row_listassapl = $listaSSAPL->fetch_assoc()) { ?>
                                <option value="<?php echo $row_listassapl["id_sig_sin_alarma_puer_lact"]; ?>"><?= $row_listassapl["ssapl_concepto"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                                                
                        <div class="mb-3">
                            <label for="pl_medicamentos" class="form-label">Medicamentos</label><label class="form-label" style="color: red">*:</label>
                            <select name="pl_medicamentos" id="pl_medicamentos" class="form-select" required >
                                <option value="">Seleccionar...</option>
                                <?php while ($row_listamedpl = $listaMEDPL->fetch_assoc()) { ?>
                                <option value="<?php echo $row_listamedpl["id_medicamentos_puer_lact"]; ?>"><?= $row_listamedpl["medpl_concepto"] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="pl_observaciones" class="form-label">Observaciones:</label>
                            <input type="text" name="pl_observaciones" id="pl_observaciones"  class="form-control" >                        
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
