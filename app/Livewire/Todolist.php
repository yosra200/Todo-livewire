<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;
use Mockery\Expectation;

class Todolist extends Component
{

    use WithPagination;
    public $name;
    public $search;
    public $EditingTodoId;
    public $EditingTodoName;

    public function rules()
    {
        return [
            'name' => ['required'],
            'EditingTodoName' => ['required'],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'The "name" field is required.',
            'EditingTodoName.required' => 'The "EditingTodoName" field is required.',
        ];
    }
    public function create(){
     $validated=$this->validateOnly('name');
     Todo::create($validated);
     $this->reset('name');
     session()->flash('success','created');
     

    }
   
    public function delete($todId){
        try{
            Todo::findOrFail($todId)->delete();
        }
        catch(Expectation $e){
        session()->flash('eror','failed to delete Todo');
        return;
        }
    } 
    public function toggle($todId){
        $todo=Todo::find($todId);
        $todo->completed=!$todo->completed;
        $todo->save();
    } 
    public function cancel(){
        $this->reset('EditingTodoId','EditingTodoName');
    }
    public function edit($todId){
        $this->EditingTodoId=$todId;
        $this->EditingTodoName=Todo::find($todId)->name;

    }
    public function update(){
        $this->validateOnly('EditingTodoName');
        Todo::find($this->EditingTodoId)->update(
    [
        'name'=>$this->EditingTodoName
    ]
        );

        $this->cancel();
       

    }

    
    
    public function render()
    {
        return view('livewire.todolist', [ 
            'todos' => Todo::latest()->where('name','like',"%{$this->search}%")->paginate(5)
        ]);
    }
}
