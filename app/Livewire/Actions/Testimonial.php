<?php

namespace App\Livewire\Actions;

use App\Models\Testimonial as TestimonialModel;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Testimoni')]
class Testimonial extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $role = '';

    public string $quote = '';

    public string $avatarColor = 'brand';

    public float $rating = 5.0;

    public bool $isActive = true;

    // Photo fields
    public string $photoSource = 'initials'; // initials | upload | external

    public mixed $uploadedPhoto = null;

    public string $photoUrl = '';

    public ?string $existingPhoto = null;

    public ?int $editingId = null;

    public bool $showForm = false;

    public ?int $deletingId = null;

    public string $deletingName = '';

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
            $testimonial = TestimonialModel::find((int) $this->form);

            if ($testimonial) {
                $this->edit($testimonial->id);

                return;
            }
        }

        $this->form = '';
    }

    #[Computed]
    public function testimonials()
    {
        return TestimonialModel::latest()->get();
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'quote' => ['required', 'string', 'max:1000'],
            'avatarColor' => ['required', 'in:brand,lime'],
            'rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'isActive' => ['boolean'],
            'photoSource' => ['required', 'in:initials,upload,external'],
        ];

        if ($this->photoSource === 'upload') {
            $rules['uploadedPhoto'] = ['nullable', 'image', 'max:2048'];
        }

        if ($this->photoSource === 'external') {
            $rules['photoUrl'] = ['nullable', 'url', 'max:500'];
        }

        $this->validate($rules);

        $photo = $this->resolvePhoto();

        $data = [
            'name' => $this->name,
            'role' => $this->role,
            'quote' => $this->quote,
            'avatar_color' => $this->avatarColor,
            'rating' => $this->rating,
            'is_active' => $this->isActive,
            'photo' => $photo,
        ];

        if ($this->editingId) {
            $testimonial = TestimonialModel::findOrFail($this->editingId);

            // Delete old uploaded file if replacing with new one
            if ($photo !== $this->existingPhoto && $this->existingPhoto && ! str_starts_with($this->existingPhoto, 'http')) {
                Storage::disk('public')->delete($this->existingPhoto);
            }

            $testimonial->update($data);
            Flux::toast(variant: 'success', text: 'Testimoni berhasil diperbarui.');
        } else {
            TestimonialModel::create($data);
            Flux::toast(variant: 'success', text: 'Testimoni berhasil ditambahkan.');
        }

        $this->resetForm();
        unset($this->testimonials);
    }

    private function resolvePhoto(): ?string
    {
        if ($this->photoSource === 'initials') {
            return null;
        }

        if ($this->photoSource === 'external') {
            return $this->photoUrl ?: null;
        }

        // upload
        if ($this->uploadedPhoto) {
            return $this->uploadedPhoto->store('uploads/testimonials', 'public');
        }

        return $this->existingPhoto;
    }

    public function edit(int $id): void
    {
        $testimonial = TestimonialModel::findOrFail($id);
        $this->editingId = $id;
        $this->name = $testimonial->name;
        $this->role = $testimonial->role;
        $this->quote = $testimonial->quote;
        $this->avatarColor = $testimonial->avatar_color;
        $this->rating = (float) $testimonial->rating;
        $this->isActive = $testimonial->is_active;
        $this->existingPhoto = $testimonial->photo;

        if (! $testimonial->photo) {
            $this->photoSource = 'initials';
        } elseif (str_starts_with($testimonial->photo, 'http')) {
            $this->photoSource = 'external';
            $this->photoUrl = $testimonial->photo;
        } else {
            $this->photoSource = 'upload';
        }

        $this->form = (string) $id;
        $this->showForm = true;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->form = 'create';
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'role', 'quote', 'editingId', 'showForm', 'form', 'uploadedPhoto', 'photoUrl', 'existingPhoto']);
        $this->avatarColor = 'brand';
        $this->rating = 5.0;
        $this->isActive = true;
        $this->photoSource = 'initials';
    }

    public function toggleActive(int $id): void
    {
        $testimonial = TestimonialModel::findOrFail($id);
        $testimonial->update(['is_active' => ! $testimonial->is_active]);
        unset($this->testimonials);
    }

    public function copy(int $id): void
    {
        $testimonial = TestimonialModel::findOrFail($id);

        TestimonialModel::create([
            'name' => $testimonial->name.' (Salinan)',
            'role' => $testimonial->role,
            'quote' => $testimonial->quote,
            'avatar_color' => $testimonial->avatar_color,
            'rating' => $testimonial->rating,
            'is_active' => false,
            'photo' => $this->copyPhoto($testimonial->photo),
        ]);

        unset($this->testimonials);
        Flux::toast(variant: 'success', text: 'Testimoni berhasil disalin.');
    }

    private function copyPhoto(?string $path): ?string
    {
        if (! $path || str_starts_with($path, 'http')) {
            return $path;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return null;
        }

        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $newPath = $directory.'/'.uniqid('copy_').($extension ? '.'.$extension : '');

        $disk->copy($path, $newPath);

        return $newPath;
    }

    public function confirmDelete(int $id): void
    {
        $testimonial = TestimonialModel::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingName = $testimonial->name;
        $this->modal('confirm-delete-testimonial')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $testimonial = TestimonialModel::findOrFail($this->deletingId);

        if ($testimonial->photo && ! str_starts_with($testimonial->photo, 'http')) {
            Storage::disk('public')->delete($testimonial->photo);
        }

        $testimonial->delete();
        $this->deletingId = null;
        $this->deletingName = '';
        unset($this->testimonials);
        $this->modal('confirm-delete-testimonial')->close();
        Flux::toast(variant: 'success', text: 'Testimoni berhasil dihapus.');
    }

    public function render(): View
    {
        return view('livewire.testimonials');
    }
}
