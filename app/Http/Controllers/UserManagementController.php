<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modeluserprofile;
use App\Models\Modeluser; // ganti jika modelmu berbeda

class UserManagementController extends Controller
{
    // Listing + search + filter + pagination
   public function index(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');

    $users = Modeluser::query()
        ->when($search, function($q, $search) {
            $q->where(function($sub) use ($search) {
                $sub->where('nama_user', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        })
        ->when($status && $status !== 'all', function($q, $status) {
           $q->where('status', $status);})
        ->orderBy('id_user', 'desc')
        ->paginate(10)
        ->appends($request->only(['search','status']));

        // Jika ada param view (contoh: ?view=12), tampilkan halaman detail
       if ($request->filled('view')) {
    $detailUser = Modeluser::with('profile')->findOrFail($request->input('view'));

    return view('admin.users.detail', [
        'detailUser' => $detailUser,
        'detailUserprofile' => $detailUser->profile,
        'search' => $search,
        'status' => $status,
    ]);
}



    return view('admin.users.index', compact('users','search','status'));
}
public function resetPassword($id_user)
{
    $user = Modeluser::findOrFail($id_user);

    // password default bisa kamu ubah, sementara saya set ke 123456
    $user->password = bcrypt('12345678');
    $user->save();

    return back()->with('success', 'Password untuk ' . $user->nama_user . ' berhasil direset ke: 12345678');
}

    // Approve user (set status to 'approved')
    public function approve($id)
    {
        $user = Modeluser::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        return redirect()->back()->with('success', 'User berhasil disetujui.');
    }
      public function pending($id)
    {
        $user = Modeluser::findOrFail($id);
        $user->status = 'pending';
        $user->save();

        return redirect()->back()->with('success', 'User diubah ke pending.');
    }

    // Delete user (permanen). Ganti ke soft-delete jika perlu.
    public function delete($id)
    {
        $user = Modeluser::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
