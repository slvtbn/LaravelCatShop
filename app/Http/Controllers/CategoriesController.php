<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use File;

class CategoriesController extends Controller
{
    function tampil() {
        $categories = Categories::get();
        return view ('categories.categories', compact('categories'));
    }

    function tambah() {
        return view('categories.categories-entry');
    }

    // tambah data ke database
    function upload(Request $request) {
        $this->validate($request, [
            'nama' => 'required',
            'harga' => 'required',
            'gambar' => 'file|image|mimes:jpeg,png,jpg:max:2048'
        ]);

        $gambar = $request->file('gambar');
        $nama_gambar = time()."_".$gambar->getClientOriginalName();
        $simpan_gambar = 'gambar_categories';
        $gambar->move($simpan_gambar, $nama_gambar);

        Categories::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'gambar' => $nama_gambar
        ]);

        return redirect('/categories');
    }

    function edit($id_categories) {
        $categories = Categories::find($id_categories);
        return view('categories.categories-edit', compact('categories'));
    }

    // update data ke database
    function update($id_categories, Request $request) {
        $this->validate($request, [
            'nama' => 'required',
            'harga' => 'required',
            'gambar' => 'file|image|mimes:jpeg,png,jpg:max:2048'
        ]);

        $categories = Categories::find($id_categories);
        $categories->nama = $request->nama;
        $categories->harga = $request->harga;

        if($request->hasfile('gambar')) {
            File::delete('gambar_categories/'.$categories->gambar);
            $gambar = $request->file('gambar');
            $nama_gambar = time()."_".$gambar->getClientOriginalName();
            $simpan_gambar = 'gambar_categories';
            $gambar->move($simpan_gambar, $nama_gambar); 
            $categories->gambar = $nama_gambar;
        }

        $categories->save();
        return redirect('/categories');
    }

    function hapus($id_categories) {
        $categories = Categories::find($id_categories);
        return view('categories.categories-hapus', compact('categories'));
    }

    function delete($id_categories) {
        // hapus file
        $categories = Categories::find($id_categories);
        File::delete('gambar_categories/'.$categories->gambar);

        // hapus data
        $categories->delete();
        return redirect('/categories');
    }

    function noDelete() {
        return redirect('/categories');
    }
}
