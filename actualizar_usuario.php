import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { Router } from '@angular/router';

@Component({
  selector: 'app-compra',
  templateUrl: './compra.component.html',
  styleUrls: ['./compra.component.css']
})
export class CompraComponent {

  codigoVerificacion = '';
codigoEnviado = '';
mostrarInputCodigo = false;
verificado = false;
reenvioHabilitado = false;




  mostrarFormulario = false;
  correoInput = '';
  datosUsuario: any = {
    id: null,
    nombres: '',
    apellido_paterno: '',
    apellido_materno: '',
    correo: '',
    telefono: '',
    fecha_registro: '',
    direccion_envio: '',
    numero_pedidos: 0
  };
  originalData: any = {};
  mensaje = '';
  usuarioExiste = false;

  constructor(private http: HttpClient, private router: Router) {}

  reenviarCodigo() {
  this.enviarCodigoVerificacion();
}

buscarUsuario() {
  console.log('Validando entrada para:', this.correoInput);

  if (!this.correoInput) return;

  const esCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.correoInput);
  const esTelefono = /^\d{10}$/.test(this.correoInput);

  if (!esCorreo && !esTelefono) {
    this.mensajeModal = 'Por favor, ingresa un correo válido.';
    this.mostrarModal = true;
    return;
  }

  // Generar y enviar código de verificación PRIMERO
  this.codigoEnviado = Math.floor(10000 + Math.random() * 90000).toString(); // 5 dígitos
  this.mostrarInputCodigo = true;
  this.verificado = false;

  const backendUrl = environment.backendUrl;

  this.http.post<any>(`${backendUrl}/enviar_codigo.php`, {
    destino: this.correoInput,
    codigo: this.codigoEnviado
  }).subscribe(sendResponse => {
    if (sendResponse.success) {
      this.mensajeModal = 'Te enviamos un código de verificación a tu correo o teléfono.';
    } else {
      this.mensajeModal = 'Error al enviar el código de verificación.';
    }
    this.mostrarModal = true;
  });
  this.reenvioHabilitado = false;
setTimeout(() => {
  this.reenvioHabilitado = true;
}, 10000);

}

  
enviarCodigoVerificacion() {
  this.codigoEnviado = Math.floor(10000 + Math.random() * 90000).toString(); // 5 dígitos
  this.mostrarInputCodigo = true;

  const backendUrl = environment.backendUrl;

  this.http.post<any>(`${backendUrl}/enviar_codigo.php`, {
    destino: this.correoInput,
    codigo: this.codigoEnviado
  }).subscribe(response => {
    if (response.success) {
      this.mensajeModal = 'Se envió un código de verificación.';
      this.mostrarModal = true;
    } else {
      this.mensajeModal = 'Error al enviar el código.';
      this.mostrarModal = true;
    }
  });
}

codigoIncorrecto = false;


verificarCodigo() {
  if (this.codigoVerificacion === this.codigoEnviado) {
    this.verificado = true;
    this.codigoIncorrecto = false;
    this.mostrarFormulario = true;
    this.mostrarInputCodigo = false;

    const backendUrl = environment.backendUrl;

    this.http.post<any>(`${backendUrl}/buscar_usuario.php`, {
      correo: this.correoInput
    }).subscribe(response => {
      console.log('Respuesta del servidor:', response);

      if (response.success && response.usuario) {
        this.usuarioExiste = true;
        this.datosUsuario = { ...response.usuario };
        this.originalData = { ...response.usuario };
        this.mensaje = `¡Gracias por tu confianza! ✨\n  \n🚀 ¡${this.datosUsuario.numero_pedidos} pedidos hasta ahora! 🚀\n\n🌟 Sigue marcando el camino del mundo. 🌟`;
      } else {
        this.usuarioExiste = false;
        this.datosUsuario = {
          id: null,
          nombres: '',
          apellido_paterno: '',
          apellido_materno: '',
          correo: this.correoInput,
          telefono: '',
          fecha_registro: this.getFechaActual(),
          direccion_envio: '',
          numero_pedidos: 0
        };
        this.originalData = {};
        this.mensaje = 'Regístrate y continúa tu camino.';
      }
    });

  } else {
    this.codigoIncorrecto = true;
    this.mensajeModal = 'El código ingresado no es correcto.';
    this.mostrarModal = true;
  }
  this.reenvioHabilitado = false;
setTimeout(() => {
  this.reenvioHabilitado = true;
}, 10000);

}



  // Método para obtener la fecha actual en formato YYYY-MM-DD
  getFechaActual(): string {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const mes = (fecha.getMonth() + 1).toString().padStart(2, '0'); // Asegura que el mes tenga 2 dígitos
    const dia = fecha.getDate().toString().padStart(2, '0'); // Asegura que el día tenga 2 dígitos
    return `${anio}-${mes}-${dia}`;
  }
  
  mostrarModal = false;
