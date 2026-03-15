<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Manage Users
        </h2>
    </x-slot>

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
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                        <td class="px-6 py-4 flex space-x-2">
    {{-- ปุ่ม Change Role: ให้แก้ได้ทุกคน "ยกเว้นตัวเอง" --}}
    @if($user->id !== Auth::id())
        <form method="POST" action="{{ route('admin.users.updateRole', $user->id) }}">
            @csrf
            @method('PATCH')
            <button type="submit" 
                class="px-3 py-1 bg-amber-500 text-white rounded-lg text-xs hover:bg-amber-600 transition">
                Change Role
            </button>
        </form>
    @else
        <span class="text-xs text-gray-400 italic">You (Current)</span>
    @endif

    {{-- ปุ่ม Delete: ให้ลบได้เฉพาะคนที่ไม่ใช่ Admin และไม่ใช่ตัวเอง --}}
    @if($user->role !== 'admin' && $user->id !== Auth::id())
        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                onclick="return confirm('Are you sure?')"
                class="px-3 py-1 bg-red-500 text-white rounded-lg text-xs hover:bg-red-600 transition">
                Delete
            </button>
        </form>
    @endif
</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>