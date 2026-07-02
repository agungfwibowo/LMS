<?php

namespace App\Livewire\Actions;

use App\Models\Tag as TagModel;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Tags')]
class Tag extends Component
{
    use WithPagination;

    public string $name = '';

    public string $slug = '';

    public ?int $editingId = null;

    public bool $showForm = false;

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
            $tag = TagModel::find((int) $this->form);

            if ($tag) {
                $this->edit($tag->id);

                return;
            }
        }

        $this->form = '';
    }

    #[Computed]
    public function tags()
    {
        return TagModel::withCount('posts')->orderBy('name')->paginate(10);
    }

    public function updatedName(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('tags', 'slug')->ignore($this->editingId)],
        ]);

        if ($this->editingId) {
            TagModel::findOrFail($this->editingId)->update([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
            Flux::toast(variant: 'success', text: 'Tag berhasil diperbarui.');
        } else {
            TagModel::create([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
            Flux::toast(variant: 'success', text: 'Tag berhasil ditambahkan.');
        }

        $this->reset(['name', 'slug', 'editingId', 'showForm', 'form']);
        $this->resetPage();
        unset($this->tags);
    }

    public function edit(int $id): void
    {
        $tag = TagModel::findOrFail($id);
        $this->editingId = $id;
        $this->name = $tag->name;
        $this->slug = $tag->slug;
        $this->form = (string) $id;
        $this->showForm = true;
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'slug', 'editingId']);
        $this->form = 'create';
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['name', 'slug', 'editingId', 'showForm', 'form']);
    }

    public ?int $deletingId = null;

    public string $deletingName = '';

    public function copy(int $id): void
    {
        $tag = TagModel::findOrFail($id);

        TagModel::create([
            'name' => $tag->name.' (Salinan)',
            'slug' => $this->uniqueSlug($tag->slug),
        ]);

        $this->resetPage();
        unset($this->tags);
        Flux::toast(variant: 'success', text: 'Tag berhasil disalin.');
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug.'-salinan';
        $counter = 2;

        while (TagModel::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-salinan-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function confirmDelete(int $id): void
    {
        $tag = TagModel::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingName = $tag->name;
        $this->modal('confirm-delete-tag')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        TagModel::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->deletingName = '';
        $this->resetPage();
        unset($this->tags);
        $this->modal('confirm-delete-tag')->close();
        Flux::toast(variant: 'success', text: 'Tag berhasil dihapus.');
    }

    public function render(): View
    {
        return view('livewire.tags');
    }
}
