<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        
    }

    public function index(Request $request)
    {
        $query = Document::with(['uploadedBy', 'documentable']);
        
        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Role-based filtering
        if (Auth::user()->hasRole('marketing')) {
            $query->where(function($q) {
                $q->where('uploaded_by', Auth::id())
                  ->orWhereHasMorph('documentable', [Project::class], function($q) {
                      $q->where('pic_id', Auth::id());
                  })
                  ->orWhereHasMorph('documentable', [Client::class], function($q) {
                      $q->where('pic_id', Auth::id());
                  });
            });
        }
        
        $documents = $query->latest()->paginate(20);
        
        // Get statistics
        $stats = [
            'total_documents' => Document::count(),
            'total_size' => Document::sum('size'),
            'this_month' => Document::whereMonth('created_at', now()->month)->count(),
            'by_category' => Document::selectRaw('category, COUNT(*) as count')
                                    ->groupBy('category')
                                    ->pluck('count', 'category')
        ];
        
        return view('documents.index', compact('documents', 'stats'));
    }

    public function create(Request $request)
    {
        $projects = Project::with('client')->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        
        // Pre-select if coming from project/client
        $preselected = [
            'type' => $request->type,
            'id' => $request->id
        ];
        
        return view('documents.create', compact('projects', 'clients', 'preselected'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:penawaran,kontrak,invoice,survey,design,progress,other',
            'type' => 'required|in:pdf,image,word,excel,other',
            'description' => 'nullable|string',
            'documentable_type' => 'required|in:project,client',
            'documentable_id' => 'required|integer',
            'file' => 'required|file|max:51200', // 50MB max
            'is_public' => 'boolean'
        ]);
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            
            // Generate unique filename
            $filename = Str::uuid() . '.' . $extension;
            
            // Determine path based on documentable type
            $folder = $validated['documentable_type'] == 'project' ? 'projects' : 'clients';
            $path = $file->storeAs('documents/' . $folder . '/' . $validated['documentable_id'], $filename, 'public');
            
            // Get documentable model
            $documentableClass = $validated['documentable_type'] == 'project' 
                ? Project::class 
                : Client::class;
            
            $documentable = $documentableClass::findOrFail($validated['documentable_id']);
            
            // Create document record
            $document = new Document();
            $document->name = $validated['name'];
            $document->original_name = $originalName;
            $document->category = $validated['category'];
            $document->type = $this->determineFileType($extension);
            $document->description = $validated['description'];
            $document->path = $path;
            $document->size = $size;
            $document->extension = $extension;
            $document->uploaded_by = Auth::id();
            $document->is_public = $validated['is_public'] ?? false;
            $document->documentable()->associate($documentable);
            $document->save();
            
            return redirect()->route('documents.show', $document)
                ->with('success', 'Dokumen berhasil diupload!');
        }
        
        return back()->with('error', 'File tidak ditemukan.');
    }

    public function show(Document $document)
    {
        // Check authorization
        if (!$this->canAccessDocument($document)) {
            abort(403, 'Unauthorized');
        }
        
        $document->load(['uploadedBy', 'documentable']);
        
        // Get related documents
        $relatedDocuments = Document::where('documentable_type', $document->documentable_type)
                                   ->where('documentable_id', $document->documentable_id)
                                   ->where('id', '!=', $document->id)
                                   ->latest()
                                   ->limit(5)
                                   ->get();
        
        // Track view
        $document->increment('views');
        
        return view('documents.show', compact('document', 'relatedDocuments'));
    }

    public function edit(Document $document)
    {
        // Check authorization
        if (!Auth::user()->hasRole('admin') && $document->uploaded_by != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        // Check authorization
        if (!Auth::user()->hasRole('admin') && $document->uploaded_by != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:penawaran,kontrak,invoice,survey,design,progress,other',
            'description' => 'nullable|string',
            'is_public' => 'boolean'
        ]);
        
        $document->update($validated);
        
        return redirect()->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil diupdate!');
    }

    public function destroy(Document $document)
    {
        // Check authorization
        if (!Auth::user()->hasRole('admin') && $document->uploaded_by != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Delete file from storage
        Storage::disk('public')->delete($document->path);
        
        // Delete database record
        $document->delete();
        
        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dihapus!');
    }
    
    public function download(Document $document)
    {
        // Check authorization
        if (!$this->canAccessDocument($document)) {
            abort(403, 'Unauthorized');
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($document->path)) {
            abort(404, 'File tidak ditemukan');
        }
        
        // Track download
        $document->increment('downloads');
        
        return Storage::disk('public')->download($document->path, $document->original_name);
    }
    
    public function preview(Document $document)
    {
        // Check authorization
        if (!$this->canAccessDocument($document)) {
            abort(403, 'Unauthorized');
        }
        
        // Only allow preview for certain file types
        $previewableTypes = ['pdf', 'image'];
        if (!in_array($document->type, $previewableTypes)) {
            return redirect()->route('documents.download', $document);
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($document->path)) {
            abort(404, 'File tidak ditemukan');
        }
        
        $path = Storage::disk('public')->path($document->path);
        $mimeType = Storage::disk('public')->mimeType($document->path);
        
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"'
        ]);
    }
    
    public function bulkDownload(Request $request)
    {
        $validated = $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id'
        ]);
        
        $documents = Document::whereIn('id', $validated['document_ids'])->get();
        
        // Check authorization for each document
        foreach ($documents as $document) {
            if (!$this->canAccessDocument($document)) {
                abort(403, 'Unauthorized access to some documents');
            }
        }
        
        // Create zip file
        $zipFileName = 'documents_' . now()->format('YmdHis') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }
        
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($documents as $document) {
                $filePath = Storage::disk('public')->path($document->path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $document->original_name);
                }
            }
            $zip->close();
        }
        
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    
    private function canAccessDocument(Document $document)
    {
        // Admin can access all
        if (Auth::user()->hasRole('admin')) {
            return true;
        }
        
        // Public documents
        if ($document->is_public) {
            return true;
        }
        
        // Document owner
        if ($document->uploaded_by == Auth::id()) {
            return true;
        }
        
        // Marketing can access their project/client documents
        if (Auth::user()->hasRole('marketing')) {
            if ($document->documentable_type == 'App\Models\Project') {
                return $document->documentable->pic_id == Auth::id();
            }
            if ($document->documentable_type == 'App\Models\Client') {
                return $document->documentable->pic_id == Auth::id();
            }
        }
        
        return false;
    }
    
    private function determineFileType($extension)
    {
        $types = [
            'pdf' => ['pdf'],
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'],
            'word' => ['doc', 'docx'],
            'excel' => ['xls', 'xlsx'],
            'powerpoint' => ['ppt', 'pptx'],
            'archive' => ['zip', 'rar', '7z'],
            'video' => ['mp4', 'avi', 'mov', 'wmv'],
            'audio' => ['mp3', 'wav', 'ogg']
        ];
        
        foreach ($types as $type => $extensions) {
            if (in_array(strtolower($extension), $extensions)) {
                return $type;
            }
        }
        
        return 'other';
    }
}