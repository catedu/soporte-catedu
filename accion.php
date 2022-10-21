<?php
    session_start();

    require_once(__DIR__ . '/../config.php');
    require_once('secret.php');

    $captchaCorrecto = FALSE;

    if(isset($_POST['captcha_challenge']) && $_POST['captcha_challenge'] == $_SESSION['captcha_text']) {
        $captchaCorrecto = TRUE;
    }else{
        $captchaCorrecto = FALSE;
    }
    // Inicializo variables
    $userRedmine = "";
    $passRedmine = "";
    $apiRedmine = "";
    $urlRedmine = "";
    $projectId = "";

    //////////////////////////////
    // Funciones
    //////////////////////////////
    function getIPAddress() {  
        //whether ip is from the share internet  
         if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
                    $ip = $_SERVER['HTTP_CLIENT_IP'];  
            }  
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
         }  
    //whether ip is from the remote address  
        else{  
                 $ip = $_SERVER['REMOTE_ADDR'];  
         }  
         return $ip;  
    }  
    /**
     * Asigna la incidencia a quién corresponda en función del ámbito
     * PERO también asigna
     * - usuario de redmine
     * - pass de redmine
     * - API de redmine
     * - url de redmine
     * - projectid
     * En función del ámbito
     */
    function asignarIncidenciaA($ambito){
        // Le indico a la función que estas variables son las de fuera
        global $userRedmine, $passRedmine, $apiRedmine, $urlRedmine, $projectId;
        // Asigno lo común
        $userRedmine = $GLOBALS["userRedmineComun"];
        $passRedmine = $GLOBALS["passRedmineComun"];
        $apiRedmine = $GLOBALS["apiRedmineComun"];
        $urlRedmine = $GLOBALS["urlRedmineComun"];
        $projectId = "9"; //CATEDU
        // Personalizo en función de cada caso
        switch ($ambito) {
            case "Aeducar":
                $projectId = "10";
                return $GLOBALS["idUserAeducar"];
                break;
            case "Aramoodle":
                return $GLOBALS["idUserAramoodle"];
                break;
            case "Aularagón":
                return $GLOBALS["idUserAularagon"];
                break;
            case "Competencias digitales":
                $projectId = "13";
                return $GLOBALS["idUserCDD"];
                break;
            case "Doceo":
                return $GLOBALS["idUserDoceo"];
                break;
            case "FP Distancia":
                $projectId = "12";
                return $GLOBALS["idUserFP"];
                break;
            case "STEAM":
                return $GLOBALS["idUserSTEAM"];
                break;
            case "Vitalinux":
                $userRedmine = $GLOBALS["userRedmineVx"];
                $passRedmine = $GLOBALS["passRedmineVx"];
                $apiRedmine = $GLOBALS["apiRedmineVx"];
                $urlRedmine = $GLOBALS["urlRedmineVx"];
                $projectId = "2";
                return $GLOBALS["idUserVitalinux"];
                break;
            case "WordPress":
                return $GLOBALS["idUserWordPress"];
                break;
        }
        return ["idUserOtros"];
    }
    //////////////////////////////
    // Recojo parámetros del form
    //////////////////////////////
    $ambito = htmlspecialchars($_POST["ambito"]);
    $nombre_solicitante = htmlspecialchars($_POST["nombre_solicitante"]);
    $pape_solicitante = htmlspecialchars($_POST["pape_solicitante"]);
    $sape_solicitante = htmlspecialchars($_POST["sape_solicitante"]);
    $email_solicitante = htmlspecialchars($_POST["email_solicitante"]);
    $otros = htmlspecialchars($_POST["otros"]);
    //
    $captcha = htmlspecialchars($_POST["captcha"]);
    $token = htmlspecialchars($_POST["token"]);
    $adjunto = htmlspecialchars($_POST["adjunto"]);

    //////////////////////////////
    // Antes de procesar miro si campos obligatorios están rellenos para evitar envío masivo de navegadores que se saltan required
    //////////////////////////////

    $camposObligatoriosRellenos = true;
    if($nombre_solicitante == "" || $pape_solicitante == "" || $email_solicitante == "" ){
        $camposObligatoriosRellenos = false;
    }



    if( $camposObligatoriosRellenos && $captchaCorrecto ){
        //////////////////////////////
        // Creo variables iniciales
        //////////////////////////////
        $date = date('d-m-Y H:i:s');
        $ip = getIPAddress();  

        $descriptionRedmine = '*' . $nombre_solicitante . ' ' . $pape_solicitante . '* ha enviado el ' . $date . ' desde la IP ' . $ip . ' una incidencia con la siguiente información:\n';
        $descriptionRedmine .= '\n';
        $descriptionRedmine .= '- *Ámbito* : ' .$ambito . '\n';
        $descriptionRedmine .= '- *Nombre solicitante* : ' . $nombre_solicitante . '\n';
        $descriptionRedmine .= '- *1er apellido solicitante* : ' . $pape_solicitante . '\n';
        $descriptionRedmine .= '- *2º apellido solicitante* : ' . $sape_solicitante . '\n';
        $descriptionRedmine .= '- *E-mail solicitante* : ' . $email_solicitante . '\n';
        $descriptionRedmine .= '- *Explicación de la situación* : ' . $otros . '\n';
        //$descriptionRedmine .= '- *captcha en form* : ' . $captcha . '\n';
        //$descriptionRedmine .= '- *captcha en sesion* : ' . $_SESSION["captcha"] . '\n';

        //////////////////////////////
        // Contacto con RedMine para crear la incidencia
        //////////////////////////////
        $url = $GLOBALS["urlRedmineComun"];
        
        $asignarA = asignarIncidenciaA($ambito);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($curl, CURLOPT_POST, 1);
        $issue =  '
        <?xml version="1.0"?>
        <issue>
        <project_id>'.$projectId.'</project_id>
        <subject>'.$ambito.'</subject>';

        if($token != ""){
            $issue .= '
            <uploads type="array">
              <upload>
                <token>' . $token . '</token>
                <filename>' . $adjunto . '</filename>
                <description>Fichero adjunto</description>
                <content_type>image/png</content_type>
              </upload>
            </uploads>';
        }

        $issue .= '<description><![CDATA['.$descriptionRedmine.']]></description>
        <priority_id>2</priority_id>
        <custom_fields type="array">
            <custom_field id="1" name="owner-email">
                <value>'.$email_solicitante.'</value>
            </custom_field>
        </custom_fields>
        <assigned_to_id>'. $asignarA .'</assigned_to_id>
        </issue>';
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $issue );
        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        
        curl_setopt($curl, CURLOPT_USERPWD, $userRedmine.":".$passRedmine);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        echo 'resultado: ' . $result . '<br/><br/>';
        $respuesta = json_decode($result, true);
        $incidenciaCreada = $respuesta["issue"];
        echo '$incidenciaCreada: '. $incidenciaCreada;
        $incidenciaCreadaId = $incidenciaCreada["id"];
        echo '$incidenciaCreadaId: '. $incidenciaCreadaId;

        $exitoCreandoIncidencia = false;
        if (isset($incidenciaCreadaId) && $incidenciaCreadaId !== '') {
            $exitoCreandoIncidencia = true;
            echo '$exitoCreandoIncidencia cambiado a true';
        }

        //////////////////////////////
        // Envío email al usuario con copia de su solicitud original
        //////////////////////////////
        if( $exitoCreandoIncidencia ){
            
            $toUser = new stdClass();
            $toUser->email = $email_solicitante;
            $toUser->firstname = $nombre_solicitante;
            $toUser->lastname = $pape_solicitante;
            $toUser->maildisplay = true;
            $toUser->id = -99; 
            
            $subject = 'Nueva incidencia - FP a distancia Aragón';
            
            $cuerpo = 'Hola ' . $nombre_solicitante . ',<br/>';
            $cuerpo .= 'su incidencia realizada el ' . $date . ' ha sido recogida en nuestro sistema con el id <strong>'. $incidenciaCreadaId .'</strong>. La misma contiene la siguiente información:<br/>';
            $cuerpo .= '<ul>';
            $cuerpo .= '<li><b>Ámbito</b>: ' . $ambito . '</li>';
            $cuerpo .= '<li><b>Nombre solicitante</b>: ' . $nombre_solicitante . '</li>';
            $cuerpo .= '<li><b>1er apellido solicitante</b>: ' . $pape_solicitante . '</li>';
            $cuerpo .= '<li><b>2º apellido solicitante</b>: ' . $sape_solicitante . '</li>';
            $cuerpo .= '<li><b>E-mail solicitante</b>: ' . $email_solicitante . '</li>';
            $cuerpo .= '<li><b>Explicación de la situación</b>: ' . $otros . '</li>';
            $cuerpo .= '</ul>';
            $cuerpo .= 'No conteste a este correo electrónico puesto que se trata de una cuenta desatendida y automatizada<br/>';
            $cuerpo .= 'Saludos<br/><br/>';
            $cuerpo .= 'FP distancia Aragón';

            $fromUser = new stdClass();
            $fromUser->firstname = null;
            $fromUser->lastname = null;
            $fromUser->email = '<>';
            $fromUser->maildisplay = true;
            $fromUser->id = -99;

            $exitoEnviandoEmail = email_to_user($toUser, $fromUser, $subject, $cuerpo);
        }
    }
