<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactController extends Controller
{
public function index()
    {
        ContactMessage::where('is_read', 0)->update(['is_read' => 1]);

        $contacts = ContactMessage::latest()->paginate(15);
        
        return view('admin.contact.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'kvkk'    => 'required', 
        ]);

        ContactMessage::create([
            'name'    => $validated['name'],
            'phone'   => $validated['phone'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        return redirect()->back()->with('success', 'Mesajınız başarıyla gönderildi. En kısa sürede sizinle iletişime geçilecektir.');
    }

    public function destroy($id)
    {
        $contact = ContactMessage::findOrFail($id);
        $contact->delete();

        return redirect()->back()->with('success', 'Mesaj başarıyla silindi.');
    }
}