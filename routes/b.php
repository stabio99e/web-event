<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CertificateTemplate;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CertificateController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->roles !== 'admin') {
            redirect()->route('dashboard')->send();
        }
    }
    /**
     * Display the certificate form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $getCertificateTemplates = CertificateTemplate::with('event')->get();
        return view('admin.certificate.index', compact('getCertificateTemplates'));
    }
    /**
     * Show the form for creating a new certificate.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $events = Event::doesntHave('certificateTemplate')->get();
        return view('admin.certificate.create', compact('events'));
    }

    /**
     * Store a newly created certificate in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'font' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) {
                    $allowed = ['ttf', 'otf'];
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, $allowed)) {
                        $fail('The font must be a valid TTF or OTF file.');
                    }
                },
            ],
            'font_size' => 'required|integer|min:1',
            'position_x' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (strtolower($value) !== 'auto' && !is_numeric($value)) {
                        $fail('Position X harus berupa angka atau kata "auto".');
                    }
                },
            ],
            'position_y' => 'required|integer|min:0',
            'text_color' => 'required|string|max:7',
            'is_active' => 'nullable|boolean',
        ]);
        $imagePath = $request->file('image')->store('certificates', 'public');
        $imagePublicPath = '/storage/' . $imagePath;
        $fontRelative = null;
        if ($request->hasFile('font')) {
            $fontPath = $request->file('font')?->store('certificates/fonts', 'public');
            $FontPublicPath = '/storage/' . $fontPath;
        }

        CertificateTemplate::create([
            'event_id' => $request->event_id,
            'name' => $request->name,
            'image_path' => $imagePublicPath,
            'font_path' => $FontPublicPath ?? 'storage/certificates/fonts/Roboto-Bold.ttf',
            'font_size' => $request->font_size,
            'position_x' => $request->position_x,
            'position_y' => $request->position_y,
            'text_color' => $request->text_color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.certificate.show')->with('success', 'Sertifikat berhasil ditambahkan.');
    }
    /**
     * Show the form for editing an existing certificate.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $certificate = CertificateTemplate::findOrFail($id);
        $events = Event::doesntHave('certificateTemplate')->orWhere('id', $certificate->event_id)->get();

        return view('admin.certificate.edit', compact('certificate', 'events'));
    }
    /**
     * Update the specified certificate in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if ($request->has('preview')) {
            return $this->preview($request, $id);
        }

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'font' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) {
                    $allowed = ['ttf', 'otf'];
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, $allowed)) {
                        $fail('The font must be a valid TTF or OTF file.');
                    }
                },
            ],
            'font_size' => 'required|integer|min:1',
            'position_x' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (strtolower($value) !== 'auto' && !is_numeric($value)) {
                        $fail('Position X harus berupa angka atau kata "auto".');
                    }
                },
            ],
            'position_y' => 'required|integer|min:0',
            'text_color' => 'required|string|max:7',
            'is_active' => 'nullable|boolean',
        ]);

        $certificate = CertificateTemplate::findOrFail($id);

        $imagePath = $certificate->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath && file_exists(public_path($imagePath))) {
                @unlink(public_path($imagePath));
            }
            $newImage = $request->file('image')->store('certificates', 'public');
            $imagePath = '/storage/' . $newImage;
        }

        $fontPath = $certificate->font_path;
        if ($request->hasFile('font')) {
            if ($fontPath && file_exists(public_path($fontPath))) {
                @unlink(public_path($fontPath));
            }
            $fontFile = $request->file('font');
            $fontName = Str::random(10) . '.' . $fontFile->getClientOriginalExtension();
            $fontFile->storeAs('certificates/fonts', $fontName, 'public');
            $fontPath = '/storage/certificates/fonts/' . $fontName;
        }

        $certificate->update([
            'event_id' => $request->event_id,
            'name' => $request->name,
            'image_path' => $imagePath,
            'font_path' => $fontPath,
            'font_size' => $request->font_size,
            'position_x' => $request->position_x,
            'position_y' => $request->position_y,
            'text_color' => $request->text_color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Sertifikat berhasil diperbarui.');
    }

    /**
     * Generate a preview of the certificate.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function preview(Request $request, $id)
    {
        $template = CertificateTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image',
            'font' => 'nullable|file|mimes:ttf,otf',
        ]);

        $imagePath = $request->hasFile('image') ? $request->file('image')->getPathname() : public_path($template->image_path);

        if (!file_exists($imagePath)) {
            return back()->withErrors(['image' => 'Gambar template tidak ditemukan.']);
        }

        $img = (new ImageManager(new Driver()))->read($imagePath);

        $fontPath = $request->hasFile('font') ? $request->file('font')->getPathname() : ($template->font_path ? public_path($template->font_path) : null);

        if ($fontPath && !file_exists($fontPath)) {
            $fontPath = null;
        }

        $x = strtolower($template->position_x) === 'auto' ? $img->width() / 2 : (int) $template->position_x;

        $img->text($request->name, $x, $template->position_y, function ($font) use ($template, $fontPath) {
            if ($fontPath) {
                $font->filename($fontPath);
            }
            $font->size($template->font_size);
            $font->color($template->text_color ?? '#000000');
            $font->align('center');
            $font->valign('middle');
        });

        $tempFilename = tempnam(sys_get_temp_dir(), 'cert_preview_') . '.png';
        $img->save($tempFilename);

        $base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($tempFilename));

        @unlink($tempFilename);

        return back()->with('preview_base64', $base64);
    }
}
