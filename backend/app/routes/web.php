<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/barcode/generate', function (\Illuminate\Http\Request $request) {
    return view('barcode.tag', [
        'sku' => $request->get('sku'),
        'name' => $request->get('name'),
        'price' => $request->get('price'),
        'show_brand' => $request->boolean('show_brand'),
        'show_name' => $request->boolean('show_name'),
        'show_barcode' => $request->boolean('show_barcode'),
        'show_sku' => $request->boolean('show_sku'),
        'show_price' => $request->boolean('show_price'),
    ]);
})->name('barcode.generate');

Route::get('/admin/orders/{order}/invoice-download', function (\App\Models\Order $order) {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $order]);
    $filename = 'invoice-' . \Illuminate\Support\Str::slug($order->order_number ?? $order->id) . '.pdf';
    return $pdf->download($filename);
})->name('orders.invoice.download')->middleware('web');
