<?php

use App\Models\Faq;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('FAQ')] class extends Component {
    public string $question = '';
    public string $answer = '';
    public bool $isActive = true;
    public ?int $editingId = null;
    public bool $showForm = false;
    public ?int $deletingId = null;
    public string $deletingQuestion = '';

    #[Computed]
    public function faqs()
    {
        return Faq::orderBy('order')->get();
    }

    public function save(): void
    {
        $this->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:2000'],
            'isActive' => ['boolean'],
        ]);

        if ($this->editingId) {
            Faq::findOrFail($this->editingId)->update([
                'question' => $this->question,
                'answer' => $this->answer,
                'is_active' => $this->isActive,
            ]);
            Flux::toast(variant: 'success', text: 'FAQ berhasil diperbarui.');
        } else {
            Faq::create([
                'question' => $this->question,
                'answer' => $this->answer,
                'is_active' => $this->isActive,
            ]);
            Flux::toast(variant: 'success', text: 'FAQ berhasil ditambahkan.');
        }

        $this->reset(['question', 'answer', 'isActive', 'editingId', 'showForm']);
        unset($this->faqs);
    }

    public function edit(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $this->editingId = $id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->isActive = $faq->is_active;
        $this->showForm = true;
    }

    public function openCreate(): void
    {
        $this->reset(['question', 'answer', 'editingId']);
        $this->isActive = true;
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['question', 'answer', 'isActive', 'editingId', 'showForm']);
    }

    public function toggleActive(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $faq->update(['is_active' => ! $faq->is_active]);
        unset($this->faqs);
    }

    public function moveUp(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $previous = Faq::where('order', '<', $faq->order)->orderByDesc('order')->first();

        if ($previous) {
            [$faq->order, $previous->order] = [$previous->order, $faq->order];
            $faq->save();
            $previous->save();
            unset($this->faqs);
        }
    }

    public function moveDown(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $next = Faq::where('order', '>', $faq->order)->orderBy('order')->first();

        if ($next) {
            [$faq->order, $next->order] = [$next->order, $faq->order];
            $faq->save();
            $next->save();
            unset($this->faqs);
        }
    }

    public function confirmDelete(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingQuestion = $faq->question;
        $this->modal('confirm-delete-faq')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        Faq::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->deletingQuestion = '';
        unset($this->faqs);
        $this->modal('confirm-delete-faq')->close();
        Flux::toast(variant: 'success', text: 'FAQ berhasil dihapus.');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
    <flux:heading size="xl" level="1">FAQ</flux:heading>

    <div class="flex items-center justify-between">
        <flux:text>Kelola pertanyaan yang sering diajukan pada halaman utama.</flux:text>
        @if (! $showForm)
            <flux:button wire:click="openCreate" variant="primary" icon="plus">
                Tambah FAQ
            </flux:button>
        @endif
    </div>

    @if ($showForm)
        <flux:card class="max-w-2xl">
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit FAQ' : 'Tambah FAQ' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:textarea
                    wire:model="question"
                    label="Pertanyaan"
                    placeholder="Tulis pertanyaan..."
                    rows="2"
                    required
                />

                <flux:textarea
                    wire:model="answer"
                    label="Jawaban"
                    placeholder="Tulis jawaban lengkap..."
                    rows="4"
                    required
                />

                <flux:switch wire:model="isActive" label="Tampilkan di halaman utama" />

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Perbarui' : 'Simpan' }}
                    </flux:button>
                    <flux:button wire:click="cancelForm" variant="ghost">Batal</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column class="w-8">#</flux:table.column>
            <flux:table.column>Pertanyaan</flux:table.column>
            <flux:table.column class="w-24">Status</flux:table.column>
            <flux:table.column class="w-32">Urutan</flux:table.column>
            <flux:table.column class="w-24"></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->faqs as $faq)
                <flux:table.row :key="$faq->id">
                    <flux:table.cell class="text-zinc-400 tabular-nums">{{ $loop->iteration }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium text-sm">{{ $faq->question }}</div>
                        <div class="text-xs text-zinc-400 mt-0.5 line-clamp-1">{{ $faq->answer }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <button wire:click="toggleActive({{ $faq->id }})" class="cursor-pointer">
                            @if ($faq->is_active)
                                <flux:badge color="lime" size="sm">Aktif</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Nonaktif</flux:badge>
                            @endif
                        </button>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-1">
                            <flux:button
                                wire:click="moveUp({{ $faq->id }})"
                                size="sm" variant="ghost" icon="chevron-up"
                                :disabled="$loop->first"
                            />
                            <flux:button
                                wire:click="moveDown({{ $faq->id }})"
                                size="sm" variant="ghost" icon="chevron-down"
                                :disabled="$loop->last"
                            />
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="edit({{ $faq->id }})" size="sm" variant="ghost" icon="pencil" />
                            <flux:button
                                wire:click="confirmDelete({{ $faq->id }})"
                                size="sm" variant="ghost" icon="trash"
                                class="text-red-500 hover:text-red-600"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="py-8 text-center text-zinc-500">
                        Belum ada FAQ. Tambahkan pertanyaan pertama.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="confirm-delete-faq" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus FAQ?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menghapus pertanyaan <strong>"{{ Str::limit($deletingQuestion, 60) }}"</strong>? Tindakan ini tidak dapat dibatalkan.
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
