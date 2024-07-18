<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Sucursale;
use App\Models\Bodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


class InventarioController extends Controller
{
    // Muestra el listado del inventario
    public function index(Request $request): View
    {
        $query = Inventario::with(['producto', 'sucursal', 'bodega']);
    
        if ($request->filled('search')) {
            $query->whereHas('producto', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('codigo_barra', 'like', '%' . $request->search . '%')
                  ->orWhereHas('categoria', function ($q2) use ($request) {
                      $q2->where('nombre', 'like', '%' . $request->search . '%');
                  });
            });
        }
    
        if ($request->filled('sucursal')) {
            $query->where('sucursal_id', $request->sucursal);
        }
    
        $inventarios = $query->paginate(10);
        $sucursales = Sucursale::all();
    
        return view('inventarios.index', compact('inventarios', 'sucursales'));
    }
    

    // Muestra el formulario para crear un nuevo inventario
    public function create()
    {
        $productos = Producto::with('categoria')->get();
        $sucursales = Sucursale::all();
        $bodegas = Bodega::all();
        return view('inventarios.create', compact('productos', 'sucursales', 'bodegas'));
    }


    // Almacena un nuevo inventario en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'bodega_id' => 'required|exists:bodegas,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $inventario = Inventario::create($request->all());

        Movimiento::create([
            'producto_id' => $request->producto_id,
            'bodega_id' => $request->bodega_id,
            'sucursal_id' => null,
            'tipo' => 'inicial',
            'cantidad' => $request->cantidad,
            'fecha' => now(),
            'user_id' => auth()->id()
        ]);

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
        return view('inventarios.edit', compact('inventarios', 'productos', 'sucursales', 'bodegas'));
    }

    // Actualiza un inventario en la base de datos
    public function update(Request $request, Inventario $inventario)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'bodega_id' => 'required|exists:bodegas,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $inventario->update($request->all());
        return redirect()->route('inventarios.index')->with('success', 'Inventario actualizado exitosamente.');
    }

    // Elimina un inventario de la base de datos
    public function destroy($id)
{
    $inventario = Inventario::findOrFail($id);
    $inventario->delete();
    return redirect()->route('inventarios.index')->with('success', 'Inventario eliminado exitosamente.');
}

public function incrementarBodega(Request $request, $inventarioId)
{
    $inventario = Inventario::findOrFail($inventarioId);
    $cantidad = $request->input('cantidad', 1); // Default increment by 1 if not provided

    $inventario->cantidad += $cantidad;
    $inventario->save();

    Movimiento::create([
        'producto_id' => $inventario->producto_id,
        'bodega_id' => $inventario->bodega_id,
        'sucursal_id' => null,
        'tipo' => 'entrada',
        'cantidad' => $cantidad,
        'fecha' => now(),
        'user_id' => auth()->id()
    ]);

    return back()->with('success', 'Cantidad incrementada correctamente.');
}

public function decrementarBodega(Request $request, $inventarioId)
{
    $inventario = Inventario::findOrFail($inventarioId);
    $cantidad = $request->input('cantidad', 1); // Default decrement by 1 if not provided

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

public function transferirASucursal(Request $request, $inventarioId)
{
    $inventario = Inventario::findOrFail($inventarioId);
    $cantidad = $request->input('cantidad');
    $sucursalId = $request->input('sucursal_id');
    $userId = auth()->id(); // Obtener el ID del usuario autenticado

    // Verifica si hay suficiente stock en la bodega
    if ($inventario->cantidad >= $cantidad) {
        // Resta la cantidad del inventario de la bodega
        $inventario->cantidad -= $cantidad;
        $inventario->save();

        // Verifica si ya existe inventario del producto en la sucursal
        $inventarioSucursal = Inventario::where('producto_id', $inventario->producto_id)
                            ->where('sucursal_id', $sucursalId)
                            ->first();

        if ($inventarioSucursal) {
            // Si existe, incrementa la cantidad
            $inventarioSucursal->cantidad += $cantidad;
            $inventarioSucursal->save();
        } else {
            // Si no existe, crea un nuevo registro de inventario para la sucursal
            Inventario::create([
                'producto_id' => $inventario->producto_id,
                'sucursal_id' => $sucursalId,
                'cantidad' => $cantidad,
                'bodega_id' => null // Proporciona null para asegurarse de que no está asociado con la bodega general
            ]);
        }

        // Registrar el movimiento
        Movimiento::create([
            'producto_id' => $inventario->producto_id,
            'bodega_id' => null, // Asegúrate de registrar la bodega origen
            'sucursal_id' => $sucursalId,
            'tipo' => 'transferencia',
            'cantidad' => $cantidad,
            'fecha' => now(),
            'user_id' => $userId
        ]);

        return back()->with('success', 'Producto transferido correctamente a la sucursal.');
    } else {
        return back()->with('error', 'No hay suficiente stock para transferir.');
    }
}




}
