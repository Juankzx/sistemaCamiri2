<!-- Modal de Confirmación -->
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printModalLabel">Confirmación de impresión</h5>
                <!-- Botón de cierre personalizado -->
                

            </div>
            <div class="modal-body">
                <p>¿Desea imprimir el recibo de la venta?</p>
            </div>

            <div class="modal-body">
                <p><strong>Total de la Venta:</strong> <span id="modalTotalVenta">$0</span></p>
                <p><strong>Monto Recibido:</strong> <span id="modalMontoRecibido">$0</span></p>
                <p><strong>Vuelto:</strong> <span id="modalVuelto">$0</span></p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yesPrintButton">Sí, imprimir</button>    
                <button type="button" class="btn btn-secondary" id="noPrintButton" data-bs-dismiss="modal">No</button>   
            </div>
        </div>
    </div>
</div>



