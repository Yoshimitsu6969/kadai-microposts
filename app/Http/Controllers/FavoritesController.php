<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
        public function store($id)
    {
        // 認証済みユーザ（閲覧者）が、 idのユーザーのmicropostをお気に入りする
      \Auth::user()->favorite($id);
      
        // 前のURLへリダイレクトさせる
        return back();
    }
        public function destroy($id)
    {
 \Auth::user()->unfavorite($id);
        // 前のURLへリダイレクトさせる
        return back();
    }

}
