// Funciones

//eventos            


//Refresh Captcha
function refreshCaptcha(){
    document.querySelector(".captcha-image").src = 'captcha.php?' + Date.now();
}
// Fichero adjunto
document.getElementById("adjunto").onchange = function(){

    var myFile = document.getElementById("adjunto");
    var files = myFile.files;
    var formData = new FormData();
    var file = files[0]; 
    // Check the file type
    if (!file.type.match('image.*')) {
        alert('TEl archivo seleccionado no es una imagen.');
        return;
    }
    //
    formData.append('fileAjax', file, file.name);

    // Set up the request
    var xhr = new XMLHttpRequest();

    // Open the connection
    xhr.open('POST', 'https://www.adistanciafparagon.es/soporte/upload.php', true);

    // Set up a handler for when the task for the request is complete
    xhr.onload = function () {
        if (xhr.status == 200) {
            //statusP.innerHTML = 'Upload copmlete!';
            console.log("respuesta: " + xhr.responseText);
            document.getElementById("token").value = xhr.responseText;
        } else {
            //statusP.innerHTML = 'Upload error. Try again.';
            console.log("Error: " + xhr.responseText);
        }
    };

    // Send the data.
    xhr.send(formData);

}