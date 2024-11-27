<!-- Modal para el PIN -->

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="modal fade" id="pinModal" tabindex="-1" role="dialog" aria-labelledby="pinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content p-4 text-center">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100" id="pinModalLabel">Introduce tu PIN</h5>
            </div>
            <div class="modal-body">
                <!-- Indicador de PIN -->
                <div class="pin-indicator mb-4">
                    <span class="dot" id="dot-1"></span>
                    <span class="dot" id="dot-2"></span>
                    <span class="dot" id="dot-3"></span>
                    <span class="dot" id="dot-4"></span>
                    <span class="dot" id="dot-5"></span>
                    <span class="dot" id="dot-6"></span>
                </div>

                <!-- Teclado Numérico -->
                <div class="numeric-keypad">
                    <div class="row">
                        <button class="key" onclick="addPinDigit(1)">1</button>
                        <button class="key" onclick="addPinDigit(2)">2</button>
                        <button class="key" onclick="addPinDigit(3)">3</button>
                    </div>
                    <div class="row">
                        <button class="key" onclick="addPinDigit(4)">4</button>
                        <button class="key" onclick="addPinDigit(5)">5</button>
                        <button class="key" onclick="addPinDigit(6)">6</button>
                    </div>
                    <div class="row">
                        <button class="key" onclick="addPinDigit(7)">7</button>
                        <button class="key" onclick="addPinDigit(8)">8</button>
                        <button class="key" onclick="addPinDigit(9)">9</button>
                    </div>
                    <div class="row">
                        <button class="key action-key" onclick="clearPin()">&#x232b;</button>
                        <button class="key" onclick="addPinDigit(0)">0</button>
                        <button class="key action-key" onclick="submitPin()">&#x2713;</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!-- Formulario para enviar el PIN -->
<form id="pinForm" action="{{ route('loginWithPin') }}" method="POST">
    @csrf
    <input type="hidden" id="pinInput" name="pin">
</form>

<script>
    let pin = '';

// Escucha eventos de teclado
document.addEventListener('keydown', function (event) {
    if (event.key >= '0' && event.key <= '9') {
        addPinDigit(parseInt(event.key)); // Asegúrate de convertir el carácter a número
    } else if (event.key === 'Backspace') {
        removeLastDigit(); // Borrar un dígito
    } else if (event.key === 'Enter') {
        submitPin();
    }
});

// Agregar dígito al PIN
function addPinDigit(digit) {
    if (pin.length < 6) {
        pin += digit;
        updatePinIndicator();
    }
}

// Borrar el último dígito
function removeLastDigit() {
    if (pin.length > 0) {
        pin = pin.slice(0, -1); // Eliminar el último dígito
        updatePinIndicator();
    }
}

// Limpiar el PIN completo
function clearPin() {
    pin = '';
    updatePinIndicator();
}

// Enviar el PIN
function submitPin() {
    if (pin.length === 6) {
        const pinField = document.getElementById('pinInput');
        pinField.value = pin;

        fetch('{{ route('loginWithPin') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ pin }),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la solicitud');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito y redirigir directamente
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Autenticado correctamente.',
                        showConfirmButton: false,
                        timer: 500, // Mostrar mensaje por 1.5 segundos
                    }).then(() => {
                        window.location.href = data.redirect || '/';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'PIN incorrecto.',
                    });
                    clearPin(); // Limpiar el PIN si es incorrecto
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al autenticar. Intenta nuevamente.',
                });
                clearPin(); // Limpiar el PIN si ocurre un error
            });
    } else {

    }
}

// Actualizar el indicador visual del PIN
function updatePinIndicator() {
    for (let i = 1; i <= 6; i++) {
        const dot = document.getElementById(`dot-${i}`);
        if (i <= pin.length) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    }
}

</script>

<!-- Estilos CSS -->
<style>
    .modal-content {
        border-radius: 15px;
        background: #f7f7f7;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }

    .pin-indicator {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .pin-indicator .dot {
        width: 15px;
        height: 15px;
        background: #ddd;
        border-radius: 50%;
        border: 2px solid #aaa;
        transition: background 0.3s;
    }

    .pin-indicator .dot.active {
        background: #007bff;
        border-color: #0056b3;
    }

    .numeric-keypad {
        display: grid;
        gap: 10px;
    }

    .numeric-keypad .row {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .key {
        width: 60px;
        height: 60px;
        border: none;
        border-radius: 50%;
        background: #007bff;
        color: white;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        transition: background 0.3s, transform 0.2s;
    }

    .key:hover {
        background: #0056b3;
    }

    .key:active {
        transform: scale(0.95);
    }

    .action-key {
        background: #dc3545;
    }

    .action-key:hover {
        background: #b02a37;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
