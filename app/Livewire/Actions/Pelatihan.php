<?php

namespace App\Livewire\Actions;

use App\Actions\Pelatihan\CopyPelatihan;
use App\Models\Pelatihan as PelatihanModel;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
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
