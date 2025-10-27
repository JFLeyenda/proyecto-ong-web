/**
 * Archivo de validaciones JavaScript
 * Semana 6 - Programación Web 2
 * 
 * Este archivo contiene funciones comunes de validación para todos los formularios
 */

// Función para mostrar mensajes de error
function mostrarError(elementoId, mensaje) {
    const elemento = document.getElementById(elementoId);
    if (elemento) {
        elemento.textContent = mensaje;
        elemento.style.display = 'block';
    }
}

// Función para limpiar mensajes de error
function limpiarError(elementoId) {
    const elemento = document.getElementById(elementoId);
    if (elemento) {
        elemento.textContent = '';
        elemento.style.display = 'none';
    }
}

// Función para limpiar todos los errores de un formulario
function limpiarTodosLosErrores(formularioId) {
    const formulario = document.getElementById(formularioId);
    if (formulario) {
        const errores = formulario.querySelectorAll('.error-mensaje');
        errores.forEach(error => {
            error.textContent = '';
            error.style.display = 'none';
        });
    }
}

// Validación general de campos requeridos
function validarCampoRequerido(input, errorId, mensaje = 'Este campo es obligatorio') {
    const valor = input.value.trim();
    if (valor === '') {
        mostrarError(errorId, mensaje);
        return false;
    } else {
        limpiarError(errorId);
        return true;
    }
}

// Validación de longitud mínima
function validarLongitudMinima(input, minimo, errorId, mensaje) {
    const valor = input.value.trim();
    if (valor.length < minimo) {
        mostrarError(errorId, mensaje || `Debe tener al menos ${minimo} caracteres`);
        return false;
    } else {
        limpiarError(errorId);
        return true;
    }
}

// Validación de longitud máxima
function validarLongitudMaxima(input, maximo, errorId, mensaje) {
    const valor = input.value.trim();
    if (valor.length > maximo) {
        mostrarError(errorId, mensaje || `No debe exceder ${maximo} caracteres`);
        return false;
    } else {
        limpiarError(errorId);
        return true;
    }
}

// Validación de números positivos
function validarNumeroPositivo(input, errorId, mensaje = 'Debe ser un número mayor a cero') {
    const valor = parseFloat(input.value);
    if (isNaN(valor) || valor <= 0) {
        mostrarError(errorId, mensaje);
        return false;
    } else {
        limpiarError(errorId);
        return true;
    }
}

// Validación de email
function validarFormatoEmail(input, errorId, mensaje = 'Ingrese un email válido') {
    const valor = input.value.trim();
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (valor && !regexEmail.test(valor)) {
        mostrarError(errorId, mensaje);
        return false;
    } else {
        limpiarError(errorId);
        return true;
    }
}

// Validación de fechas
function validarFecha(input, errorId, mensaje = 'Ingrese una fecha válida') {
    const valor = input.value;
    if (valor && isNaN(Date.parse(valor))) {
        mostrarError(errorId, mensaje);
        return false;
    } else {
        limpiarError(errorId);
        return true;
    }
}

// Validación de fecha no futura
function validarFechaNoFutura(input, errorId, mensaje = 'La fecha no puede ser futura') {
    const valor = input.value;
    const hoy = new Date();
    const fechaIngresada = new Date(valor);
    
    if (fechaIngresada > hoy) {
        mostrarError(errorId, mensaje);
        return false;
    } else {
        limpiarError(errorId);
        return true;
    }
}

// Función para agregar validaciones en tiempo real a un campo
function agregarValidacionTiempoReal(inputId, validacionFn, ...args) {
    const input = document.getElementById(inputId);
    if (input) {
        input.addEventListener('blur', function() {
            validacionFn(this, ...args);
        });
        
        input.addEventListener('input', function() {
            // Limpiar el error mientras el usuario escribe
            const errorId = 'error-' + inputId.replace('_', '-');
            limpiarError(errorId);
        });
    }
}

// Validación completa de formularios antes del envío
function validarFormulario(formularioId, validaciones) {
    let formularioValido = true;
    
    for (const validacion of validaciones) {
        const { inputId, validacionFn, args } = validacion;
        const input = document.getElementById(inputId);
        
        if (input && !validacionFn(input, ...args)) {
            formularioValido = false;
        }
    }
    
    return formularioValido;
}

// Formatear números con separadores de miles
function formatearNumero(numero) {
    return new Intl.NumberFormat('es-CL').format(numero);
}

// Función para confirmar acciones importantes
function confirmarAccion(mensaje, callback) {
    if (confirm(mensaje)) {
        callback();
    }
}

// Función para mostrar mensajes de éxito temporales
function mostrarMensajeExito(mensaje, duracion = 3000) {
    const div = document.createElement('div');
    div.className = 'mensaje-temporal exito';
    div.textContent = mensaje;
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        z-index: 1000;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    `;
    
    document.body.appendChild(div);
    
    setTimeout(() => {
        div.remove();
    }, duracion);
}

// Función para mostrar mensajes de error temporales
function mostrarMensajeError(mensaje, duracion = 5000) {
    const div = document.createElement('div');
    div.className = 'mensaje-temporal error';
    div.textContent = mensaje;
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #f8d7da;
        color: #721c24;
        padding: 15px 20px;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        z-index: 1000;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    `;
    
    document.body.appendChild(div);
    
    setTimeout(() => {
        div.remove();
    }, duracion);
}

// Inicializar validaciones cuando se carga el documento
document.addEventListener('DOMContentLoaded', function() {
    // Agregar validaciones comunes a todos los formularios
    const formularios = document.querySelectorAll('form');
    
    formularios.forEach(form => {
        // Prevenir envío si hay errores
        form.addEventListener('submit', function(e) {
            const erroresVisibles = this.querySelectorAll('.error-mensaje:not([style*="display: none"])');
            if (erroresVisibles.length > 0) {
                e.preventDefault();
                mostrarMensajeError('Por favor corrija los errores antes de continuar');
                return false;
            }
        });
        
        // Agregar validaciones básicas a campos comunes
        const camposTexto = form.querySelectorAll('input[type="text"], textarea');
        camposTexto.forEach(campo => {
            if (campo.hasAttribute('required')) {
                campo.addEventListener('blur', function() {
                    const errorId = 'error-' + this.id.replace('_', '-');
                    validarCampoRequerido(this, errorId);
                });
            }
        });
        
        const camposEmail = form.querySelectorAll('input[type="email"]');
        camposEmail.forEach(campo => {
            campo.addEventListener('blur', function() {
                const errorId = 'error-' + this.id.replace('_', '-');
                if (this.value.trim() !== '') {
                    validarFormatoEmail(this, errorId);
                }
            });
        });
        
        const camposNumero = form.querySelectorAll('input[type="number"]');
        camposNumero.forEach(campo => {
            campo.addEventListener('blur', function() {
                const errorId = 'error-' + this.id.replace('_', '-');
                if (this.hasAttribute('required') || this.value.trim() !== '') {
                    validarNumeroPositivo(this, errorId);
                }
            });
        });
    });
});