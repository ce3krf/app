// Clave secreta compartida con PHP
const SECRET_KEY = "PATATA";


// Función para encriptar usando XOR y base64 con al menos 16 caracteres de salida
function encrypt(id) {
    var key = SECRET_KEY;
    id = id.toString();
    
    // Si la clave es más corta que el ID, repetirla
    while (key.length < id.length) {
        key += SECRET_KEY;
    }
    
    var encrypted = '';
    
    // Aplicar XOR entre la ID y la clave
    for (var i = 0; i < id.length; i++) {
        encrypted += String.fromCharCode(id.charCodeAt(i) ^ key.charCodeAt(i));
    }
    
    // Codificar en base64
    var encoded = btoa(encrypted);
    
    // Asegurarse de que tenga al menos 16 caracteres, agregando relleno si es necesario
    while (encoded.length < 16) {
        encoded += "=";  // Usamos `=` como relleno, común en base64
    }

    return encoded.substring(0, 16);  // Devolver los primeros 16 caracteres
}

// Función para desencriptar
function decrypt(encrypted) {
    var key = SECRET_KEY;
    
    // Eliminar cualquier `=` añadido durante el encriptado
    encrypted = encrypted.replace(/=/g, "");
    
    // Decodificar desde base64
    var encryptedDecoded = atob(encrypted);
    
    // Si la clave es más corta que el texto cifrado, repetirla
    while (key.length < encryptedDecoded.length) {
        key += SECRET_KEY;
    }
    
    var decrypted = '';
    
    // Aplicar XOR nuevamente para recuperar la ID original
    for (var i = 0; i < encryptedDecoded.length; i++) {
        decrypted += String.fromCharCode(encryptedDecoded.charCodeAt(i) ^ key.charCodeAt(i));
    }
    
    return decrypted;
}