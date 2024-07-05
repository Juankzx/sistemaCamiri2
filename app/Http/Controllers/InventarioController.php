<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Sucursale;
use App\Models\Inventario;
use App\Models\Movimiento;
use App\Models\Bodega;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\InventarioRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $inventarios = Inventario::with(['producto', 'sucursal', 'bodega'])->paginate();

        return view('inventario.index', compact('inventarios'))
            ->with('i', ($request->input('page', 1) - 1) * $inventarios->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $inventario = new Inventario();
        $productos = Producto::all();
        $sucursales = Sucursale::all();
        $bodegas = Bodega::all();

        return view('inventario.create', compact('inventario', 'productos', 'sucursales', 'bodegas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventarioRequest $request): RedirectResponse
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'bodega_id' => 'required|exists:bodegas,id',
            'cantidad' => 'required|integer|min:1',
        ]);
    
        // Verificar si el producto ya está registrado en la misma sucursal
        $exists = Inventario::where('producto_id', $request->producto_id)
                            ->where('sucursal_id', $request->sucursal_id)
                            ->where('bodega_id', $request->bodega_id)
                            ->exists();
    
        if ($exists) {
            return redirect()->route('inventarios.create')->with('error', 'El producto ya está registrado en esta sucursal.');
        }
    
        // Crear nuevo inventario
        Inventario::create([
            'producto_id' => $request->producto_id,
            'sucursal_id' => $request->sucursal_id,
            'bodega_id' => $request->bodega_id,
            'cantidad' => $request->cantidad,
        ]);
        // Registrar el movimiento de entrada
        Movimiento::create([
            'producto_id' => $request->producto_id,
            'sucursal_id' => $request->sucursal_id,
            'tipo' => 'entrada',
            'cantidad' => $request->cantidad,
            'fecha' => Carbon::now(),
            'user_id' => Auth::id(),
        ]);
    
        return redirect()->route('inventarios.index')->with('success', 'Inventario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $inventario = Inventario::find($id);

        return view('inventario.show', compact('inventario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $inventario = Inventario::find($id);

        return view('inventario.edit', compact('inventario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventarioRequest $request, Inventario $inventario): RedirectResponse
    {
        $inventario->update($request->validated());

        return Redirect::route('inventarios.index')
            ->with('success', 'Inventario updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Inventario::find($id)->delete();

        return Redirect::route('inventarios.index')
            ->with('success', 'Inventario deleted successfully');
    }

    public function updateQuantity(Request $request, $id): RedirectResponse
{
    $request->validate([
        'cantidad' => 'required|integer|min:1',
    ]);

    $inventario = Inventario::findOrFail($id);
    $inventario->increment('cantidad', $request->cantidad);

     // Registrar el movimiento de entrada
     Movimiento::create([
        'producto_id' => $inventario->producto_id,
        'sucursal_id' => $inventario->sucursal_id,
        'tipo' => 'Entrada',
        'cantidad' => $request->cantidad,
        'fecha' => now(),
        'user_id' => Auth::id(),
    ]);

    return redirect()->route('inventarios.index')->with('success', 'Cantidad aumentada exitosamente.');
}

    public function decreaseQuantity(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $inventario = Inventario::findOrFail($id);

        if ($inventario->cantidad >= $request->cantidad) {
            $inventario->decrement('cantidad', $request->cantidad);

            // Registrar el movimiento de salida
            Movimiento::create([
                'producto_id' => $inventario->producto_id,
                'sucursal_id' => $inventario->sucursal_id,
                'tipo' => 'salida',
                'cantidad' => $request->cantidad,
                'fecha' => Carbon::now(),
                'user_id' => Auth::id(),
            ]);


            return redirect()->route('inventarios.index')->with('success', 'Cantidad restada exitosamente.');
        } else {
            return redirect()->route('inventarios.index')->with('error', 'La cantidad a restar excede la cantidad actual en el inventario.');
        }
    }
    public function updateInventario(Request $request, $sucursalId, $productoId)
    {
        // Actualiza el inventario en la sucursal
        $inventario = Inventario::where('sucursal_id', $sucursalId)
            ->where('producto_id', $productoId)
            ->first();

        if ($inventario) {
            $inventario->cantidad = $request->input('cantidad');
            $inventario->save();
        } else {
            // Crear un nuevo inventario si no existe
            $inventario = new Inventario();
            $inventario->sucursal_id = $sucursalId;
            $inventario->producto_id = $productoId;
            $inventario->cantidad = $request->input('cantidad');
            $inventario->bodega_id = null; // Sin bodega para la sucursal
            $inventario->save();
        }

        // Actualiza el inventario en la bodega general
        $this->updateBodegaGeneralInventario($productoId);

        return response()->json(['message' => 'Inventario actualizado con éxito']);
    }

    private function updateBodegaGeneralInventario($productoId)
    {
        // Suma las cantidades de todas las sucursales para el producto especificado
        $totalCantidad = Inventario::where('producto_id', $productoId)->sum('cantidad');

        // Encuentra el inventario de la bodega general para el producto
        $bodegaGeneral = Bodega::where('nombre', 'Bodega General')->first();

        if (!$bodegaGeneral) {
            // Crear la bodega general si no existe
            $bodegaGeneral = new Bodega();
            $bodegaGeneral->nombre = 'Bodega General';
            $bodegaGeneral->save();
        }

        $bodegaInventario = Inventario::where('bodega_id', $bodegaGeneral->id)
            ->where('producto_id', $productoId)
            ->first();

        if ($bodegaInventario) {
            $bodegaInventario->cantidad = $totalCantidad;
            $bodegaInventario->save();
        } else {
            // Crear un nuevo inventario de bodega si no existe
            $bodegaInventario = new Inventario();
            $bodegaInventario->producto_id = $productoId;
            $bodegaInventario->cantidad = $totalCantidad;
            $bodegaInventario->bodega_id = $bodegaGeneral->id;
            $bodegaInventario->save();
        }
    }
}
