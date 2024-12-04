<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,medium,pro',
        ]);

        // Mapeamento do plano para price_id do Stripe
        $priceIds = [
            'basic' => 'prod_RJttLaoTzJzWS7',
            'medium' => 'prod_RJtux12AovYv6r',
            'pro' => 'prod_RJtunRY5BliTm8',
        ];

        Stripe::setApiKey(config('services.stripe.secret'));

        // Criar uma sessão de checkout
        $session = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => Auth::user()->email,
            'line_items' => [[
                'price' => $priceIds[$request->plan],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancel'),
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function checkoutSuccess()
    {
        // Sucesso no pagamento, atualizar dados do usuário
        return response()->json(['message' => 'Pagamento bem-sucedido!']);
    }

    public function checkoutCancel()
    {
        return response()->json(['message' => 'Pagamento cancelado!']);
    }
}
