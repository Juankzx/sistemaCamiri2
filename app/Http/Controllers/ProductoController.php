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
    
     public function index(Request $request)
     {
         $productos = Producto::with(['unidadmedida', 'categoria', 'proveedor'])
                              ->paginate(10)
                              ->withQueryString();
     
         return view('producto.index', compact('productos'))
                ->with('i', (request()->input('page', 1) - 1) * 10);
     }
     


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unidadMedida = UnidadMedida::all();
        $producto = new Producto();
        $categorias = Categoria::all();
        $proveedores = Proveedore::all();

        return view('producto.create', compact('producto', 'categorias', 'proveedores', 'unidadMedida'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo_barra' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'unidadmedida_id' => 'required|exists:unidad_medida,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'preciocompra' => 'required|integer',
            'precioventa' => 'required|integer',
            'categoria_id' => 'nullable|exists:categorias,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'estado' => 'required|boolean',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('imagenes', 'public');
        }

        Producto::create($data);

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
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
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $proveedores = Proveedore::all();
        $sucursales = Sucursale::all();
        $unidadMedida = UnidadMedida::all();

        return view('producto.edit', compact('producto', 'categorias', 'proveedores', 'sucursales', 'unidadMedida'));
    }

    public function update(Request $request, Producto $producto): RedirectResponse
{
    $validated = $request->validate([
        'codigo_barra' => 'required|string|max:255',
        'nombre' => 'required|string|max:255',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'preciocompra' => 'required|numeric',
        'precioventa' => 'required|numeric',
        'categoria_id' => 'required|exists:categorias,id',
        'proveedor_id' => 'required|exists:proveedores,id',
    ]);

    $producto->codigo_barra = $validated['codigo_barra'];
    $producto->nombre = $validated['nombre'];
    $producto->preciocompra = $validated['preciocompra']; // Corregido
    $producto->precioventa = $validated['precioventa']; // Corregido
    $producto->categoria_id = $validated['categoria_id'];
    $producto->proveedor_id = $validated['proveedor_id'];

    if ($request->hasFile('imagen')) {
        // Eliminar imagen antigua
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        // Guardar nueva imagen
        $imagen_path = $request->file('imagen')->store('imagenes', 'public');
        // Guardar en la base de datos
        $producto->imagen = $imagen_path;
    }

    $producto->save();
    
    return redirect()->route('productos.index')->with('success', 'Éxito, su producto ha sido actualizado.');
}

    public function destroy($id): RedirectResponse
    {
        Producto::find($id)->delete();

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





        

}
