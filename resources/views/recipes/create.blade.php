<x-layout>
    <title>Create Recipe</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
<div class="recipe-create-page">

    <div class="create-container">

        <h1>Create Your Own Recipe</h1>

        <form method="POST" action="/recipes" enctype="multipart/form-data" class="recipe-form">

            @csrf

            <!-- TITLE -->
            <div class="form-group">
                <label for="title">Recipe Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    placeholder="Enter recipe name"
                    value="{{ old('title') }}"
                    required
                >
                @error('title')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- IMAGE UPLOAD -->
            <div class="form-group">
                <label for="image">Recipe Image</label>
                <div class="image-upload">
                    <input 
                        type="file" 
                        id="image" 
                        name="image" 
                        accept="image/*"
                        required
                    >
                    <div id="image-preview" class="image-preview"></div>
                </div>
                @error('image')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- INGREDIENTS -->
            <div class="form-group ingredients-group">
                <label>Select Ingredients</label>
                <div class="ingredients-selector">
                    @foreach($categories as $category => $items)
                        <div class="ingredient-category">
                            <h3>{{ $category }}</h3>
                            <div class="ingredient-checkboxes">
                                @foreach($items as $ingredient)
                                    <label class="checkbox-item">
                                        <input 
                                            type="checkbox" 
                                            name="ingredients[]" 
                                            value="{{ $ingredient }}"
                                        >
                                        <span>{{ $ingredient }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('ingredients')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- CUSTOM INGREDIENTS -->
            <div class="form-group">
                <label for="custom_ingredients">Add Custom Ingredients (comma-separated)</label>
                <input 
                    type="text" 
                    id="custom_ingredients" 
                    name="custom_ingredients" 
                    placeholder="e.g., vanilla extract, soy sauce, coconut milk"
                    value="{{ old('custom_ingredients') }}"
                >
            </div>

            <!-- COOK TIME -->
            <div class="form-group">
                <label for="cook_time">Cook Time (minutes)</label>
                <input 
                    type="number" 
                    id="cook_time" 
                    name="cook_time" 
                    min="1"
                    placeholder="e.g., 30"
                    value="{{ old('cook_time') }}"
                    required
                >
                @error('cook_time')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- INSTRUCTIONS -->
            <div class="form-group">
                <label for="instructions">Instructions</label>
                <textarea 
                    id="instructions" 
                    name="instructions" 
                    placeholder="Enter step-by-step instructions for your recipe..."
                    rows="8"
                    required
                >{{ old('instructions') }}</textarea>
                @error('instructions')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- BUTTONS -->
            <div class="form-actions">
                <a href="/" class="cancel-btn">Cancel</a>
                <button type="submit" class="submit-btn">Create Recipe</button>
            </div>

        </form>

    </div>

</div>

<script>
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('image-preview').innerHTML = `<img src="${event.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

</x-layout>
