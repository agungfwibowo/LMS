<?php

namespace App\Livewire\Actions;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pengguna')]
class UserApproval extends Component
{
    use WithPagination;

    public ?int $deletingId = null;

    public string $deletingName = '';

    #[Computed]
    public function users()
    {
        return User::orderByRaw('approved_at is null desc')
            ->orderBy('name')
            ->paginate(10);
    }

    public function approve(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->isApproved()) {
            return;
        }

        $user->approved_at = now();
        $user->save();

        unset($this->users);
        Flux::toast(variant: 'success', text: "Akun \"{$user->name}\" berhasil disetujui.");
    }

    public function confirmDelete(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->isApproved() || $user->id === auth()->id()) {
            return;
        }

        $this->deletingId = $id;
        $this->deletingName = $user->name;
        $this->modal('confirm-delete-user-approval')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $user = User::findOrFail($this->deletingId);

        // Hanya akun yang belum disetujui yang boleh ditolak/dihapus dari sini,
        // agar akun aktif (beserta kontennya) tidak terhapus tanpa sengaja.
        if ($user->isApproved() || $user->id === auth()->id()) {
            return;
        }

        $user->delete();
        $this->deletingId = null;
        $this->deletingName = '';
        $this->resetPage();
        unset($this->users);
        $this->modal('confirm-delete-user-approval')->close();
        Flux::toast(variant: 'success', text: 'Pendaftaran berhasil ditolak.');
    }

    public function render(): View
    {
        return view('livewire.user-approvals');
    }
}
