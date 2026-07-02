<?php

namespace App\Enums;

enum PelatihanCategoryIcon: string
{
    case AcademicCap = 'academic-cap';
    case Heart = 'heart';
    case ShieldCheck = 'shield-check';
    case ClipboardDocumentList = 'clipboard-document-list';
    case UserGroup = 'user-group';
    case Beaker = 'beaker';
    case ComputerDesktop = 'computer-desktop';
    case HandRaised = 'hand-raised';
    case BookOpen = 'book-open';
    case ChartBar = 'chart-bar';
    case DocumentText = 'document-text';
    case Briefcase = 'briefcase';

    public function label(): string
    {
        return match ($this) {
            PelatihanCategoryIcon::AcademicCap => 'Topi Akademik',
            PelatihanCategoryIcon::Heart => 'Hati (Klinis)',
            PelatihanCategoryIcon::ShieldCheck => 'Perisai (Keselamatan)',
            PelatihanCategoryIcon::ClipboardDocumentList => 'Papan Klip (Manajemen)',
            PelatihanCategoryIcon::UserGroup => 'Kelompok Orang',
            PelatihanCategoryIcon::Beaker => 'Tabung Lab',
            PelatihanCategoryIcon::ComputerDesktop => 'Komputer (TI)',
            PelatihanCategoryIcon::HandRaised => 'Tangan (Pelayanan)',
            PelatihanCategoryIcon::BookOpen => 'Buku Terbuka',
            PelatihanCategoryIcon::ChartBar => 'Grafik (Mutu)',
            PelatihanCategoryIcon::DocumentText => 'Dokumen',
            PelatihanCategoryIcon::Briefcase => 'Koper (Administrasi)',
        };
    }
}
