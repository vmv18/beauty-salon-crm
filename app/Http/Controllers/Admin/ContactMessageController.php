<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.contact-messages.index', compact('messages'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactMessage $contactMessage)
    {
        // Позначити як прочитане
        if (!$contactMessage->is_read) {
            $contactMessage->markAsRead();
        }

        return view('admin.contact-messages.show', compact('contactMessage'));
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->markAsRead();

        return back()->with('success', 'Повідомлення позначено як прочитане.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Повідомлення видалено успішно.');
    }
}
