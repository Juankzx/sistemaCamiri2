@extends('adminlte::page')

@section('template_title')
    Productos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Productos') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                  {{ __('+ Agregar') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <!-- Campo de búsqueda en vivo -->
                        <input type="text" id="productSearch" class="form-control mb-3" placeholder="Buscar producto por código o nombre...">
                        <br>

                        <!-- Contenedor para los resultados de búsqueda -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="result-table">
                                <thead class="thead">
                                    <tr>
                                        <th>N°</th>   
                                        <th>Nombre</th>
                                        <th>Codigo de Barras</th>
                                        <th>Unidad de Medida</th>
                                        <th>Precio Compra</th>
                                        <th>Precio Venta</th>
                                        <th>Categoria</th>
                                        <th>Proveedor</th>  
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 0; @endphp
                                    @foreach ($productos as $producto)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $producto->nombre }}</td>
                                            <td>{{ $producto->codigo_barra }}</td>
                                            <td>{{ $producto->unidadmedida->nombre }} - {{ $producto->unidadmedida->abreviatura }}</td>
                                            <td>{{ isset($producto->preciocompra) ? '$' . number_format($producto->preciocompra, 0) : 'N/A' }}</td>
                                            <td>{{ isset($producto->precioventa) ? '$' . number_format($producto->precioventa, 0) : 'N/A' }}</td>
                                            <td>{{ $producto->categoria->nombre ?? 'Sin categoria' }}</td>
                                            <td>{{ $producto->proveedor->nombre ?? 'Sin proveedor' }}</td>
                                            <td>
                                                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" style="display:inline;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('productos.show', $producto->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('productos.edit', $producto->id) }}"><i class="fa fa-fw fa-edit"></i></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"><i class="fa fa-fw fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <p class="small text-muted">
                        Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de {{ $productos->total() }} registros
                    </p>
                </div>
                <div>
                    {{ $productos->links('pagination::bootstrap-4') }} <!-- Estilo Bootstrap 4 para la paginación -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargamos los productos desde la variable PHP en un array de objetos JS
        const products = @json($productos->items());

        // Configuración de Fuse.js para la búsqueda en vivo
        const options = {
            keys: ['nombre', 'codigo_barra'],
            threshold: 0.3
        };

        const fuse = new Fuse(products, options);

        document.getElementById('productSearch').addEventListener('input', function(e) {
            const searchText = e.target.value.trim();
            if (searchText === '') {
                displayProducts(products);
            } else {
                const result = fuse.search(searchText);
                displayProducts(result.map(r => r.item));
            }
        });

        function displayProducts(filteredProducts) {
            const tableBody = document.querySelector('#result-table tbody');
            tableBody.innerHTML = '';

            if (filteredProducts.length > 0) {
                filteredProducts.forEach((producto, index) => {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${producto.nombre}</td>
                            <td>${producto.codigo_barra}</td>
                            <td>${producto.unidadmedida ? producto.unidadmedida.nombre + ' - ' + producto.unidadmedida.abreviatura : ''}</td>
                            <td>${producto.preciocompra ? '$' + Number(producto.preciocompra).toLocaleString() : 'N/A'}</td>
                            <td>${producto.precioventa ? '$' + Number(producto.precioventa).toLocaleString() : 'N/A'}</td>
                            <td>${producto.categoria ? producto.categoria.nombre : 'Sin categoria'}</td>
                            <td>${producto.proveedor ? producto.proveedor.nombre : 'Sin proveedor'}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/productos/${producto.id}"><i class="fa fa-fw fa-eye"></i></a>
                                <a class="btn btn-sm btn-success" href="/productos/${producto.id}/edit"><i class="fa fa-fw fa-edit"></i></a>
                                <form action="/productos/${producto.id}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm delete-btn"><i class="fa fa-fw fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="10" class="text-center">No se encontraron productos.</td></tr>';
            }
            addDeleteEventListeners();
        }

        function addDeleteEventListeners() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    var form = this.closest('form');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        }

        displayProducts(products);
    });

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error en la operación',
                html: '{!! implode("<br>", $errors->all()) !!}',
                showConfirmButton: true
            });
        @endif
    });
</script>
@endsection
