<!-- Modal edita registro -->
<div class="modal fade" id="editaModalusuario" tabindex="-1" aria-labelledby="editaModalusuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editaModalusuarioLabel">Editar registro</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="actualizaUsuarios.php" method="post" enctype="multipart/form-data">

                    <input type="hidden" id="id" name="id">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label><label class="form-label" style="color: red">*:</label>
                        <input type="text" name="nombre" id="nombre"  class="form-control" required>                        
                    </div>
                   
                    <div class="mb-3">
                        <label for="tipo_usuario" class="form-label">Tipo</label><label class="form-label" style="color: red">*:</label>
                        <select name="tipo_usuario" id="tipo_usuario" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($row_usuario = $tipo_usuario->fetch_assoc()) { ?>
                                <option value="<?php echo $row_usuario["id"]; ?>"><?= $row_usuario["tipo"] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="clues_id" class="form-label">CLUES</label><label class="form-label" style="color: red">*:</label>
                        <input type="text" name="clues_id" id="clues_id"  class="form-control" required> 
                        <div id="respuesta"> </div>                       
                    </div>

                      
                    <hr>
                    <legend>Accesos:</legend>
                        <div class="row"> 
                        <div class="col">

                            <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"  name="a_usuarios" id="a_usuarios" >
                            <label class="form-check-label" for="a_usuarios">Usuarios</label>
                            </div>

                            <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"  name="a_configuracion" id="a_configuracion" >
                            <label class="form-check-label" for="a_configuracion">Configuración</label>
                            </div>
 
                        </div>


                        <div class="col">
                        
                            <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"  name="a_tarjeta" id="a_tarjeta" >
                            <label class="form-check-label" for="a_tarjeta">Tarjeta</label>
                            </div>

                        </div>



                        <div class="col">

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"  name="a_consultas" id="a_consultas" >
                            <label class="form-check-label" for="a_consultas">Consultas</label>
                            </div>
                        
                            <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"  name="a_estadisticas" id="a_estadisticas" >
                            <label class="form-check-label" for="a_estadisticas">Estadisticas</label>
                            </div>

                        </div>
                        <br>
                        <hr>
                        <legend>Privilegios:</legend>
                        <div class="row"> 
                        <div class="col">

                            <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"  name="a_agregar" id="a_agregar" >
                            <label class="form-check-label" for="a_agregar">Agregar</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"  name="a_modificar" id="a_modificar" >
                            <label class="form-check-label" for="a_modificar">Modificar</label>
                            </div>
                        </div>

                        <div class="col">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"  name="a_eliminar" id="a_eliminar" >
                            <label class="form-check-label" for="a_eliminar">Eliminar</label>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="d-flex justify-content-end pt2">
                        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" id="enviar"  class="btn btn-primary ms-1" onclick="verificar()"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                        </div>

                </form>



                <script>
                    function verificar() {
                        if(document.getElementById('a_usuarios').checked){
                            $a_usuarios = 1; 
                         }else{
                            $a_usuarios = 0;
                         }

                        if(document.getElementById('a_tarjeta').checked){
                            $a_tarjeta = 1; 
                         }
                         else{
                            $a_tarjeta = 0; 
                         }

                        if(document.getElementById('a_configuracion').checked){
                            $a_configuracion = 1; 
                        }else{
                            $a_configuracion = 0; 
                        }
                        
                        if(document.getElementById('a_consultas').checked){
                            $a_consultas = 1; 
                        }else{
                            $a_consultas = 0; 
                        }

                        if(document.getElementById('a_estadisticas').checked){
                            $a_estadisticas = 1; 
                        }else{
                            $a_estadisticas = 0; 
                        }
                       
                        if(document.getElementById('a_agregar').checked){
                            $a_agregar = 1; 
                        }else{
                            $a_agregar = 0; 
                        }
                        if(document.getElementById('a_modificar').checked){
                            $a_modificar = 1; 
                        }else{
                            $a_modificar = 0; 
                        }
                        if(document.getElementById('a_eliminar').checked){
                            $a_eliminar = 1; 
                        }else{
                            $a_eliminar = 0; 
                        }
                        
                    }
                    </script>

            </div>

        </div>
    </div>
</div>