<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Menu;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Menampilkan semua transaksi
    public function index()
    {
        $transactions = Transaction::with('menu')->get();
        return response()->json($transactions, 200);
    }

    // Menambahkan transaksi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Ambil data menu untuk menghitung total harga
        $menu = Menu::findOrFail($validated['menu_id']);
        $total_price = $menu->price * $validated['quantity'];

        // Simpan transaksi
        $transaction = Transaction::create([
            'menu_id' => $validated['menu_id'],
            'quantity' => $validated['quantity'],
            'total_price' => $total_price,
        ]);

        return response()->json($transaction, 201);
    }

    // Menampilkan detail transaksi berdasarkan ID
    public function show(Transaction $transaction)
    {
        $transaction->load('menu');
        return response()->json($transaction, 200);
    }

    // Memperbarui data transaksi
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'menu_id' => 'sometimes|exists:menus,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        if ($request->has('menu_id')) {
            $menu = Menu::findOrFail($validated['menu_id']);
            $transaction->menu_id = $validated['menu_id'];
        } else {
            $menu = $transaction->menu;
        }

        if ($request->has('quantity')) {
            $transaction->quantity = $validated['quantity'];
        }

        $transaction->total_price = $menu->price * $transaction->quantity;
        $transaction->save();

        return response()->json($transaction, 200);
    }

    // Menghapus transaksi
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(null, 204);
    }
}
