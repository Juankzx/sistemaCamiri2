<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Sucursale;
use App\Models\Bodega;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventarioController extends Controller
{
    // Muestra el listado del inventario
// Muestra el listado del inventario
public function index(Request $request): View
{
    // Obtener la consulta base del inventario con las relaciones necesarias
    $query = Inventario::with(['producto.unidadMedida', 'sucursal', 'bodega']);

    // Aplicar filtro de búsqueda si se especifica
    if ($request->filled('search')) {
        $query->whereHas('producto', function ($q) use ($request) {
            $q->where('nombre', 'like', '%' . $request->search . '%')
              ->orWhere('codigo_barra', 'like', '%' . $request->search . '%')
              ->orWhereHas('categoria', function ($q2) use ($request) {
                  $q2->where('nombre', 'like', '%' . $request->search . '%');
              });
        });
    }

    // Filtrar por sucursal si se proporciona
    if ($request->filled('sucursal')) {
        $query->where('sucursal_id', $request->sucursal);
    }

    // Obtener resultados con paginación y relaciones
    $inventarios = $query->paginate(15); // Cambiar el número según la cantidad de elementos por página que deseas

    // Obtener todos los inventarios para búsqueda adicional en la vista (si es necesario)
    $todosInventarios = Inventario::with(['producto', 'sucursal', 'bodega'])->get();

    // Obtener las sucursales y bodegas disponibles para el filtro
    $sucursales = Sucursale::all();
    $bodegas = Bodega::all();

    // Retornar la vista con todas las variables necesarias
    return view('inventarios.index', compact('inventarios', 'sucursales', 'todosInventarios', 'bodegas'));
}


    

    // Muestra el formulario para crear un nuevo inventario
    public function create()
{
    $productos = Producto::with('categoria')->get();
    $bodegas = Bodega::all();
    $sucursales = Sucursale::all();

    // Obtener los productos que ya están en el inventario
    $productosInventariados = Inventario::pluck('producto_id')->toArray();

    return view('inventarios.create', compact('productos', 'sucursales', 'bodegas', 'productosInventariados'));
}



    // Almacena un nuevo inventario en la base de datos
    public function store(Request $request)
    {
        // Obtener el producto para verificar su unidad de medida
        $producto = Producto::with('unidadMedida')->find($request->producto_id);
    
        // Validar el formulario basándonos en la unidad de medida del producto
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'bodega_id' => 'required|exists:bodegas,id',
            'cantidad' => $producto->unidadMedida->nombre === 'Kilo' ? 'required|numeric|min:0.01' : 'required|integer|min:1',
        ], [
            'cantidad.numeric' => 'La cantidad debe ser un número válido para productos vendidos por Kilos.',
            'cantidad.integer' => 'La cantidad debe ser un número entero para productos no vendidos por Kilos.',
        ]);
    
        // Verificar si el producto ya existe en cualquier inventario
        $existe = Inventario::where('producto_id', $request->producto_id)
                            ->where(function($query) use ($request) {
                                $query->where('bodega_id', $request->bodega_id)
                                      ->orWhereNotNull('sucursal_id'); // Verificar en cualquier sucursal también
                            })
                            ->exists();
    
        // Si ya existe, redireccionar con un mensaje de error
        if ($existe) {
            return redirect()->back()->with('error', 'El producto ya existe en el inventario. No se puede duplicar.');
        }
    
        // Crear el nuevo inventario si no existe
        $inventario = Inventario::create($request->all());
    
        // Registrar el movimiento inicial en la base de datos
        Movimiento::create([
            'producto_id' => $request->producto_id,
            'bodega_id' => $request->bodega_id,
            'sucursal_id' => null,
            'tipo' => 'inicial',
            'cantidad' => $request->cantidad,
            'fecha' => now(),
            'user_id' => auth()->id()
        ]);
    
        // Redireccionar con mensaje de éxito
        return redirect()->route('inventarios.index')->with('success', 'Inventario agregado exitosamente a la bodega.');
    }
    

    // Muestra un inventario específico
    public function show(Inventario $inventario)
    {
        return view('inventarios.show', compact('inventarios'));
    }

    // Muestra el formulario para editar un inventario existente
    public function edit(Inventario $inventario)
    {
        $productos = Producto::all();
        $sucursales = Sucursale::all();
        $bodegas = Bodega::all();
        return view('inventarios.edit', compact('inventario', 'productos', 'sucursales', 'bodegas'));
    }

    // Actualiza un inventario en la base de datos
