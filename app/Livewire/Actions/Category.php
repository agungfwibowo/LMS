<?php

namespace App\Livewire\Actions;

use App\Models\Category as CategoryModel;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Kategori')]
class Category extends Component
{
    use WithPagination;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public ?int $editingId = null;

    /**
     * Referensi form di URL: '' (tertutup), 'create', atau id untuk edit.
     */
    #[Url(as: 'form', except: '')]
    public string $form = '';

    public bool $showForm = false;

    public ?int $deletingId = null;

    public string $deletingName = '';

    public function mount(): void
    {
        if ($this->form === 'create') {
            $this->openCreate();

            return;
        }

        if (is_numeric($this->form)) {
            $category = CategoryModel::find((int) $this->form);

            if ($category) {
                $this->edit($category->id);

                return;
            }
        }

        $this->form = '';
    }

    #[Computed]
    public function categories()
    {
        return CategoryModel::withCount('posts')->orderBy('name')->paginate(10);
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
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($this->editingId)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($this->editingId) {
            CategoryModel::findOrFail($this->editingId)->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);
            Flux::toast(variant: 'success', text: 'Kategori berhasil diperbarui.');
        } else {
            CategoryModel::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);
            Flux::toast(variant: 'success', text: 'Kategori berhasil ditambahkan.');
        }

        $this->reset(['name', 'slug', 'description', 'editingId', 'showForm', 'form']);
        $this->resetPage();
        unset($this->categories);
    }

    public function edit(int $id): void
    {
        $category = CategoryModel::findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
        $this->form = (string) $id;
        $this->showForm = true;
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'slug', 'description', 'editingId']);
        $this->form = 'create';
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['name', 'slug', 'description', 'editingId', 'showForm', 'form']);
    }

    public function copy(int $id): void
    {
        $category = CategoryModel::findOrFail($id);

        CategoryModel::create([
            'name' => $category->name.' (Salinan)',
            'slug' => $this->uniqueSlug($category->slug),
            'description' => $category->description,
        ]);

        $this->resetPage();
        unset($this->categories);
        Flux::toast(variant: 'success', text: 'Kategori berhasil disalin.');
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug.'-salinan';
        $counter = 2;

        while (CategoryModel::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-salinan-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function confirmDelete(int $id): void
    {
        $category = CategoryModel::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingName = $category->name;
        $this->modal('confirm-delete-category')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        CategoryModel::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->deletingName = '';
        $this->resetPage();
        unset($this->categories);
        $this->modal('confirm-delete-category')->close();
        Flux::toast(variant: 'success', text: 'Kategori berhasil dihapus.');
    }

    public function render(): View
    {
        return view('livewire.categories');
    }
}
