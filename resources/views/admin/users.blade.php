<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Manage Users
        </h2>
    </x-slot>
    <div class="mb-8 p-6 bg-white rounded-2xl shadow-sm border border-pink-50">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <span>➕</span> เพิ่ม Admin ใหม่
    </h3>
    <form action="{{ route('admin.users.storeAdmin') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @csrf
        <div>
            <input type="text" name="name" placeholder="ชื่อ-นามสกุล" required
                class="w-full border-gray-200 rounded-xl p-2.5 text-sm focus:ring-pink-500 focus:border-pink-500">
        </div>
        <div>
            <input type="email" name="email" placeholder="อีเมล (Email)" required
                class="w-full border-gray-200 rounded-xl p-2.5 text-sm focus:ring-pink-500 focus:border-pink-500">
        </div>
        <div>
            <input type="password" name="password" placeholder="รหัสผ่าน" required
                class="w-full border-gray-200 rounded-xl p-2.5 text-sm focus:ring-pink-500 focus:border-pink-500">
        </div>
        <button type="submit" 
            class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2.5 px-4 rounded-xl transition shadow-lg shadow-pink-100">
            สร้าง Admin
        </button>
    </form>
</div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">ID</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Role</th>
                            <th class="px-6 py-3 text-left">Joined</th>
                            <th class="px-6 py-3 text-left">Actions</th> </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
    @foreach($users as $user)
    <tr class="hover:bg-gray-50">
        {{-- 1. คอลัมน์ ID --}}
        <td class="px-6 py-4 text-gray-400 text-sm">{{ $user->id }}</td>

        {{-- 2. คอลัมน์ Name --}}
        <td class="px-6 py-4 font-medium text-gray-800 text-sm">{{ $user->name }}</td>

        {{-- 3. คอลัมน์ Email --}}
        <td class="px-6 py-4 text-gray-500 text-sm">{{ $user->email }}</td>

        {{-- 4. คอลัมน์ Role --}}
        <td class="px-6 py-4">
            <span @class([
                'px-2 py-1 rounded-full text-[10px] font-bold uppercase',
                'bg-red-100 text-red-700'   => $user->role === 'admin',
                'bg-blue-100 text-blue-700' => $user->role === 'customer',
            ])>
                {{ $user->role }}
            </span>
        </td>

        {{-- 5. คอลัมน์ Joined --}}
        <td class="px-6 py-4 text-gray-400 text-sm">{{ $user->created_at->format('d/m/Y') }}</td>

        {{-- 6. คอลัมน์ Actions (ปุ่มกด) --}}
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                @if($user->id !== Auth::id())
                    <form method="POST" action="{{ route('admin.users.updateRole', $user->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-3 py-1 bg-amber-100 text-amber-600 rounded-lg text-xs font-bold hover:bg-amber-200 transition">
                            Change Role
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('แน่ใจนะว่าจะลบ?')" 
                                class="px-3 py-1 bg-red-100 text-red-600 rounded-lg text-xs font-bold hover:bg-red-200 transition">
                            Delete
                        </button>
                    </form>
                @else
                    <span class="text-xs text-gray-300 italic">Current Admin</span>
                @endif
            </div>
        </td>
    </tr>
    @endforeach
</tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>