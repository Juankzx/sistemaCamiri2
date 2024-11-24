<?php

namespace App\Http\Controllers;

use App\Models\Sucursale;
use App\Models\UnidadMedida;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });
}

    
     public function index(Request $request)
     {
        

         $productos = Producto::with(['unidadmedida', 'categoria', 'proveedor'])
         ->where('estado', 1) // Filtrar solo productos con estado "activo"
            ->orderBy('created_at', 'desc') // Ordenar por 'created_at' // Ordenar los pagos por la fecha de creación en orden descendente
            ->paginate(15)
            ->withQueryString();
     
         return view('producto.index', compact('productos'))
                ->with('i', (request()->input('page', 1) - 1) * 10);
     }
     


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Solo cargar categorías activas
        $unidadMedida = UnidadMedida::where('estado', true)->get();

        $producto = new Producto();
        
        // Solo cargar categorías activas
        $categorias = Categoria::where('estado', true)->get();
        
        // Solo cargar proveedores activas
        $proveedores = Proveedore::where('estado', true)->get();

        return view('producto.create', compact('producto', 'categorias', 'proveedores', 'unidadMedida'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar que 'nombre' y 'codigo_barra' sean únicos en combinación
        $request->validate([
            'codigo_barra' => 'required|string|max:255|unique:productos,codigo_barra',
            'nombre' => 'required|string|max:255|unique:productos,nombre',
            'unidadmedida_id' => 'required|exists:unidad_medida,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'preciocompra' => 'required|integer',
            'precioventa' => 'required|integer',
            'categoria_id' => 'nullable|exists:categorias,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
        ], [
            'nombre.unique' => 'El nombre del producto ya existe. Por favor, elija un nombre diferente.',
            'codigo_barra.unique' => 'El código de barras ya está registrado para otro producto.',
        ]);
    
        $data = $request->all();
    
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('imagenes', 'public');
        }
    
        Producto::create($data);
    
        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
    }
    
    public function update(Request $request, Producto $producto): RedirectResponse
    {
        // Validar 'nombre' y 'codigo_barra' asegurando que no se dupliquen excepto para el producto actual
        $request->validate([
            'codigo_barra' => 'required|string|max:255|unique:productos,codigo_barra,' . $producto->id,
            'nombre' => 'required|string|max:255|unique:productos,nombre,' . $producto->id,
            'unidadmedida_id' => 'required|exists:unidad_medida,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'preciocompra' => 'required|numeric',
            'precioventa' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
        ], [
            'nombre.unique' => 'El nombre del producto ya existe. Por favor, elija un nombre diferente.',
            'codigo_barra.unique' => 'El código de barras ya está registrado para otro producto.',
        ]);
    
        // Actualizar el producto con los datos validados
        $producto->codigo_barra = $request->codigo_barra;
        $producto->nombre = $request->nombre;
        $producto->unidadmedida_id = $request->unidadmedida_id; // Asignar la unidad de medida
        $producto->preciocompra = $request->preciocompra;
        $producto->precioventa = $request->precioventa;
        $producto->categoria_id = $request->categoria_id;
        $producto->proveedor_id = $request->proveedor_id;
    
        // Si se sube una nueva imagen, eliminar la anterior y guardar la nueva
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen antigua si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
    
            // Guardar la nueva imagen y actualizar en la base de datos
            $producto->imagen = $request->file('imagen')->store('imagenes', 'public');
        }
    
        $producto->save();
    
        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente.');
    }
    

    
    /**
     * Display the specified resource.
     */
    public function show($id)
{
    Log::info('Intentando mostrar detalles del producto con ID:', ['id' => $id]);

    try {
        $producto = Producto::findOrFail($id);
        Log::info('Producto encontrado:', ['producto' => $producto->toArray()]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::error('Producto no encontrado con ID:', ['id' => $id]);
        return redirect()->route('productos.index')->with('error', 'Producto no encontrado.');
    }

    return view('producto.show', compact('producto'));
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        if (auth()->user()->hasRole('bodeguero')) {
            abort(403, 'No tienes permiso para editar este producto.');
        }

        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $proveedores = Proveedore::all();
        $sucursales = Sucursale::all();
        $unidadMedida = UnidadMedida::all();

        return view('producto.edit', compact('producto', 'categorias', 'proveedores', 'sucursales', 'unidadMedida'));
    }

   

    public function destroy($id): RedirectResponse
    {
        if (auth()->user()->hasRole('bodeguero')) {
            abort(403, 'No tienes permiso para editar este producto.');
        }

        // Cambiar el estado a "inactivo" en lugar de eliminar el producto
        $producto = Producto::find($id);
        if ($producto) {
            $producto->estado = false;
            $producto->save();
        }

        return Redirect::route('productos.index')
            ->with('success', 'Producto deleted successfully');
    }

    public function getProductos()
{
    $productos = Producto::all()->map(function ($producto) {
        $producto->imagen_url = $producto->imagen ? asset('storage/imagenes/' . $producto->imagen) : asset('default_image_path'); // Asegúrate de tener una imagen por defecto
        return $producto;
    });

    return response()->json($productos);
}

public function getProductosPorSucursal($sucursalId)
    {
        $productos = Producto::whereHas('inventarios', function($query) use ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        })->with(['inventarios' => function($query) use ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }])->get();

        return response()->json($productos);
    }

    public function search(Request $request)
{
    $search = $request->input('query');

    // Verificar que se recibió la consulta de búsqueda
    if (is_null($search) || $search === '') {
        return response()->json([], 200);
    }

    // Filtrar productos por nombre o código de barras
    $productos = Producto::with(['unidadmedida', 'categoria', 'proveedor'])
                         ->where('nombre', 'like', "%$search%")
                         ->orWhere('codigo_barra', 'like', "%$search%")
                         ->paginate(10);

    // Devolver los productos como JSON
    return response()->json($productos->items());
}

public function buscar(Request $request)
    {
        $query = $request->input('query');

        // Filtrar productos que coincidan con el término de búsqueda
        $productos = Producto::where('nombre', 'LIKE', "%$query%")
            ->orWhere('codigo_barra', 'LIKE', "%$query%")
            ->with('categoria') // Asegúrate de cargar la categoría si deseas mostrarla
            ->limit(10) // Limitar la cantidad de resultados
            ->get();

        // Formatear los productos para la respuesta JSON
        $productosFormateados = $productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo_barra' => $producto->codigo_barra,
                'categoria' => $producto->categoria->nombre ?? 'Sin categoría'
            ];
        });

        return response()->json($productosFormateados);
    }







        

}