public function update(Request $request, Inventario $inventario)
{
    // Obtener el producto para verificar su unidad de medida
    $producto = Producto::with('unidadMedida')->find($request->producto_id);

    // Validación dinámica según la unidad de medida
    $request->validate([
        'producto_id' => 'required|exists:productos,id',
        'sucursal_id' => 'required|exists:sucursales,id',
        'bodega_id' => 'required|exists:bodegas,id',
        'cantidad' => $producto->unidadMedida->nombre === 'Kilo' ? 'required|numeric|min:0.01' : 'required|integer|min:1',
    ], [
        'cantidad.numeric' => 'La cantidad debe ser un número válido para productos vendidos por Kilos.',
        'cantidad.integer' => 'La cantidad debe ser un número entero para productos no vendidos por Kilos.',
    ]);

    // Actualizar el inventario con los datos validados
    $inventario->update($request->all());

    // Redirigir con mensaje de éxito
    return redirect()->route('inventarios.index')->with('success', 'Inventario actualizado exitosamente.');
}


    // Elimina un inventario de la base de datos
    public function destroy($id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->delete();
        return redirect()->route('inventarios.index')->with('success', 'Inventario eliminado exitosamente.');
    }

    // Incrementar la cantidad en la bodega
    public function incrementarBodega(Request $request, $inventarioId)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);  // Validar el campo cantidad

        $inventario = Inventario::findOrFail($inventarioId);
        $inventario->cantidad += $request->input('cantidad');
        $inventario->save();

        Movimiento::create([
            'producto_id' => $inventario->producto_id,
            'bodega_id' => $inventario->bodega_id,
            'sucursal_id' => null,
            'tipo' => 'entrada',
            'cantidad' => $request->input('cantidad'),
            'fecha' => now(),
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Cantidad incrementada correctamente.');
    }

    // Decrementar la cantidad en la bodega
    public function decrementarBodega(Request $request, $inventarioId)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);  // Validar el campo cantidad

        $inventario = Inventario::findOrFail($inventarioId);
        $cantidad = $request->input('cantidad');

        if ($inventario->cantidad >= $cantidad) {
            $inventario->cantidad -= $cantidad;
            $inventario->save();

            Movimiento::create([
                'producto_id' => $inventario->producto_id,
                'bodega_id' => $inventario->bodega_id,
                'sucursal_id' => null,
                'tipo' => 'salida',
                'cantidad' => $cantidad,
                'fecha' => now(),
                'user_id' => auth()->id()
            ]);

            return back()->with('success', 'Cantidad decrementada correctamente.');
        } else {
            return back()->with('error', 'No hay suficiente stock para decrementar.');
        }
    }

    // Transferir producto a una sucursal
    public function transferirASucursal(Request $request, $inventarioId)
{
    $request->validate([
        'sucursal_id' => 'required|exists:sucursales,id',
        'cantidad' => 'required|integer|min:1'
    ]);

    $inventario = Inventario::findOrFail($inventarioId);
    $cantidad = $request->input('cantidad');
    $sucursalId = $request->input('sucursal_id');

    // Verificar si hay suficiente stock en la bodega
    if ($inventario->cantidad >= $cantidad) {
        // Resta la cantidad del inventario de la bodega
        $inventario->cantidad -= $cantidad;
        $inventario->save();

        // Buscar si ya existe un inventario del mismo producto en la sucursal seleccionada
        $inventarioSucursal = Inventario::where('producto_id', $inventario->producto_id)
                            ->where('sucursal_id', $sucursalId)
                            ->first();

        if ($inventarioSucursal) {
            // Si existe en la sucursal, incrementa la cantidad
            $inventarioSucursal->cantidad += $cantidad;
            $inventarioSucursal->save();
        } else {
            // Si no existe, crea un nuevo inventario en la sucursal
            Inventario::create([
                'producto_id' => $inventario->producto_id,
                'sucursal_id' => $sucursalId,
                'cantidad' => $cantidad,
                'bodega_id' => null // Proporciona null para indicar que no está en la bodega general
            ]);
        }

        // Registrar el movimiento
        Movimiento::create([
            'producto_id' => $inventario->producto_id,
            'bodega_id' => null,
            'sucursal_id' => $sucursalId,
            'tipo' => 'transferencia',
            'cantidad' => $cantidad,
            'fecha' => now(),
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Producto transferido correctamente a la sucursal.');
    } else {
        return back()->with('error', 'No hay suficiente stock en la bodega para transferir.');
    }
}

}
