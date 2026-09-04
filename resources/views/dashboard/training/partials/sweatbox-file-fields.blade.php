@php $editingSweatboxFile = isset($sweatboxFile) && $sweatboxFile; @endphp
<div class="form-group">
    <label class="font-weight-bold small">Position</label>
    <select name="position" class="form-control" required>
        @foreach(['CYWG_GND', 'CYWG_TWR', 'CYWG_TML'] as $position)
            <option value="{{ $position }}" @selected(old('position', $editingSweatboxFile ? $sweatboxFile->position : '') === $position)>{{ $position }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label class="font-weight-bold small">Display Name</label>
    <input type="text" name="name" class="form-control" maxlength="255" value="{{ old('name', $editingSweatboxFile ? $sweatboxFile->name : '') }}" required>
</div>
<div class="form-group">
    <label class="font-weight-bold small">Description</label>
    <textarea name="description" class="form-control" rows="2" maxlength="500" required>{{ old('description', $editingSweatboxFile ? $sweatboxFile->description : '') }}</textarea>
</div>
<div class="form-group">
    <label class="font-weight-bold small">File Uploader Link</label>
    <input type="text" name="file_url" class="form-control" maxlength="2048" placeholder="/storage/files/uploads/1234567890.txt" value="{{ old('file_url', $editingSweatboxFile ? $sweatboxFile->file_url : '') }}" required>
    <small class="form-text text-muted">Paste the complete link returned by File Uploader.</small>
</div>
<div class="form-row">
    <div class="form-group col-sm-8">
        <label class="font-weight-bold small">Updated Date</label>
        <input type="date" name="updated_on" class="form-control" value="{{ old('updated_on', $editingSweatboxFile ? $sweatboxFile->updated_on->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>
    <div class="form-group col-sm-4">
        <label class="font-weight-bold small">Order</label>
        <input type="number" name="sort_order" class="form-control" min="0" max="9999" value="{{ old('sort_order', $editingSweatboxFile ? $sweatboxFile->sort_order : 0) }}">
    </div>
</div>
