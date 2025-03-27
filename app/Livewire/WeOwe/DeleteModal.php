<?php

namespace App\Livewire\WeOwe;

use App\Models\WeOweItem;
use LivewireUI\Modal\ModalComponent;

class DeleteModal extends ModalComponent
{
    public $weOweItem;
    public $item_id;
    
    public function mount($id)
    {
        $this->item_id = $id;
        $this->weOweItem = WeOweItem::findOrFail($id);
    }
    
    public function delete()
    {
        $this->weOweItem->delete();
        
        $this->closeModal();
        
        session()->flash('success', 'We-Owe item deleted successfully.');
        
        $this->redirect(route('we-owe.index'));
    }
    
    public function render()
    {
        return view('livewire.we-owe.delete-modal');
    }
} 