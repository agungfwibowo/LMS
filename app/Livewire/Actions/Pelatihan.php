<?php

namespace App\Livewire\Actions;

use App\Actions\Pelatihan\CopyPelatihan;
use App\Enums\PelatihanStatus;
use App\Models\Pelatihan as PelatihanModel;
use App\Models\PelatihanCategory;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pelatihan')]
class Pelatihan extends Component
{
    use WithPagination;

    public ?int $deletingId = null;

    public string $deletingTitle = '';

    #[Computed]
    public function pelatihans()
    {
        return PelatihanModel::with(['category', 'modules:id,pelatihan_id,title,urutan'])
            ->withCount('modules')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function categories()
    {
        return PelatihanCategory::orderBy('name')->get();
    }

    // --- Quick Edit ---
    public ?int $quickEditId = null;

    public string $qeTitle = '';

    public string $qeSlug = '';

    public ?int $qeCategoryId = null;

    public string $qeStatus = 'draft';

    public bool $qeIsActive = true;

    public function startQuickEdit(int $id): void
    {
        $pelatihan = PelatihanModel::findOrFail($id);

        $this->quickEditId = $pelatihan->id;
        $this->qeTitle = $pelatihan->title;
        $this->qeSlug = $pelatihan->slug;
        $this->qeCategoryId = $pelatihan->pelatihan_category_id;
        $this->qeStatus = $pelatihan->status->value;
        $this->qeIsActive = $pelatihan->is_active;

        $this->resetValidation();
    }

    public function cancelQuickEdit(): void
    {
        $this->reset(['quickEditId', 'qeTitle', 'qeSlug', 'qeCategoryId', 'qeStatus', 'qeIsActive']);
        $this->resetValidation();
    }

    public function saveQuickEdit(): void
    {
        if (! $this->quickEditId) {
            return;
        }

        $validated = $this->validate([
            'qeTitle' => ['required', 'string', 'max:255'],
            'qeSlug' => ['required', 'string', 'max:255', Rule::unique('pelatihans', 'slug')->ignore($this->quickEditId)],
            'qeCategoryId' => ['nullable', 'integer', 'exists:pelatihan_categories,id'],
            'qeStatus' => ['required', Rule::in(array_column(PelatihanStatus::cases(), 'value'))],
            'qeIsActive' => ['boolean'],
        ]);

        $pelatihan = PelatihanModel::findOrFail($this->quickEditId);
        $pelatihan->update([
            'title' => $validated['qeTitle'],
            'slug' => $validated['qeSlug'],
            'pelatihan_category_id' => $validated['qeCategoryId'],
            'status' => $validated['qeStatus'],
            'is_active' => $validated['qeIsActive'],
        ]);

        $this->cancelQuickEdit();
        unset($this->pelatihans);
        Flux::toast(variant: 'success', text: 'Pelatihan berhasil diperbarui.');
    }

    public function toggleActive(int $id): void
    {
        $pelatihan = PelatihanModel::findOrFail($id);
        $pelatihan->update(['is_active' => ! $pelatihan->is_active]);
        unset($this->pelatihans);
    }

    public function copy(int $id, CopyPelatihan $copyPelatihan): void
    {
        $pelatihan = PelatihanModel::findOrFail($id);
        $copy = $copyPelatihan->handle($pelatihan);
        unset($this->pelatihans);
        Flux::toast(variant: 'success', text: "Pelatihan berhasil disalin sebagai \"{$copy->title}\".");
    }

    public function confirmDelete(int $id): void
    {
        $pelatihan = PelatihanModel::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingTitle = $pelatihan->title;
        $this->modal('confirm-delete-pelatihan')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $pelatihan = PelatihanModel::findOrFail($this->deletingId);

        if ($pelatihan->thumbnail && ! str_starts_with($pelatihan->thumbnail, 'http')) {
            Storage::disk('public')->delete($pelatihan->thumbnail);
        }

        $pelatihan->delete();
        $this->deletingId = null;
        $this->deletingTitle = '';
        $this->resetPage();
        unset($this->pelatihans);
        $this->modal('confirm-delete-pelatihan')->close();
        Flux::toast(variant: 'success', text: 'Pelatihan berhasil dihapus.');
    }

    public function render(): View
    {
        return view('livewire.pelatihan');
    }
}
