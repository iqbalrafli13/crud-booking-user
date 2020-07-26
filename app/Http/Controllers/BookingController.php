<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Booking;

class BookingController extends Controller
{

    public function booking(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $tanggalawal = date('Y-m-d',strtotime( '+1 Day'));
        $tanggalakhir = date('Y-m-d',strtotime( '+7 Day'));

        $this->validate($request,[
            'booking_date'=> "required|after_or_equal:".$tanggalawal."|before_or_equal:".$tanggalakhir."|date|date_format:Y-m-d",
            'nokendaraan'=>  "required" ,
            'namakendaraan'=> "required" ,
        ]);
        if ($this->isBooked( $request->json("nokendaraan"))) {
            return 'ada';
        }else {
            return 'gakada';
        }
        die();
        $booking = Auth()->user()->bookings()->create([
            'kode'=> $this->getnomerantrian($request->json("booking_date")),
            'booking_date'=> $request->json("booking_date"),
            'nokendaraan'=>  $request->json("nokendaraan"),
            'namakendaraan'=> $request->json("namakendaraan"),
        ]);

        return $booking;
    }

    public function konfirmasi(Request $request)
    {
        $this->validate($request,['kode'=> "required",]);
        $booking =  Booking::where('kode',$request->json("kode"));
        $booking->update(['status'=>1]);

        return response()->json(['response' => 'booking terkonfirmasi'], 200);
    }

    public function isBooked($nokendaraan)
    {
        return Auth()->user()->bookings()
        ->where('nokendaraan', $nokendaraan)
        ->where('status', 0)->first();
    }
    
    public function getnomerantrian($tanggal)
    {
        $sNextKode = "";
        $sLastKode = "";
        date_default_timezone_set('Asia/Jakarta');
        $value =  Booking::where('booking_date',$tanggal)
        ->orderBy('id', 'DESC')->get();

        if (isset($value[0])) { // jika sudah ada, langsung ambil dan proses...
            $value = $value[0]->nomorantrean;
            $sLastKode = intval(substr($value, 3, 3)); // ambil 3 digit terakhir
            $sLastKode = intval($sLastKode) + 1; // konversi ke integer, lalu tambahkan satu
            $sNextKode = sprintf('%03s', $sLastKode); // format hasilnya dan tambahkan prefix
            if (strlen($sNextKode) > 6) {
                $sNextKode = "999";
            }
        } else { // jika belum ada, gunakan kode yang pertama
            $sNextKode = "001";
        }
        return date('ymd',strtotime( '+1 Day')).$sNextKode;
    }

}
