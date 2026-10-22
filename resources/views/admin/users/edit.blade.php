@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')
    <div class="p-gutter flex flex-col gap-stack_lg">
        <div class="flex items-center justify-between">
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Edit User</h1>
            <a href="{{ route('admin.users.index') }}"
                class="px-4 py-2 rounded-lg bg-surface-container-high text-on-surface hover:bg-surface-container-highest transition-colors font-semibold">
                Kembali
            </a>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container-high p-8 max-w-2xl">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block font-label-md text-label-md font-semibold text-on-surface mb-1">Full
                        Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-3 py-2 rounded-lg bg-surface-container-low border-none focus:ring-2 focus:ring-primary text-body-sm font-body-sm @error('name') ring-2 ring-error @enderror"
                        required>
                    @error('name') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block font-label-md text-label-md font-semibold text-on-surface mb-1">Email
                        Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-3 py-2 rounded-lg bg-surface-container-low border-none focus:ring-2 focus:ring-primary text-body-sm font-body-sm @error('email') ring-2 ring-error @enderror"
                        required>
                    @error('email') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password"
                        class="block font-label-md text-label-md font-semibold text-on-surface mb-1">Password (Kosongkan
                        jika tidak ingin diubah)</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-3 py-2 rounded-lg bg-surface-container-low border-none focus:ring-2 focus:ring-primary text-body-sm font-body-sm @error('password') ring-2 ring-error @enderror">
                    @error('password') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="role"
                        class="block font-label-md text-label-md font-semibold text-on-surface mb-1">Role</label>
                    <select name="role" id="role"
                        class="w-full px-3 py-2 rounded-lg bg-surface-container-low border-none focus:ring-2 focus:ring-primary text-body-sm font-body-sm @error('role') ring-2 ring-error @enderror"
                        required>
                        <option value="front_desk" {{ old('role', $user->role) == 'front_desk' ? 'selected' : '' }}>Front Desk
                        </option>
                        <option value="staf_bidang" {{ old('role', $user->role) == 'staf_bidang' ? 'selected' : '' }}>Staf
                            Bidang</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator
                        </option>
                    </select>
                    @error('role') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="px-6 py-2 rounded-lg bg-primary text-on-primary font-semibold hover:bg-primary-hover transition-colors">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection