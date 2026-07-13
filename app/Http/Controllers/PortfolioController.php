<?php

namespace App\Http\Controllers;

use App\Models\FotoPortfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function portfolio()
    {
        // Busca as fotos trazendo junto os dados da manicure (evita lentidão no banco)
        $fotos = FotoPortfolio::with('manicure')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('portfolio', compact('fotos'));
    }
}