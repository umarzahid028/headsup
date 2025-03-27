<?php

namespace App\Livewire\WeOwe;

use App\Models\Vehicle;
use App\Models\WeOweItem;
use App\Models\Vendor;
use App\Models\User;
use LivewireUI\Modal\ModalComponent;
use Illuminate\Support\Facades\Auth;

class CreateModal extends ModalComponent
{
    public $vehicle_id;
    public $details;
    public $description;
    public $type = 'we_owe';
    public $cost = 0;
    public $status = 'pending';
    public $assigned_to;
    public $vendor_id;
    public $due_date;
    
    public function mount($vehicle_id = null)
    {
        $this->vehicle_id = $vehicle_id;
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
        ];
    }
    
    public function save()
    {
        $this->validate();
        
        WeOweItem::create([
            'vehicle_id' => $this->vehicle_id,
            'details' => $this->details,
            'description' => $this->description,
            'type' => $this->type,
            'cost' => $this->cost,
            'status' => $this->status,
            'assigned_to' => $this->assigned_to,
            'vendor_id' => $this->vendor_id,
            'due_date' => $this->due_date,
        ]);
        
        $this->closeModal();
        
        session()->flash('success', 'We-Owe item created successfully.');
        
        $this->redirect(route('we-owe.index'));
    }
    
    public function render()
    {
        return view('livewire.we-owe.create-modal', [
            'vehicles' => Vehicle::orderBy('stock_number')->get(),
            'vendors' => Vendor::orderBy('name')->get(),
            'users' => User::role('staff')->orderBy('name')->get(),
        ]);
    }
} 