mensajeModal = '';
camposActualizados: string[] = [];

cerrarModal() {
  this.mostrarModal = false;
  this.mensajeModal = '';
  this.camposActualizados = [];
}


  hayCambios(): boolean {
    return Object.keys(this.datosUsuario).some(key => {
      return this.datosUsuario[key] !== this.originalData[key];
    });
  }

  get nombreCompleto(): string {
    const { apellido_paterno, apellido_materno, nombres } = this.datosUsuario;
    return `${apellido_paterno} ${apellido_materno} ${nombres}`.trim();
  }
  
  autoAjustarAltura(textarea: HTMLTextAreaElement): void {
    textarea.style.height = 'auto'; // Reinicia altura para medir bien
    const maxAltura = 200;
    textarea.style.height = Math.min(textarea.scrollHeight, maxAltura) + 'px';
  }
  
  // Método para detectar campos cambiados permitidos (telefono y direccion_envio)
obtenerCamposCambiados(): string[] {
  const campos = [];
  if (this.datosUsuario.telefono !== this.originalData.telefono) {
    campos.push('Teléfono');
  }
  if (this.datosUsuario.direccion_envio !== this.originalData.direccion_envio) {
    campos.push('Dirección de envío');
  }
  return campos;
}

onSubmit() {
  let endpoint = 'registrar_usuario.php';
  const payload = { ...this.datosUsuario };

  if (!/^\d{10}$/.test(this.datosUsuario.telefono)) {
    this.mensajeModal = 'El teléfono debe tener exactamente 10 dígitos.';
    this.mostrarModal = true;
    return;
  }

  if (this.usuarioExiste) {
    if (this.hayCambios()) {
      endpoint = 'actualizar_usuario.php';
      payload.numero_pedidos += 1;
    } else {
      this.router.navigate(['/Finaliza-la-Compra']);
      return;
    }
  } else {
    payload.numero_pedidos = 0;
  }

  const backendUrl = environment.backendUrl;

  this.http.post<any>(`${backendUrl}/${endpoint}`, payload).subscribe(response => {
    if (response.success) {
      localStorage.setItem('datosUsuario', JSON.stringify(this.datosUsuario));
      this.mostrarFormulario = false;

      if (!this.usuarioExiste) {
        this.mensajeModal = '🎉 ¡Felicidades por iniciar tu camino! 🎉\nQuédate con nosotros y recibirás premios en el futuro.';
        this.mostrarModal = true;
        setTimeout(() => {
          this.mostrarModal = false;
          this.router.navigate(['/Finaliza-la-Compra']);
        }, 5000);
      } else {
        this.camposActualizados = this.obtenerCamposCambiados();
        this.mensajeModal = 'Datos actualizados correctamente:';
        this.mostrarModal = true;
        setTimeout(() => {
          this.mostrarModal = false;
          this.router.navigate(['/Finaliza-la-Compra']);
        }, 5000);
      }
    } else {
      this.mensajeModal = 'Error al actualizar o registrar usuario.';
      this.mostrarModal = true;
    }
  });
}



}
