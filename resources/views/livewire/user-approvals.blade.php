<div class="flex h-full w-full flex-1 flex-col gap-4">
    <flux:heading size="xl" level="1">Pengguna</flux:heading>

    <div class="flex items-center justify-between">
        <flux:text>Setujui atau tolak pendaftaran akun baru. Akun yang belum disetujui tidak dapat mengakses panel admin.</flux:text>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column class="w-8">#</flux:table.column>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column class="w-28">Status</flux:table.column>
            <flux:table.column class="w-36">Terdaftar</flux:table.column>
            <flux:table.column class="w-32"></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell class="text-zinc-400 tabular-nums">{{ $loop->iteration }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:avatar size="xs" :name="$user->name" :initials="$user->initials()" />
                            <span class="text-sm font-medium">{{ $user->name }}</span>
                            @if ($user->id === auth()->id())
                                <flux:badge color="zinc" size="sm">Anda</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="text-sm text-zinc-500">{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-1">
                            <flux:badge :color="$user->role->color()" size="sm">{{ $user->role->label() }}</flux:badge>
                            @if ($user->isApproved())
                                <flux:badge color="lime" size="sm">Disetujui</flux:badge>
                            @else
                                <flux:badge color="amber" size="sm">Menunggu</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="text-sm text-zinc-500">
                        {{ $user->created_at?->translatedFormat('d M Y H:i') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @unless ($user->isApproved())
                            <div class="flex items-center gap-2">
                                <flux:button
                                    wire:click="approve({{ $user->id }})"
                                    size="sm" variant="primary" icon="check"
                                >
                                    Setujui
                                </flux:button>
                                <flux:button
                                    wire:click="confirmDelete({{ $user->id }})"
                                    size="sm" variant="ghost" icon="trash"
                                    class="text-red-500 hover:text-red-600"
                                    tooltip="Tolak pendaftaran"
                                />
                            </div>
                        @endunless
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="py-8 text-center text-zinc-500">
                        Belum ada pengguna terdaftar.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$this->users" />

    <flux:modal name="confirm-delete-user-approval" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tolak pendaftaran?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menolak pendaftaran <strong>"{{ $deletingName }}"</strong>? Akun akan dihapus dan tindakan ini tidak dapat dibatalkan.
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger">Tolak &amp; Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
