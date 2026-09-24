<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function updateStatus(): void
    {
        $this->status = $this->stock_quantity <= 0
            ? 'Out of Stock'
            : ($this->stock_quantity <= 10 ? 'Low Stock' : 'Available');

        $this->save();
    }
}