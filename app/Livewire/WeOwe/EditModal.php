<?php

namespace App\Livewire\WeOwe;

use App\Models\Vehicle;
use App\Models\WeOweItem;
use App\Models\Vendor;
use App\Models\User;
use LivewireUI\Modal\ModalComponent;
use Illuminate\Support\Facades\Auth;

class EditModal extends ModalComponent
{
    public $weOweItem;
    public $item_id;
    public $vehicle_id;
    public $details;
    public $description;
    public $type;
    public $cost;
    public $status;
    public $assigned_to;
    public $vendor_id;
    public $due_date;
    public $completed_at;
    public $has_waiver;
    public $waiver_signed;
    public $waiver_signed_at;
    public $sms_sent;
    public $sms_sent_at;
    
    public function mount($id)
    {
        $this->item_id = $id;
        $this->weOweItem = WeOweItem::findOrFail($id);
        
        $this->vehicle_id = $this->weOweItem->vehicle_id;
        $this->details = $this->weOweItem->details;
        $this->description = $this->weOweItem->description;
        $this->type = $this->weOweItem->type;
        $this->cost = $this->weOweItem->cost;
        $this->status = $this->weOweItem->status;
        $this->assigned_to = $this->weOweItem->assigned_to;
        $this->vendor_id = $this->weOweItem->vendor_id;
        $this->due_date = $this->weOweItem->due_date ? $this->weOweItem->due_date->format('Y-m-d') : null;
        $this->completed_at = $this->weOweItem->completed_at;
        $this->has_waiver = $this->weOweItem->has_waiver;
        $this->waiver_signed = $this->weOweItem->waiver_signed;
        $this->waiver_signed_at = $this->weOweItem->waiver_signed_at;
        $this->sms_sent = $this->weOweItem->sms_sent;
        $this->sms_sent_at = $this->weOweItem->sms_sent_at;
    }
    
    public function rules()
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'details' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:we_owe,goodwill',
            'cost' => 'required|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'due_date' => 'nullable|date',
            'has_waiver' => 'boolean',
            'waiver_signed' => 'boolean',
        ];
    }
    
    public function save()
    {
        $this->validate();
        
        // If status is changed to completed, set completed_at
        if ($this->status === 'completed' && $this->weOweItem->status !== 'completed') {
            $this->completed_at = now();
        } elseif ($this->status !== 'completed') {
            $this->completed_at = null;
        }
        
        $this->weOweItem->update([
            'vehicle_id' => $this->vehicle_id,
            'details' => $this->details,
            'description' => $this->description,
            'type' => $this->type,
            'cost' => $this->cost,
            'status' => $this->status,
            'assigned_to' => $this->assigned_to,
            'vendor_id' => $this->vendor_id,
            'due_date' => $this->due_date,
            'completed_at' => $this->completed_at,
            'has_waiver' => $this->has_waiver,
            'waiver_signed' => $this->waiver_signed,
        ]);
        
        $this->closeModal();
        
        session()->flash('success', 'We-Owe item updated successfully.');
        
        $this->redirect(route('we-owe.index'));
    }
    
    public function render()
    {
        return view('livewire.we-owe.edit-modal', [
            'vehicles' => Vehicle::orderBy('stock_number')->get(),
            'vendors' => Vendor::orderBy('name')->get(),
            'users' => User::role('staff')->orderBy('name')->get(),
        ]);
    }
} 