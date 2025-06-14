<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'plan', 'amount', 'payment_status', 'due_date', 'payment_date'];

    // Relacionamento com o usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Escopo para buscar pagamentos pendentes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    // Método para verificar se o pagamento está atrasado
    public function isOverdue()
    {
        return $this->due_date < now() && $this->payment_status !== 'completed';
    }
}
