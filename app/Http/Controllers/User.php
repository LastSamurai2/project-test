<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Validation\Rule;

class User extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('name', 'LIKE', "%{$value}%")->orWhere('email', 'LIKE', "%{$value}%");
            });
        });

        $users = QueryBuilder::for(\App\Models\User::class)
            ->defaultSort('id')
            ->allowedFilters(['name', 'email', $globalSearch])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('User', [
            'users' => $users,
            'buttons' => [
                array(
                    "label" => "button.add-new",
                    "url" => route('user.new')
                )
            ]
        ])->table(function (InertiaTable $table) {
            $table->addSearchRows([
                'email' => __('Email'),
                'name' => __('Name')
            ]);
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(\Illuminate\Http\Request $request)
    {
        Request::validate([
            'name' => ['required'],
            'email' => ['required', 'email', Rule::unique('users')],
            'password' => ['required', 'confirmed', 'min:6']
        ]);

        $user = new \App\Models\User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return Redirect::route('user')->withFlash(['success' => __('user.flash.created')]);
    }

    /**
     * @param $id
     * @return \Inertia\Response
     */
    public function edit($id = null)
    {
        if ($id) {
            $users = \App\Models\User::all();
            $user = $users->find($id);
        } else {
            $user = new \App\Models\User();
        }

        return Inertia::render('User/Edit', [
            'editeduser' => $user,
            'buttons' => [
                array(
                    "label" => "button.back",
                    "url" => route('user')
                )
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(\Illuminate\Http\Request $request, $userId)
    {
        Request::validate([
            'name' => ['required'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
        ]);

        $users = \App\Models\User::all();
        $user = $users->find($userId);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return Redirect::back()->withFlash(['success' => __('user.flash.updated')]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(\Illuminate\Http\Request $request, $id)
    {
        \App\Models\User::destroy($id);
        return Redirect::route('user')->withFlash(['success' => __('user.flash.deleted')]);
    }
}