?>

<!DOCTYPE html>
    <html  dir="ltr" lang="es" xml:lang="es">
    <head>
        <title>FP a distancia - Aragón</title>
        <link rel="shortcut icon" href="https://<?php echo $_SERVER['HTTP_HOST'] ?>/pluginfile.php/1/theme_moove/favicon/1615997395/FAVICON11.ico" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="keywords" content="moodle, FP a distancia - Aragón" />
        <link rel="stylesheet" type="text/css" href="https://<?php echo $_SERVER['HTTP_HOST'] ?>/theme/yui_combo.php?rollup/3.17.2/yui-moodlesimple-min.css" /><script id="firstthemesheet" type="text/css">/** Required in order to fix style inclusion problems in IE with YUI **/</script><link rel="stylesheet" type="text/css" href="https://<?php echo $_SERVER['HTTP_HOST'] ?>/theme/styles.php/moove/1615997395_1/all" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
        <link rel="stylesheet" href="https://<?php echo $_SERVER['HTTP_HOST'] ?>/theme/moove/style/aragon/aragon-wrapper.css" type="text/css">
        
    </head>
    <body  id="page-site-index" class="format-site course path-site chrome dir-ltr lang-es yui-skin-sam yui3-skin-sam test-adistanciafparagon-es pagelayout-frontpage course-1 context-2 notloggedin ">
        <section  class="ita-sectionguia aragob_header_wrapper">
            <!-- aragob bar  -->
            <div class="aragob_header"></div>
            <!-- fin aragon bar -->
        </section>
        <nav class="navbar nav-inicio">
        </nav>
        <div id="page" class="container-fluid">
            <div id="page-header" class="frontpage-guest-header">
                <div class="d-flex flex-wrap">
                    <div id="page-navbar">
                        <nav>
                            <ol class="breadcrumb"><li class="breadcrumb-item"><a href="https://<?php echo $_SERVER['HTTP_HOST'] ?>/" >Página Principal</a></li>
                                <li class="breadcrumb-item"><a href="https://<?php echo $_SERVER['HTTP_HOST'] ?>/soporte-catedu/" >Soporte</a></li>
                            </ol>    
                        </nav>
                    </div>
                </div>
                <div id="page-content" class="row pb-3 d-print-block">
                    <div id="region-main-box" class="col-12">
                        <section id="region-main">
                            <div class="card">
                                <div class="card-body">
                                    <!--span class="notifications" id="user-notifications"></span-->
                                    <div role="main">
                                        <span id="maincontent"></span>
                                        <div class="settingsform">
