<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePartnerMessageRequest;
use App\Http\Requests\Admin\UpdatePartnerMessageRequest;
use App\Models\PartnerMessage;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PartnerMessageController extends Controller
{
    public function index(Request $request): View
    {
        $items = PartnerMessage::query()->when($request->filled('search'), fn ($query) => $query->where(fn ($search) => $search->where('name', 'like', '%'.$request->string('search').'%')->orWhere('designation', 'like', '%'.$request->string('search').'%')->orWhere('organization', 'like', '%'.$request->string('search').'%')))->when($request->filled('status'), fn ($query) => $query->where('is_active', (bool) $request->integer('status')))->ordered()->paginate(15)->withQueryString();
        return view('admin.pages.partner-messages.index', compact('items'));
    }

    public function create(): View { return view('admin.pages.partner-messages.form', ['item' => new PartnerMessage(['is_active' => true])]); }
    public function store(StorePartnerMessageRequest $request, FileUploadService $uploader): RedirectResponse { $data = $this->payload($request->validated()); $data['image_path'] = $uploader->replace($request->file('image_path'), null, 'uploads/partner-messages/portraits'); $data['organization_logo_path'] = $uploader->replace($request->file('organization_logo_path'), null, 'uploads/partner-messages/logos'); PartnerMessage::create($data); $this->clearCache(); return redirect()->route('admin.partner-messages.index')->with('success', 'Partner message created successfully.'); }
    public function edit(PartnerMessage $partnerMessage): View { return view('admin.pages.partner-messages.form', ['item' => $partnerMessage]); }
    public function update(UpdatePartnerMessageRequest $request, PartnerMessage $partnerMessage, FileUploadService $uploader): RedirectResponse
    {
        $data = $this->payload($request->validated());
        $data['image_path'] = $request->hasFile('image_path') ? $uploader->replace($request->file('image_path'), null, 'uploads/partner-messages/portraits') : $partnerMessage->image_path;
        $data['organization_logo_path'] = $request->hasFile('organization_logo_path') ? $uploader->replace($request->file('organization_logo_path'), null, 'uploads/partner-messages/logos') : $partnerMessage->organization_logo_path;
        $oldImage = $partnerMessage->image_path; $oldLogo = $partnerMessage->organization_logo_path; $partnerMessage->update($data);
        $this->deleteUnused($oldImage, $data['image_path']); $this->deleteUnused($oldLogo, $data['organization_logo_path']); $this->clearCache();
        return redirect()->route('admin.partner-messages.index')->with('success', 'Partner message updated successfully.');
    }
    public function destroy(PartnerMessage $partnerMessage): RedirectResponse { $partnerMessage->delete(); $this->clearCache(); return back()->with('success', 'Partner message deleted. Media files were retained safely.'); }
    private function payload(array $data): array { $data['is_active'] = (bool) ($data['is_active'] ?? false); $data['is_featured'] = (bool) ($data['is_featured'] ?? false); $data['display_order'] = (int) ($data['display_order'] ?? 0); return $data; }
    private function clearCache(): void { Cache::forget('partner_messages_public'); }
    private function deleteUnused(?string $oldPath, ?string $newPath): void { if ($oldPath && $oldPath !== $newPath && ! PartnerMessage::query()->where('image_path', $oldPath)->orWhere('organization_logo_path', $oldPath)->exists()) Storage::disk('public')->delete($oldPath); }
}
