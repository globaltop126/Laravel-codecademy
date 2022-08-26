<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'project_id',
        'client_id',
        'due_date',
        'tax_id',
        'created_by',
        'status',
    ];

    public static $status = [
        '1' => 'Not Paid',
        '2' => 'Partialy Paid',
        '3' => 'Sent',
        '4' => 'Unpaid',
        '5' => 'Paid',
    ];

    public static $status_color = [
        '1' => 'info',
        '2' => 'warning',
        '3' => 'success',
        '4' => 'warning',
        '5' => 'success',
    ];

    // Get Invoice Projects
    public function project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }

    // Get Invoice Client
    public function client()
    {
        return $this->hasOne('App\Models\User', 'id', 'client_id');
    }

    // Get Invoice Users
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    // Get Invoice Based Tax
    public function tax()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax_id');
    }

    // Get Invoice Item
    public function items()
    {
        return $this->hasMany('App\Models\InvoiceProduct', 'invoice_id', 'id');
    }

    // Get Invoice Payments
    public function payments()
    {
        return $this->hasMany('App\Models\InvoicePayment', 'invoice_id', 'id');
    }

    // Get Invoice Subtotal
    public function getSubTotal()
    {
        $subTotal = 0;
        foreach($this->items as $product)
        {
            $subTotal += $product->price;
        }

        return $subTotal;
    }

    // Get Invoice Tax
    public function getTax()
    {
        $tax = ($this->getSubTotal() * (!empty($this->tax) ? $this->tax->rate : 0)) / 100.00;

        return $tax;
    }

    // Get Invoice Tax
    public function getTotal()
    {
        return $this->getSubTotal() + $this->getTax();
    }

    // Get Invoice Due Amount
    public function getDue()
    {
        $due = 0;
        foreach($this->payments as $payment)
        {
            $due += $payment->amount;
        }

        return $this->getTotal() - $due;
    }

    // Change Invoice Status
    public static function change_status($invoice_id, $status)
    {
        $invoice         = Invoice::find($invoice_id);
        $invoice->status = $status;
        $invoice->update();
    }

    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        foreach($this->items as $product)
        {
            $totalDiscount += $product->discount;
        }

        return $totalDiscount;
    }
}
