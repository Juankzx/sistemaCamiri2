<!-- Modal de Confirmación -->
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printModalLabel">Confirmación de impresión</h5>
                <!-- Botón de cierre personalizado -->
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"></button>
                <span aria-hidden="true">&times;</span>
            </div>
            <div class="modal-body">
                ¿Desea imprimir el recibo de la venta?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yesPrintButton">Sí, imprimir</button>    
                <button type="button" class="btn btn-secondary" id="noPrintButton" data-bs-dismiss="modal">No</button>   
            </div>
        </div>
    </div>
</div>

<!-- Estilos específicos para el modal -->
<style>
    .close {
    font-size: 2rem;
    color: #000; /* Cambiar a tu color preferido */
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

</style>
