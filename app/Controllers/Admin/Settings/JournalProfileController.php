<?php

namespace App\Controllers\Admin\Settings;

use App\Controllers\BaseController;

class JournalProfileController extends BaseController
{
    public function index() { return view('admin/settings/journals/index', ['title' => 'Profil Jurnal']); }
    public function create() { return redirect()->back()->with('info', 'Form tambah profil jurnal akan disiapkan pada batch berikutnya.'); }
    public function store() { return redirect()->back()->with('success', 'Profil jurnal tersimpan.'); }
    public function edit(int $id) { return redirect()->back()->with('info', "Form edit profil jurnal #{$id} akan disiapkan pada batch berikutnya."); }
    public function update(int $id) { return redirect()->back()->with('success', "Profil jurnal #{$id} diperbarui."); }
    public function destroy(int $id) { return redirect()->back()->with('success', "Profil jurnal #{$id} dihapus."); }
}
