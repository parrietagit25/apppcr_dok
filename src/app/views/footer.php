    <!--<footer class="bg-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <p>&copy; 2024 GrupoPCR. Todos los derechos reservados.</p>
        </div>
    </footer>-->
    <!-- Bootstrap JS desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script>
        let timeout = null;
        window.regColRegistroPermitido = false;

        function actualizarEstadoRegistroCol(permitido) {
            window.regColRegistroPermitido = permitido;
            const btn = document.getElementById('reg_col');
            if (!btn) {
                return;
            }
            btn.disabled = !permitido;
            btn.style.display = permitido ? 'block' : 'none';
        }

        function buscar_code_col() {

            document.getElementById("buscar_user").innerHTML = '<div class="loader-container"><div class="loader"></div></div>';
            actualizarEstadoRegistroCol(false);

            clearTimeout(timeout); 
            timeout = setTimeout(() => {
                let codigo = document.getElementById("reg_col_code").value;

                if (codigo.trim() === "") {
                    document.getElementById("buscar_user").innerHTML = "";
                    actualizarEstadoRegistroCol(false);
                    return;
                }

                fetch("<?php echo JS_FETCH; ?>?codigo=" + encodeURIComponent(codigo))
                    .then(response => response.json())
                    .then(data => {
                        let mensaje = "";
                        
                        if (data.existe) {
                            mensaje = '<div class="alert alert-danger mt-2">El código ya está registrado.</div>';
                            actualizarEstadoRegistroCol(false);
                        } else if (data.no_existe) {
                            mensaje = '<div class="alert alert-danger mt-2">El código no existe en la base de datos de RRHH.</div>';
                            actualizarEstadoRegistroCol(false);
                        } else {
                            mensaje = '<div class="alert alert-success mt-2">El código está disponible.</div>';
                            actualizarEstadoRegistroCol(true);
                        }
                        document.getElementById("buscar_user").innerHTML = mensaje;
                    })
                    .catch(error => {
                        console.error("Error en la consulta:", error);
                        document.getElementById("buscar_user").innerHTML = '<div class="alert alert-warning mt-2">Error en la verificación.</div>';
                        actualizarEstadoRegistroCol(false);
                    });
            }, 3000); 
        }

        function buscar_code_rest_pass() {
            document.getElementById("buscar_user").innerHTML = '<div class="loader-container"><div class="loader"></div></div>';
            document.getElementById('restore_col').style.display = 'none'; 

            clearTimeout(timeout);
            timeout = setTimeout(() => {
                let codigo = document.getElementById("reg_col_code").value.trim();

                if (codigo === "") {
                    document.getElementById("buscar_user").innerHTML = "";
                    return;
                }

                fetch("<?php echo JS_FETCH; ?>?codigo_restores_pass=" + encodeURIComponent(codigo))
                    .then(response => response.text()) // 💡 Primero obtenemos el texto para revisar errores
                    .then(text => {
                        console.log("Respuesta RAW del servidor:", text); // 🚀 Depuración: Verifica la respuesta completa
                        
                        try {
                            let data = JSON.parse(text); // 💡 Convierte el texto en JSON
                            let mensaje = "";

                            if (data.error) {
                                mensaje = '<div class="alert alert-danger mt-2">' + data.error + '</div>';
                                document.getElementById('restore_col').style.display = 'none';
                            } else if (data.email) {
                                mensaje = '<div class="alert alert-success mt-2">Se enviará un email al correo ' + data.email + '. para restaurar la su password</div>';
                                document.getElementById('restore_col').style.display = 'block';
                                document.getElementById('email').value = data.email;
                            }

                            document.getElementById("buscar_user").innerHTML = mensaje;
                        } catch (error) {
                            console.error("Error al parsear JSON:", error, "Texto recibido:", text);
                            document.getElementById("buscar_user").innerHTML = '<div class="alert alert-warning mt-2">Error en la verificación.</div>';
                        }
                    })
                    .catch(error => {
                        console.error("Error en la consulta:", error);
                        document.getElementById("buscar_user").innerHTML = '<div class="alert alert-warning mt-2">Error en la verificación.</div>';
                    });
            }, 3000);
        }

        function comprar_pass(){

            let pass1 = document.getElementById('new_pass').value;
            let pass2 = document.getElementById('new_pass2').value;

            if (pass1 == pass2) {
                document.getElementById('new_pass').style.border = 'green 2px solid';
                document.getElementById('new_pass2').style.border = 'green 2px solid';
                document.getElementById('restablecer_pass').style.display = 'block';
            }else{
                document.getElementById('new_pass').style.border = 'red 2px solid';
                document.getElementById('new_pass2').style.border = 'red 2px solid';
                document.getElementById('restablecer_pass').style.display = 'none';
            }

        } 

    </script>
    <script>
        const input = document.querySelector(".prefijo");

        // Siempre inicia con 00
        input.value = "00";

        // Coloca el cursor después del 00 al hacer clic
        input.addEventListener("focus", function () {
            if (input.value.length <= 2) {
            input.setSelectionRange(2, 2);
            }
        });

        // Evita que se borre el prefijo
        input.addEventListener("keydown", function (e) {
            // Previene el borrado del 00
            if ((input.selectionStart <= 2) &&
                (e.key === "Backspace" || e.key === "Delete")) {
            e.preventDefault();
            }
        });

        // Asegura que el valor siempre comience con 00
        input.addEventListener("input", function () {
            if (!input.value.startsWith("00")) {
            input.value = "00" + input.value.replace(/^0+/, '');
            }
        });
    </script>
    <script>
        function bloquearFechasPasadas(selector) {
            const hoy = new Date().toISOString().split('T')[0];
            document.querySelectorAll(selector).forEach(campo => {
            campo.setAttribute("min", hoy);
            });
        }

        // Invocación automática al cargar la página
        document.addEventListener("DOMContentLoaded", function () {
            bloquearFechasPasadas(".bloquear-pasado");

            const formRegCol = document.getElementById('form_reg_col');
            if (formRegCol) {
                formRegCol.addEventListener('submit', function (e) {
                    if (!window.regColRegistroPermitido) {
                        e.preventDefault();
                    }
                });
                formRegCol.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' && !window.regColRegistroPermitido) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>
