<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function store(Request $request)
{
   
    $validated = $request->validate([
        'name'                => 'required|string|max:255', 
        'phone'               => 'required|string|max:20', 
        'manken_cinsiyet'     => 'required|string',
        'manken_kilo'         => 'required|string',
        'manken_boy'          => 'required|string',
        'secilen_kategori'    => 'required|string',
        'tasarim_ozeti'       => 'nullable|string',
        'cizim_katmani'       => 'nullable|string', 
        'referans_resimler.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096', 
        'gogus'               => 'nullable|integer',
        'bel'                 => 'nullable|integer',
        'kalca'               => 'nullable|integer',
        'ekstra_olculer'      => 'nullable|string',
    ]);

    $uploadedImages = [];
    if ($request->hasFile('referans_resimler')) {
        foreach ($request->file('referans_resimler') as $file) {
            $path = $file->store('referanslar', 'public');
            $uploadedImages[] = $path;
        }
    }

    $appointment = Appointment::create([
        'name'              => $request->name,  
        'phone'             => $request->phone, 
        'kisisel_bilgiler'  => null,

        'gender'            => $request->manken_cinsiyet,
        'manken_kilo'       => $request->manken_kilo,
        'height'            => $request->manken_boy,
        'chest'             => $request->gogus,
        'waist'             => $request->bel,
        'hip'               => $request->kalca,
        'ekstra_olculer'    => $request->ekstra_olculer,
        'clothing_type'     => $request->secilen_kategori,
        'message'           => $request->tasarim_ozeti,
        'cizim_katmani'     => $request->cizim_katmani, 
        'referans_resimler' => $uploadedImages,
        'status'            => 'bekliyor',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Tasarım randevunuz başarıyla oluşturuldu!',
        'data'    => $appointment
    ], 200);
}

}