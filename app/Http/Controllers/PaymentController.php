<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Listar pagamentos do usuário autenticado
    public function index()
    {
        $payments = Auth::user()->payments;

        return response()->json($payments);
    }

    // Criar um novo pagamento
    public function store(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,medium,pro',
            'amount' => 'required|numeric|min:0',
        ]);

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'plan' => $request->plan,
            'amount' => $request->amount,
            'payment_status' => 'pending',
            'due_date' => now()->addMonth(),
        ]);

        return response()->json($payment, 201);
    }

    // Atualizar o status do pagamento
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_status' => 'required|in:completed,pending,failed',
        ]);

        if ($payment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payment->update([
            'payment_status' => $request->payment_status,
            'payment_date' => $request->payment_status === 'completed' ? now() : null,
        ]);

        return response()->json($payment);
    }

    // Exibir um pagamento específico
    public function show(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($payment);
    }
}
