<?php

namespace App\Livewire\Actions;

use App\Models\Faq as FaqModel;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('FAQ')]
class Faq extends Component
{
    public string $question = '';

    public string $answer = '';

    public bool $isActive = true;

    public ?int $editingId = null;

    public bool $showForm = false;

    public ?int $deletingId = null;

    public string $deletingQuestion = '';

    /**
     * Referensi form di URL: '' (tertutup), 'create', atau id untuk edit.
     */
    #[Url(as: 'form', except: '')]
    public string $form = '';

    public function mount(): void
    {
        if ($this->form === 'create') {
            $this->openCreate();

            return;
        }

        if (is_numeric($this->form)) {
            $faq = FaqModel::find((int) $this->form);

            if ($faq) {
                $this->edit($faq->id);

                return;
            }
        }

        $this->form = '';
    }

    #[Computed]
    public function faqs()
    {
        return FaqModel::orderBy('order')->get();
    }

    public function save(): void
    {
        $this->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:2000'],
            'isActive' => ['boolean'],
        ]);

        if ($this->editingId) {
            FaqModel::findOrFail($this->editingId)->update([
                'question' => $this->question,
                'answer' => $this->answer,
                'is_active' => $this->isActive,
            ]);
            Flux::toast(variant: 'success', text: 'FAQ berhasil diperbarui.');
        } else {
            FaqModel::create([
                'question' => $this->question,
                'answer' => $this->answer,
                'is_active' => $this->isActive,
            ]);
            Flux::toast(variant: 'success', text: 'FAQ berhasil ditambahkan.');
        }

        $this->reset(['question', 'answer', 'isActive', 'editingId', 'showForm', 'form']);
        unset($this->faqs);
    }

    public function edit(int $id): void
    {
        $faq = FaqModel::findOrFail($id);
        $this->editingId = $id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->isActive = $faq->is_active;
        $this->form = (string) $id;
        $this->showForm = true;
    }

    public function openCreate(): void
    {
        $this->reset(['question', 'answer', 'editingId']);
        $this->isActive = true;
        $this->form = 'create';
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['question', 'answer', 'isActive', 'editingId', 'showForm', 'form']);
    }

    public function toggleActive(int $id): void
    {
        $faq = FaqModel::findOrFail($id);
        $faq->update(['is_active' => ! $faq->is_active]);
        unset($this->faqs);
    }

    public function moveUp(int $id): void
    {
        $faq = FaqModel::findOrFail($id);
        $previous = FaqModel::where('order', '<', $faq->order)->orderByDesc('order')->first();

        if ($previous) {
            [$faq->order, $previous->order] = [$previous->order, $faq->order];
            $faq->save();
            $previous->save();
            unset($this->faqs);
        }
    }

    public function moveDown(int $id): void
    {
        $faq = FaqModel::findOrFail($id);
        $next = FaqModel::where('order', '>', $faq->order)->orderBy('order')->first();

        if ($next) {
            [$faq->order, $next->order] = [$next->order, $faq->order];
            $faq->save();
            $next->save();
            unset($this->faqs);
        }
    }

    public function copy(int $id): void
    {
        $faq = FaqModel::findOrFail($id);

        FaqModel::create([
            'question' => $faq->question.' (Salinan)',
            'answer' => $faq->answer,
            'is_active' => false,
        ]);

        unset($this->faqs);
        Flux::toast(variant: 'success', text: 'FAQ berhasil disalin.');
    }

    public function confirmDelete(int $id): void
    {
        $faq = FaqModel::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingQuestion = $faq->question;
        $this->modal('confirm-delete-faq')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        FaqModel::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->deletingQuestion = '';
        unset($this->faqs);
        $this->modal('confirm-delete-faq')->close();
        Flux::toast(variant: 'success', text: 'FAQ berhasil dihapus.');
    }

    public function render(): View
    {
        return view('livewire.faqs');
    }
}
