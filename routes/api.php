<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Dashboard\Faturamento\DashboardFaturamentoController;
use App\Http\Controllers\Dashboard\Faturamento\DashboardFaturamentoDrillController;
use App\Http\Controllers\Dashboard\Vendas\DashboardVendasController;
use App\Http\Controllers\Dashboard\Vendas\DashboardVendasPorFormaDePagamentoController;
use App\Http\Controllers\Dashboard\Vendas\DashboardVendasPorTabelaDePrecoController;
use App\Http\Controllers\Dashboard\Vendas\Vendedores\DashboardVendasPorVendedoresController;
use App\Http\Controllers\Dashboard\Vendas\Vendedores\DashboardVendasVendedoresPorFormaDePagamentoController;
use App\Http\Controllers\HomeController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth', [AuthController::class, 'auth']);
Route::post('/auth/google', [GoogleAuthController::class, 'handleGoogleLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    $user = $request->user();

    return new UserResource($user);
})->middleware('auth:sanctum');

Route::get('/home', [HomeController::class, 'index'])->middleware('auth:sanctum');

Route::post('/dashboard/faturamento',
    [DashboardFaturamentoController::class, 'index'])->middleware('auth:sanctum');

Route::post('/dashboard/faturamento/drill',
    [DashboardFaturamentoDrillController::class, 'index'])->middleware('auth:sanctum');

Route::get('/dashboard/vendas/',
    [DashboardVendasController::class, 'index'])->middleware('auth:sanctum');

Route::get('/dashboard/vendas/tabeladepreco',
    [DashboardVendasPorTabelaDePrecoController::class, 'index'])->middleware('auth:sanctum');

Route::post('/dashboard/vendas/tabeladepreco',
    [DashboardVendasPorTabelaDePrecoController::class, 'post'])->middleware('auth:sanctum');

Route::get('/dashboard/vendas/formadepagamento',
    [DashboardVendasPorFormaDePagamentoController::class, 'index'])->middleware('auth:sanctum');

Route::post('/dashboard/vendas/formadepagamento',
    [DashboardVendasPorFormaDePagamentoController::class, 'post'])->middleware('auth:sanctum');

Route::post('/dashboard/vendas/vendedores',
    [DashboardVendasPorVendedoresController::class, 'index'])->middleware('auth:sanctum');

Route::post('/dashboard/vendas/vendedores/formadepagamento',
    [DashboardVendasVendedoresPorFormaDePagamentoController::class, 'post'])->middleware('auth:sanctum');

Route::get('/dashboard/vendas/vendedores/formadepagamento',
    [DashboardVendasVendedoresPorFormaDePagamentoController::class, 'index'])->middleware('auth:sanctum');

Route::post('/dashboard/vendas/vendedores/formadepagamento/drill',
    [DashboardVendasVendedoresPorFormaDePagamentoController::class, 'drill'])->middleware('auth:sanctum');
