<?php


session_start();

$sessionid = session_id();
?>
<!DOCTYPE html>
    <html  dir="ltr" lang="es" xml:lang="es">
    <head>
        <title>Soporte - CATEDU</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="keywords" content="soporte, tickets, CATEDU" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    </head>
    <body >
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <h2>Soporte</h2>

                    <form action="accion.php" method="post" id="form_soporte" name="form_soporte" >
                        
                            
                            <div class="alert alert-warning" role="alert">
                                Asegúrese de introducir su correo electrónico correctamente
                            </div>
                            
                            <div class="mb-3">
                            <label for="ambito" class="form-label">(*) Ámbito</label>
                                <select id="ambito" name="ambito" class="form-select" required >
                                    <option value="">Elija una opción</option>
                                    <option value="Aeducar">Aeducar</option>
                                    <option value="Aramoodle">Aramoodle</option>
                                    <option value="Aularagón">Aularagón</option>
                                    <option value="Competencias digitales">Competencias digitales</option>
                                    <option value="Doceo">Doceo</option>
                                    <option value="FP Distancia">FP Distancia</option>
                                    <option value="STEAM">STEAM</option>
                                    <option value="Vitalinux">Vitalinux</option>
                                    <option value="WordPress">WordPress</option>
                                    <option value="otro">Otro ámbito o Desconozco el ámbito</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="asunto" class="form-label">(*) Asunto</label>
                                <input type="text" class="form-control" id="asunto" name="asunto" required >
                            </div>

                           <div class="mb-3">
                                <label for="nombre_solicitante" class="form-label">(*) Su nombre</label>
                                <input type="text" class="form-control" id="nombre_solicitante" name="nombre_solicitante" required >
                            </div>

                            <div class="mb-3">
                                <label for="pape_solicitante" class="form-label">(*) Su 1er apellido</label>
                                <input type="text" class="form-control" id="pape_solicitante" name="pape_solicitante" required >
                            </div>

                            <div class="mb-3">
                                <label for="sape_solicitante" class="form-label">Su 2º apellido</label>
                                <input type="text" class="form-control" id="sape_solicitante" name="sape_solicitante" >
                            </div>

                            <div class="mb-3">
                                <label for="email_solicitante" class="form-label">(*) Su email</label>
                                <input type="email" class="form-control" id="email_solicitante" name="email_solicitante" required >
                            </div>

                            <div class="mb-3">
                                <label for="adjunto" class="form-label">Adjunte una imagen si lo desea</label>
                                <input type="file" class="form-control" id="adjunto" >
                                <input type="hidden" id="token" name="token" value="" />
                            </div>

                            <div class="mb-3">
                                <label for="otros" class="form-label">Explique su incidencia</label>
                                <textarea required rows="8" cols="60" id="otros" name="otros" spellcheck="true" class="form-control text-ltr" required ></textarea>
                            </div>

                            <div class="mb-3">
                                <img src="captcha.php" alt="CAPTCHA" class="captcha-image">
                                <p>¿No puedes leer la imagen? <a href='javascript: refreshCaptcha();'>click aquí</a> para refrescar</p>
                            </div>

                            <div class="mb-3">
                                <label for="captcha_challenge" class="form-label">(*) Captcha</label>
                                <input type="text" class="form-control" id="captcha_challenge" name="captcha_challenge" pattern="[A-Z]{6}" required >
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <script src="./js/functions.js" ></script>

    </body>
</html>