<?php
    //////////////////////////////
    // comprobaciones para informar a los usuarios del éxito/fallo de su comunicación
    //////////////////////////////
    $h3 = '';
    if(!$camposObligatoriosRellenos){
        $h3 =  'Debe rellenar todos los campos obligatorios. Incidencia NO procesada.';
    }elseif(!$captchaCorrecto){
        $h3 =  'El código de captcha no es correcto. Incidencia NO procesada.';
    }elseif($exitoCreandoIncidencia && $exitoEnviandoEmail){
        $h3 =  'Incidencia ' . $incidenciaCreadaId . ' creada. Se le ha enviado un email con copia de la misma.';
    }elseif ($exitoCreandoIncidencia && !$exitoEnviandoEmail) {
        $h3 =  'Incidencia ' . $incidenciaCreadaId . ' creada pero ha fallado el envío de un email a su cuenta con copia de la misma. NO se le podrá comunicar la resolución de la misma o realizar consultas adicionales.';
    }else{
        $h3 =  'Ha fallado la creación de la incidencia. Vuelva a intentarlo.';
    }
?>
                                            <h2>Soporte</h2>
                                            <h3><?php echo $h3 ?></h3>
                                            <div class="settingsform">
<?php
    if( $exitoCreandoIncidencia ){
?>
                                            <p>La información recogida es la siguiente:</p>
                                            <ul>
                                                <li>Ámbito</b>: <?php echo htmlentities($ambito, ENT_QUOTES, "UTF-8"); ?></li>
                                                <li>Nombre solicitante</b>: <?php echo htmlentities($nombre_solicitante, ENT_QUOTES, "UTF-8"); ?></li>
                                                <li>1er apellido solicitante</b>: <?php echo htmlentities($pape_solicitante, ENT_QUOTES, "UTF-8"); ?></li>
                                                <li>2º apellido solicitante</b>: <?php echo htmlentities($sape_solicitante, ENT_QUOTES, "UTF-8"); ?></li>
                                                <li>E-mail solicitante</b>: <?php echo htmlentities($email_solicitante, ENT_QUOTES, "UTF-8"); ?></li>
                                                <li>Explicación de la situación</b>: <?php echo htmlentities($otros, ENT_QUOTES, "UTF-8"); ?></li>
                                            </ul>
<?php
    }else{
?>
                                            <p class="box py-3 generalbox alert alert-error alert alert-danger">
                                                Ha fallado la creación de la incidencia
                                            </p>
<?php
    }
?>

                                        </div>
                                    </div>
                                </div><!-- end of .card-body -->
                            </div> <!-- card -->
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <script>
            
        </script>
    </body>
</html>
