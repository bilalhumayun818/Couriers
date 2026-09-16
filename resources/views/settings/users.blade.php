@extends('demo.layout')
@section('title', 'Users & Roles')
@section('page-title', 'System Settings — Users & Roles')
@section('page-subtitle', 'Manage access to your courier workspace')

@section('content')
@if(session('success'))
<div role="status" class="card p-4 mb-4 text-sky-300">{{ session('success') }}</div>
@endif
<div class="card overflow-hidden">
    <div class="p-5 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="font-semibold text-slate-100">User Accounts <span class="text-sky-400">({{ $users->total() }})</span></h2>
            <p class="text-xs text-slate-400 mt-1">Users can access business pages. Admins can also view and create user accounts.</p>
        </div>
        <button type="button" class="btn-primary" onclick="openUserModal()">+ Add User</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="table-header">
                <th class="px-5 py-3 text-left">Name</th><th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-left">Role</th><th class="px-5 py-3 text-left">Created</th>
            </tr></thead>
            <tbody>
            @foreach($users as $user)
                <tr class="table-row">
                    <td class="px-5 py-3 font-semibold text-slate-100">{{ $user->name }} @if($user->is(auth()->user()))<span class="text-xs text-sky-400">(you)</span>@endif</td>
                    <td class="px-5 py-3 text-slate-300">{{ $user->email }}</td>
                    <td class="px-5 py-3"><span class="badge-blue">{{ $user->role }}</span></td>
                    <td class="px-5 py-3 text-slate-400">{{ $user->created_at?->format('d M Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-5">{{ $users->links() }}</div>
</div>
@endsection

@section('modals')
<dialog id="userModal" class="modal-box text-slate-100 w-full max-w-md p-0" style="max-height:90dvh;overflow-y:auto;">
    <form method="POST" action="{{ route('settings.users.store') }}">
        @csrf
        <div class="p-5 border-b border-slate-700 flex justify-between items-center">
            <h2 class="font-semibold">Add New User</h2>
            <button type="button" class="text-slate-400" aria-label="Close" onclick="document.getElementById('userModal').close()">✕</button>
        </div>
        <div class="p-5 space-y-4">
            @if($errors->any())
            <div role="alert" class="rounded-lg border border-sky-400/30 bg-sky-400/10 p-3 text-sm text-sky-300">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif
            <div><label for="user_name" class="block text-xs mb-2">Full name</label><input id="user_name" name="name" value="{{ old('name') }}" type="text" required maxlength="255" autocomplete="name"></div>
            <div><label for="user_email" class="block text-xs mb-2">Email address</label><input id="user_email" name="email" value="{{ old('email') }}" type="email" required maxlength="255" autocomplete="email"></div>
            <div><label for="user_role" class="block text-xs mb-2">Role</label><select id="user_role" name="role" required>
                <option value="User" @selected(old('role', 'User') === 'User')>User</option>
                <option value="Admin" @selected(old('role') === 'Admin')>Admin</option>
            </select></div>
            <div><label for="user_password" class="block text-xs mb-2">Password (at least 8 characters)</label><input id="user_password" name="password" type="password" class="glass-input w-full p-3" required minlength="8" maxlength="255" autocomplete="new-password"></div>
            <div><label for="user_password_confirmation" class="block text-xs mb-2">Confirm password</label><input id="user_password_confirmation" name="password_confirmation" type="password" class="glass-input w-full p-3" required minlength="8" maxlength="255" autocomplete="new-password"></div>
        </div>
        <div class="p-5 border-t border-slate-700 flex justify-end gap-2">
            <button type="button" class="btn-ghost" onclick="document.getElementById('userModal').close()">Cancel</button>
            <button type="submit" class="btn-primary">Create User</button>
        </div>
    </form>
</dialog>
@endsection

@section('scripts')
<script>
function openUserModal() { document.getElementById('userModal').showModal(); }
@if($errors->any()) openUserModal(); @endif
</script>
@endsection
