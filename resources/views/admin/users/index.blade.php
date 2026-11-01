@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna | WARNA BUDAYA')

@section('content')
    <div
        class="ua-config"
        data-user-config
        data-index-url="{{ route('admin.api.users.index') }}"
        data-store-url="{{ route('admin.api.users.store') }}"
        data-show-template="{{ route('admin.api.users.show', ['user' => '__id__']) }}"
        data-update-template="{{ route('admin.api.users.update', ['user' => '__id__']) }}"
        data-status-template="{{ route('admin.api.users.status', ['user' => '__id__']) }}"
        data-password-template="{{ route('admin.api.users.password', ['user' => '__id__']) }}"
        data-csrf-token="{{ csrf_token() }}"
    ></div>

    <section class="admin-page-heading ua-heading">
        <div>
            <p class="admin-eyebrow"><span></span> Akses khusus Super Admin</p>
            <h1>Manajemen <em>Pengguna.</em></h1>
            <p>Kelola akun pengelola dan kewenangannya. Akun Super Admin dilindungi dari perubahan role dan penonaktifan melalui halaman ini.</p>
        </div>
        <div class="admin-heading-stamp" aria-hidden="true">WB<small>AKSES<br>PENGELOLA</small></div>
    </section>

    <section class="ua-panel" aria-labelledby="ua-list-title">
        <div class="ua-panel-heading">
            <div>
                <p class="admin-eyebrow"><span></span> Direktori internal</p>
                <h2 id="ua-list-title">Akun pengelola <span class="ua-total" data-users-total>—</span></h2>
                <p>Data akun dimuat langsung dari layanan pengelolaan.</p>
            </div>
            <button type="button" class="ua-button ua-button-primary" data-user-create>＋ Tambah akun</button>
        </div>

        <form class="ua-filters" data-user-filter-form>
            <label class="ua-search">
                <span class="admin-sr-only">Cari nama atau email</span>
                <input type="search" name="search" maxlength="100" placeholder="Cari nama atau email…">
            </label>
            <label>
                <span class="admin-sr-only">Filter role</span>
                <select name="role">
                    <option value="">Semua role</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="verifikator">Verifikator</option>
                </select>
            </label>
            <label>
                <span class="admin-sr-only">Filter status</span>
                <select name="status">
                    <option value="">Semua status login</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak aktif</option>
                </select>
            </label>
            <button type="submit" class="ua-button ua-button-secondary">Terapkan</button>
            <button type="reset" class="ua-reset">Atur ulang</button>
        </form>

        <p class="ua-feedback" data-users-feedback role="status" aria-live="polite" hidden></p>
        <div class="ua-table-wrap">
            <table class="ua-table">
                <thead>
                    <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Bergabung</th><th><span class="admin-sr-only">Tindakan</span></th></tr>
                </thead>
                <tbody data-user-rows>
                    <tr><td colspan="6" class="ua-state"><strong>Memuat akun…</strong><small>Mohon tunggu sebentar.</small></td></tr>
                </tbody>
            </table>
        </div>
        <nav class="ua-pagination" data-user-pagination aria-label="Halaman akun pengelola"></nav>
    </section>

    <div class="ua-toast" data-user-toast role="status" aria-live="polite" hidden></div>

    <dialog class="ua-dialog" data-user-dialog>
        <form data-user-form>
            <div class="ua-dialog-heading">
                <div><p class="admin-eyebrow"><span></span> Akun pengelola</p><h2 data-user-form-title>Tambah akun</h2></div>
                <button type="button" class="ua-close" data-user-close aria-label="Tutup">×</button>
            </div>
            <div class="ua-form-grid">
                <label>Nama<input name="name" maxlength="255" required autocomplete="name"></label>
                <label>Email<input name="email" type="email" maxlength="255" required autocomplete="email"></label>
                <label>Role<select name="role" required><option value="admin">Admin</option><option value="verifikator">Verifikator</option></select></label>
                <div class="ua-password-fields" data-user-password-fields>
                    <label>Password<input name="password" type="password" minlength="12" autocomplete="new-password"></label>
                    <label>Konfirmasi password<input name="password_confirmation" type="password" minlength="12" autocomplete="new-password"></label>
                    <small>Password minimal 12 karakter.</small>
                </div>
            </div>
            <div class="ua-form-error" data-user-form-error role="alert" hidden></div>
            <div class="ua-dialog-actions"><button type="button" class="ua-button ua-button-secondary" data-user-close>Batal</button><button type="submit" class="ua-button ua-button-primary" data-user-submit>Simpan akun</button></div>
        </form>
    </dialog>

    <dialog class="ua-dialog ua-confirm-dialog" data-password-dialog>
        <form data-password-form>
            <div class="ua-dialog-heading">
                <div><p class="admin-eyebrow"><span></span> Keamanan akun</p><h2>Reset password</h2></div>
                <button type="button" class="ua-close" data-password-close aria-label="Tutup">×</button>
            </div>
            <p data-password-caption>Masukkan password baru untuk akun terpilih.</p>
            <div class="ua-form-grid">
                <label>Password baru<input name="password" type="password" minlength="12" required autocomplete="new-password"></label>
                <label>Konfirmasi password<input name="password_confirmation" type="password" minlength="12" required autocomplete="new-password"></label>
            </div>
            <div class="ua-form-error" data-password-error role="alert" hidden></div>
            <div class="ua-dialog-actions"><button type="button" class="ua-button ua-button-secondary" data-password-close>Batal</button><button type="submit" class="ua-button ua-button-primary">Reset password</button></div>
        </form>
    </dialog>
@endsection
