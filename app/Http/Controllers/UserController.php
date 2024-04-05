<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Http\Requests\UserRequest;
use \App\Models\User;
use \App\Models\Cabang;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $param;

    public function __construct()
    {
        $this->param['pageIcon'] = 'fa fa-database';
        $this->param['parentMenu'] = '/user';
        $this->param['current'] = 'User';
    }

    public function index(Request $request)
    {
        $this->param['pageTitle'] = 'List User';
        $this->param['btnText'] = 'Tambah User';
        $this->param['btnLink'] = route('user.create');

        try {
            $keyword = $request->get('keyword');
            $getUser = User::with('cabang')->orderBy('id', 'ASC');

            if ($keyword) {
                $getUser->where('id', 'LIKE', "%{$keyword}%")->orWhere('name', 'LIKE', "%{$keyword}%")->orWhere('nip', 'LIKE', "%{$keyword}%");
            }

            $this->param['user'] = $getUser->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return \view('user.index', $this->param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['pageTitle'] = 'Tambah User';
        $this->param['btnText'] = 'List User';
        $this->param['btnLink'] = route('user.index');
        $this->param['allCab'] = Cabang::get();


        return \view('user.create', $this->param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        /* TODO
        1. validasi form
        2. simpan ke db
        3. redirect ke index
        */

        $validated = $request->validated();
        try {
            $user = new User;
            $user->nip = ($request->get('nip') == null) ? null : $validated['nip'];
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];
            $user->password = \Hash::make('12345678');
            $user->id_cabang = $validated['role'] == 'PBP' ? 1 : $request->id_cabang;
            $user->save();
        } catch (Exception $e) {
            return back()->withError('Terjadi kesalahan.');
        } catch (QueryException $e) {
            return back()->withError('Terjadi kesalahan.');
        }

        return redirect()->route('user.index')->withStatus('Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->param['pageTitle'] = 'Edit User';
        $this->param['user'] = User::find($id);
        $this->param['btnText'] = 'List User';
        $this->param['btnLink'] = route('user.index');
        $this->param['allCab'] = Cabang::get();

        return view('user.edit', $this->param);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validatedData = $request->validate(
        [
            'nip' => 'sometimes|nullable|unique:users,nip,' . $user->id,
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required',
            'id_cabang' => 'present',
        ],
        [
            'email.unique' => 'Email sudah di gunakan'
            ]
        );
        try {
            $user->nip = $request->get('nip');
            $user->name = $request->get('name');
            $user->email = $request['email'];
            $user->role = $request->get('role');
            $user->id_cabang = $request['id_cabang'];
            $user->save();
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }

        return redirect()->route('user.index')->withStatus('Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
        } catch (Exception $e) {
            return back()->withError('Terjadi kesalahan.');
        } catch (QueryException $e) {
            return back()->withError('Terjadi kesalahan.');
        }

        return redirect()->route('user.index')->withStatus('Data berhasil dihapus.');
    }

    public function changePassword()
    {
        $this->param['pageTitle'] = 'Edit Password';
        $this->param['user'] = User::find(auth()->user()->id);
        if (Auth::user()->password_change_at == null) {
            $this->param['force'] = true;
        } else {
            $this->param['force'] = false;
        }
        return view('user.change-password', $this->param);
    }

    public function updatePassword(Request $request, $id)
    {
        // return $request;
        $user = User::findOrFail($id);;
        $old = $request->old_pass;
        $new = $request->password;

        if (!\Hash::check($old, $user->password)){
            return back()->withError('Password lama tidak cocok.');
        }

        if (\Hash::check($new, $user->password)){
            return back()->withError('Password baru tidak boleh sama dengan password lama.');
        }

        $validatedData = $request->validate(
            [
                'old_pass' => 'required',
                'password' => 'required',
                'confirmation' => 'required|same:password'
            ],
            [
                'required' => ':attribute harus diisi.',
                'password.unique' => 'Password baru tidak boleh sama dengan password lama.',
                'same' => 'Konfirmasi password harus sesuai.'
            ],
            [
                'old_pass' => 'Password lama',
                'password' => 'Password baru',
                'confirmation' => 'Konfirmasi password baru',
            ]
        );

        try {
            $user->password = \Hash::make($request->get('password'));
            $user->password_change_at = now();
            $user->save();
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }

        alert()->success('Berhasil', 'Password berhasil diperbarui.');
        return redirect()->route('dashboard')->withStatus('Password berhasil diperbarui.');
    }

    function resetPassword($id)
    {
        $user = User::findOrFail($id);
        try {
            $user->password = \Hash::make('12345678');
            $user->password_change_at = null;
            $user->save();
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi Kesalahan.' . $e);
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi Kesalahan.' . $e);
        }

        alert()->success('Berhasil', 'Password berhasil di-reset.');
        return back()->withStatus('Password berhasil di-reset.');
    }

    function indexSession(Request $request)
    {
        $this->param['pageTitle'] = 'Master Session';

        try {
            $keyword = $request->get('keyword');
            $data = DB::table('sessions')
                ->join('users', 'users.id', 'sessions.user_id')
                ->select('users.name', 'users.email', 'users.nip', 'users.role', 'sessions.id', 'users.id_cabang', 'sessions.user_id', 'sessions.device_name', 'sessions.ip_address', 'sessions.created_at')
                ->when($request->keyword, function ($query, $search) {
                    return $query->where('users.email', 'like', '%' . $search . '%');
                })
                ->paginate(10);

            $pengajuanController = new PengajuanKreditController;
            foreach ($data as $key => $value) {
                $value->karyawan = null;
                if ($value->nip) {
                    $karyawan = $pengajuanController->getKaryawanFromAPI($value->nip);

                    if ($karyawan) {
                        if (is_array($karyawan)) {
                            if (!array_key_exists('error', $karyawan))
                                $value->karyawan = $karyawan;
                        }
                    }
                }
            }

            $this->param['data'] = $data;
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('user.sessions.index', $this->param);
    }

    public function resetSession($id)
    {
        try {
            DB::table('sessions')
                ->where('user_id', $id)
                ->delete();

            return back()->withStatus('Berhasil menghapus session.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi Kesalahan.' . $e);
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi Kesalahan.' . $e);
        }
    }

    public function indexAPISession(Request $request) {
        $this->param['pageTitle'] = 'Master Session Mobile';

        try {
            $data = DB::table('personal_access_tokens')
                ->join('users', 'users.id', 'personal_access_tokens.tokenable_id')
                ->select('users.name', 'users.email', 'users.nip', 'users.role', 'personal_access_tokens.id', 'personal_access_tokens.device_name', 'personal_access_tokens.ip_address', 'users.id_cabang', 'personal_access_tokens.tokenable_id', 'personal_access_tokens.created_at', 'personal_access_tokens.project')
                ->when($request->keyword, function ($query, $search) {
                    return $query->where('users.email', 'like', '%' . $search . '%');
                })
                ->paginate(10);

            $pengajuanController = new PengajuanKreditController;
            foreach ($data as $key => $value) {
                $value->karyawan = null;
                if ($value->nip) {
                    $karyawan = $pengajuanController->getKaryawanFromAPI($value->nip);

                    if ($karyawan) {
                        if (is_array($karyawan)) {
                            if (!array_key_exists('error', $karyawan))
                                $value->karyawan = $karyawan;
                        }
                    }
                }
            }

            $this->param['data'] = $data;
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('user.api-sessions.index', $this->param);
    }

    public function resetAPISession($id) {
        try {
            DB::table('personal_access_tokens')
                ->where('id', $id)
                ->delete();

            return back()->withStatus('Berhasil menghapus API session.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi Kesalahan.' . $e);
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi Kesalahan.' . $e);
        }
    }
}
