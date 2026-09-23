<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::latest()->get();
        return view('admin.appointments.index', compact('appointments'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Bekliyor,Onaylandı,İptal'
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->status = $request->status;
        $appointment->save();

        return redirect()->route('admin.appointments.index')->with('success', 'Randevu durumu güncellendi.');
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('success', 'Randevu başarıyla silindi.');
    }
}