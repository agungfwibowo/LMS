<?php

namespace App\Livewire\Actions;

use App\Enums\PelatihanCategoryIcon;
use App\Models\PelatihanCategory as PelatihanCategoryModel;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Kategori Pelatihan')]
class PelatihanCategory extends Component
{
    use WithPagination;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public string $icon = 'academic-cap';

    public ?int $editingId = null;

    public bool $showForm = false;

    /**
     * Referensi form di URL: '' (tertutup), 'create', atau id untuk edit.
     */
    #[Url(as: 'form', except: '')]
    public string $form = '';

    public ?int $deletingId = null;

    public string $deletingName = '';

    public function mount(): void
    {
        if ($this->form === 'create') {
            $this->openCreate();

            return;
        }

        if (is_numeric($this->form)) {
            $category = PelatihanCategoryModel::find((int) $this->form);

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
        return PelatihanCategoryModel::withCount('pelatihans')->orderBy('name')->paginate(10);
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
            'slug' => ['required', 'string', 'max:255', Rule::unique('pelatihan_categories', 'slug')->ignore($this->editingId)],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['required', Rule::enum(PelatihanCategoryIcon::class)],
        ]);

        if ($this->editingId) {
            PelatihanCategoryModel::findOrFail($this->editingId)->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'icon' => $this->icon,
            ]);
            Flux::toast(variant: 'success', text: 'Kategori pelatihan berhasil diperbarui.');
        } else {
            PelatihanCategoryModel::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'icon' => $this->icon,
            ]);
            Flux::toast(variant: 'success', text: 'Kategori pelatihan berhasil ditambahkan.');
        }

        $this->reset(['name', 'slug', 'description', 'editingId', 'showForm', 'form']);
        $this->icon = 'academic-cap';
        $this->resetPage();
        unset($this->categories);
    }

    public function edit(int $id): void
    {
        $category = PelatihanCategoryModel::findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
        $this->icon = $category->icon?->value ?? 'academic-cap';
        $this->form = (string) $id;
        $this->showForm = true;
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'slug', 'description', 'editingId']);
        $this->icon = 'academic-cap';
        $this->form = 'create';
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['name', 'slug', 'description', 'editingId', 'showForm', 'form']);
        $this->icon = 'academic-cap';
    }

    public function copy(int $id): void
    {
        $category = PelatihanCategoryModel::findOrFail($id);

        PelatihanCategoryModel::create([
            'name' => $category->name.' (Salinan)',
            'slug' => $this->uniqueSlug($category->slug),
            'description' => $category->description,
            'icon' => $category->icon,
        ]);

        $this->resetPage();
        unset($this->categories);
        Flux::toast(variant: 'success', text: 'Kategori pelatihan berhasil disalin.');
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug.'-salinan';
        $counter = 2;

        while (PelatihanCategoryModel::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-salinan-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function confirmDelete(int $id): void
    {
        $category = PelatihanCategoryModel::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingName = $category->name;
        $this->modal('confirm-delete-pelatihan-category')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        PelatihanCategoryModel::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->deletingName = '';
        $this->resetPage();
        unset($this->categories);
        $this->modal('confirm-delete-pelatihan-category')->close();
        Flux::toast(variant: 'success', text: 'Kategori pelatihan berhasil dihapus.');
    }

    public function render(): View
    {
        return view('livewire.pelatihan-categories');
    }
}
