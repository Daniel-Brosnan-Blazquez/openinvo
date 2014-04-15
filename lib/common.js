
function updateClock(){
    var date;
    var h;
    var m;
    var s;
    var mStr;
    var sStr;
    var hStr;

    // Obtain date
    date = new Date();
    h    = date.getHours();
    m  = date.getMinutes();
    s = date.getSeconds();

    // Compound clock components
    if (m < 10)
        mStr = "0" + m;
    else
        mStr = String (m);
    
    if (s < 10)
        sStr = "0" + s;
    else
        sStr = String (s);

    hStr = h + " : " + mStr + " : " + sStr;

    // Update value
    document.getElementById("clock").innerHTML = hStr;

    // Change value every second
    setTimeout("updateClock()",1000);
}

var veces = 1;

function validacion() {
  valor1 = document.getElementById("login").value;
  if( valor1 == null || valor1.length == 0 || /^\s+$/.test(valor1) ) {
  alert(' El campo Login es obligatorio');
  return false;
}

  valor2 = document.getElementById("password").value;
  if( valor2 == null || valor2.length == 0 || /^\s+$/.test(valor2) ) {
  alert(' El campo Password es obligatorio');
  return false;
}
  if (valor2.length < 8) {
    veces++;
  }

  valor3 = document.getElementById("entidad").value;
  valor4 = document.getElementById("oficina").value;
  valor5 = document.getElementById("control").value;
  valor6 = document.getElementById("cuenta").value;
  if( valor3 == null || valor3.length == 0 || /^\s+$/.test(valor3)) {
  alert('Debe completar todos los campos de CCC');
  return false;
  }

  else if (isNaN (valor3) || valor3.length != 4) {
  alert('El primer campo ha de constar de 4 dígitos');
  return false;
  }

  if( valor4 == null || valor4.length == 0 || /^\s+$/.test(valor4)  ) {
  alert('Debe completar todos los campos de CCC');
  return false;
  }

  else if (isNaN (valor4) || valor4.length != 4) {
  alert('El segundo campo ha de constar de 4 dígitos');
  return false;
  }

  if( valor5 == null || valor5.length == 0 || /^\s+$/.test(valor5) ) {
  alert('Debe completar todos los campos de CCC');
  return false;
  }

  else if (isNaN(valor5) || valor5.length != 2) {
  alert('El tercer campo ha de constar de 2 dígitos');
  return false;
  }

  if( valor6 == null || valor6.length == 0 || /^\s+$/.test(valor6) ) {
  alert('Debe completar todos los campos de CCC');
  return false;
  }

  else if (isNaN (valor6) || valor6.length != 10) {
  alert('El cuarto campo ha de constar de 10 dígitos');
  return false;
  }
  alert (veces);
  if (valor2.length < 8 && veces == 2) {

	alert ('Se recomienda que Password tenga al menos 8 caracteres');
	return false;
    }
  // Si el script ha llegado a este punto, todas las condiciones
  // se han cumplido, por lo que se devuelve el valor true
  return true;
}
