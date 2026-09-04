<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\SweatboxFile;
use App\Models\Settings\AuditLogEntry;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SweatboxFileController extends Controller
{
    private const POSITIONS = ['CYWG_GND', 'CYWG_TWR', 'CYWG_TML'];

    public function index()
    {
        $records = SweatboxFile::orderBy('sort_order')->orderBy('name')->get();
        $sweatboxFiles = collect(self::POSITIONS)->mapWithKeys(function ($position) use ($records) {
            return [$position => $records->where('position', $position)->values()];
        });

        return view('dashboard.training.sweatbox-files', compact('sweatboxFiles'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['updated_by'] = Auth::id();

        $file = SweatboxFile::create($data);
        $this->audit('CREATE SWEATBOX FILE '.$file->name.' ('.$file->id.')');

        return redirect()->route('training.sweatbox-files')->withSuccess('Sweatbox file added.');
    }

    public function update(Request $request, $id)
    {
        $file = SweatboxFile::findOrFail($id);
        $data = $this->validated($request);
        $data['updated_by'] = Auth::id();
        $file->update($data);

        $this->audit('EDIT SWEATBOX FILE '.$file->name.' ('.$file->id.')');

        return redirect()->route('training.sweatbox-files')->withSuccess('Sweatbox file updated.');
    }

    public function destroy($id)
    {
        $file = SweatboxFile::findOrFail($id);
        $this->audit('DELETE SWEATBOX FILE '.$file->name.' ('.$file->id.')');
        $file->delete();

        return redirect()->route('training.sweatbox-files')->withSuccess('Sweatbox listing removed. The uploaded file itself was not deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'position' => ['required', Rule::in(self::POSITIONS)],
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'file_url' => ['required', 'string', 'max:2048', 'regex:#^(https?://|/)#i'],
            'updated_on' => 'required|date',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);
    }

    private function audit(string $action): void
    {
        AuditLogEntry::create([
            'user_id' => Auth::id(),
            'affected_id' => Auth::id(),
            'action' => $action,
            'time' => now(),
            'private' => 0,
        ]);
    }
}
