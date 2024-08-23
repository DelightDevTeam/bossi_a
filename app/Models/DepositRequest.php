<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function Ramsey\Uuid\v1;

class DepositRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'agent_id', 'agent_payment_type_id', 'amount', 'status', 'refrence_no'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class,'agent_id');
    }
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class,'agent_payment_type_id');
    }

}
