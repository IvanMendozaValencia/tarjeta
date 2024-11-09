<!-- Modal edita registro -->
<div class="modal fade" id="editaModalRecienN" tabindex="-1" aria-labelledby="editaModalRecienNLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editaModalRecienNLabel">Editar recién nacido</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="actualizaRencienN_mod.php" method="post" enctype="multipart/form-data">

                <input type="hidden" id="id_recien_nacida" name="id_recien_nacida">
                <input type="hidden" id="fk_id_tarjeta" name="fk_id_tarjeta">
                 
                <div class="row"> 

                    <div class="col">
                        <div class="mb-3">
                            <label for="rn_vivo1" class="form-label">Vivo</label><label class="form-label" style="color: red">*:</label>
                            <select name="rn_vivo1" id="rn_vivo1" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>                                
                            </select>
                        </div>
                    </div>

                    <div class="col">
                        <div class="mb-3">
                            <label for="rn_muerto1" class="form-label">Muerto</label><label class="form-label" style="color: red">*:</label>
                            <select name="rn_muerto1" id="rn_muerto1" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                        </div>
                    </div> 
                    
                    <div class="col">
                        <div class="mb-1">
                                <label for="rn_sexo" class="form-label">Sexo</label><label class="form-label" style="color: red">*:</label>
                            <select name="rn_sexo" id="rn_sexo" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($row_listasrn = $listarsrn->fetch_assoc()) { ?>
                            <option value="<?php echo $row_listasrn["id_sexo_rn"]; ?>"><?= $row_listasrn["srn_concepto"] ?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="row"> 
                    <div class="col">
                        <div class="mb-3">
                            <label for="rn_peso" class="form-label">Peso gr</label><label class="form-label" style="color: red">*:</label>
                            <input type="text" name="rn_peso" id="rn_peso"  class="form-control" required>                        
                        </div>
                    </div>    
                    <div class="col">    
                        <div class="mb-3">
                            <label for="rn_talla" class="form-label">Talla cm</label><label class="form-label" style="color: red">*:</label>
                            <input type="text" name="rn_talla" id="rn_talla"  class="form-control" required>                        
                        </div>
                    </div>     
                </div>
                <div class="row"> 
                             <center> <legend>A los 5 minutos:</legend></center> 
                        <div class="col">  
                            <div class="mb-3">
                                <label for="rn_apgar" class="form-label">APGAR</label><label class="form-label" style="color: red">*:</label>
                                <input type="number" name="rn_apgar" min="1" max="10" id="rn_apgar"  class="form-control" required>                        
                            </div>
                        </div>    
                        <div class="col">      
                            <div class="mb-3">
                                <label for="rn_silverman" class="form-label">SILVERMAN</label><label class="form-label" style="color: red">*:</label>
                                <input type="number" name="rn_silverman" min="1" max="10" id="rn_silverman"  class="form-control" required>                        
                            </div>
                        </div>    
                </div>  
                <div class="row"> 
                            <center><legend>TAMIZAJE:</legend></center>
                        <div class="col"> 
                            <div class="mb-3">
                            <label for="rn_tamiz_metabolicov" class="form-label">METABÓLICO</label><label class="form-label" style="color: red">*:</label>
                            <select name="rn_tamiz_metabolico" id="rn_tamiz_metabolico" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI">SI</option>
							<option value="NO">NO</option>
                            </select>                  
                            </div>
                        </div> 
                        <div class="col"> 
                            <div class="mb-3">
                            <label for="rn_tamiz_auditivo" class="form-label">AUDITIVO</label><label class="form-label" style="color: red">*:</label>
                            <select name="rn_tamiz_auditivo" id="rn_tamiz_auditivo" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <option value="SI">SI</option>
							<option value="NO">NO</option>
                            </select>                    
                            </div>
                        </div> 
                </div>      
                    
                    <div class="d-flex justify-content-end pt2">
                        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary ms-1"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                    </div>
                    
                </div>
                </form>

                <script>
                $(document).ready(function () {
                    $("#rn_vivo1").change(function() {
                    if($("#rn_vivo1").val() === "SI"){
                        $('#rn_muerto1').val('NO');
                    }
                    else
                    {
                        $('#rn_muerto1').val('SI');
                    }
                    })
                });
                $(document).ready(function () {
                    $("#rn_muerto1").change(function() {
                    if($("#rn_muerto1").val() === "SI"){
                        $('#rn_vivo1').val('NO');
                    }
                    else
                    {
                        $('#rn_vivo1').val('SI');
                    }
                    })
                });
                </script>

            </div>

        </div>
    </div>
</div>