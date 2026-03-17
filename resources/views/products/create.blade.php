<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('✨ Create New Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl p-8">
                
                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Left hand-side: Basic Information --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="What is the product name..."
                                    class="w-full border-gray-200 dark:border-gray-700 rounded-xl p-3 text-sm focus:border-pink-500 focus:ring-pink-500 dark:bg-gray-900 dark:text-gray-100">
                                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea name="description" rows="4" placeholder="Brief description..."
                                    class="w-full border-gray-200 dark:border-gray-700 rounded-xl p-3 text-sm focus:border-pink-500 focus:ring-pink-500 dark:bg-gray-900 dark:text-gray-100">{{ old('description') }}</textarea>
                                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ราคา (฿)</label>
                                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0"
                                        class="w-full border-gray-200 dark:border-gray-700 rounded-xl p-3 text-sm focus:border-pink-500 focus:ring-pink-500 dark:bg-gray-900 dark:text-gray-100">
                                    @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock Amount</label>
                                    <input type="number" name="stock_number" value="{{ old('stock_number') }}" min="0"
                                        class="w-full border-gray-200 dark:border-gray-700 rounded-xl p-3 text-sm focus:border-pink-500 focus:ring-pink-500 dark:bg-gray-900 dark:text-gray-100">
                                    @error('stock_number') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Right hand-side: Image and Tag --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-xl hover:border-pink-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 00-4 4H12a4 4 0 00-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 005.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                            <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-pink-600 hover:text-pink-500 focus-within:outline-none">
                                                <span>Upload a file</span>
                                                <input id="file-upload" name="image" type="file" class="sr-only" accept="image/*">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                                    </div>
                                </div>
                                @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Tags Selector --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Choose related tags</label>
                                <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto p-2 border border-gray-100 dark:border-gray-700 rounded-xl shadow-inner">
                                    @foreach($tags as $tag)
                                        <label class="relative inline-flex items-center">
                                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only peer"
                                                {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                                            <div class="px-3 py-1 text-xs font-semibold rounded-full border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 cursor-pointer 
                                                peer-checked:bg-pink-100 peer-checked:text-pink-600 peer-checked:border-pink-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                                #{{ $tag->name }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('tags') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-700">

                    {{-- Button --}}
                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.products') }}" class="text-sm text-gray-500 hover:text-pink-500 flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-3 bg-pink-500 text-white font-bold rounded-xl shadow-lg shadow-pink-200 hover:bg-pink-600 hover:shadow-pink-300 transform hover:-translate-y-0.5 transition-all">
                            ✨ Save new product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('file-upload').onchange = evt => {
        const [file] = document.getElementById('file-upload').files
        if (file) {
            // สร้าง Element สำหรับโชว์รูป (ถ้ายังไม่มี) หรือเปลี่ยนรูปเดิม
            let preview = document.getElementById('preview-img');
            if(!preview) {
                preview = document.createElement('img');
                preview.id = 'preview-img';
                preview.className = 'mt-4 mx-auto h-40 rounded-lg shadow-md';
                document.querySelector('.border-dashed').after(preview);
            }
            preview.src = URL.createObjectURL(file)
        }
    }
</script>
</x-app-layout>