@extends('layouts.sidebar')

@section('title', 'Edit Brand')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('brands.index') }}" class="text-gray-500 hover:text-gray-700">
                            Brands
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('brands.show', $brand) }}" class="ml-4 text-gray-500 hover:text-gray-700">
                                {{ $brand->brand_name }}
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-900">Edit</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="mt-4">
                <h1 class="text-3xl font-bold text-gray-900">Edit Brand</h1>
                <p class="text-gray-600">Update brand information</p>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('brands.update', $brand) }}" enctype="multipart/form-data" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <!-- Current Photo -->
                @if($brand->brand_photo && $brand->brand_photo !== 'default.jpg')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Photo</label>
                        <div class="flex items-center space-x-4">
                            <img class="h-20 w-auto rounded-lg border border-gray-200"
                                 src="{{ Storage::url('brands/' . $brand->brand_photo) }}"
                                 alt="{{ $brand->brand_name }}">
                            <div>
                                <p class="text-sm text-gray-600">Current brand photo</p>
                                <p class="text-xs text-gray-500">Upload a new file to replace it</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Brand Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $brand->brand_photo && $brand->brand_photo !== 'default.jpg' ? 'New Brand Photo' : 'Brand Photo' }}
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <div id="image-preview" class="hidden">
                                <img id="preview-img" class="mx-auto h-32 w-auto rounded-lg" src="#" alt="Preview">
                            </div>
                            <div id="upload-placeholder">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                            <input id="brand_photo" name="brand_photo" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                        </div>
                    </div>
                    @error('brand_photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Brand Code (Read Only) -->
                <div>
                    <label for="brand_code" class="block text-sm font-medium text-gray-700">Brand Code</label>
                    <input type="text"
                           name="brand_code"
                           id="brand_code"
                           value="{{ $brand->brand_code }}"
                           readonly
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 text-gray-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Brand code cannot be changed</p>
                </div>

                <!-- Brand Name -->
                <div>
                    <label for="brand_name" class="block text-sm font-medium text-gray-700">Brand Name *</label>
                    <input type="text"
                           name="brand_name"
                           id="brand_name"
                           value="{{ old('brand_name', $brand->brand_name) }}"
                           required
                           maxlength="30"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('brand_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('brands.show', $brand) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Update Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('upload-placeholder').classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
