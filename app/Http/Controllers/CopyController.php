<?php

namespace App\Http\Controllers;

use App\Models\Copy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CopyController extends Controller
{
    public function destroy(Copy $copy)
    {
        // Solo admin y librarian pueden eliminar copias
        if (! Auth::user()->hasRole(['admin', 'librarian'])) {
            abort(403, 'No tienes permiso para eliminar copias.');
        }

        // Verificar que la copia no esté prestada
        if ($copy->loans->where('status', 'active')->isNotEmpty()) {
            return redirect()->back()->with('error', 'No se puede eliminar una copia que está prestada.');
        }

        $copy->delete();

        return redirect()->back()->with('success', 'Copia eliminada correctamente.');
    }
}