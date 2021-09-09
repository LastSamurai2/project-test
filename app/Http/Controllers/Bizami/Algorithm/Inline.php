<?php

namespace App\Http\Controllers\Bizami\Algorithm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bizami\Algorithm;
use Illuminate\Support\Facades\Schema;

class Inline extends Controller
{


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateAlgorithmRequest($request);

        $algorithms = Algorithm::all();
        $algorithm = $algorithms->find($id);
        $this->setAlgorithmData($algorithm, $request);
        $algorithm->save();

        return response([
            'message' => __('bizami.algorithm.flash.updated')
        ], 201);
    }


    /**
     * @param $request
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateAlgorithmRequest($request)
    {
        // TODO: this must stay static - I think.
        $this->validate(
            $request,
            [
                'name' => 'required',
            ]);
    }

    /**
     * @param $algorithm
     * @param $request
     */
    protected function setAlgorithmData($algorithm, $request)
    {
        $columns = Schema::getColumnListing($algorithm->getTable());
        foreach($columns as $key)
        {
            if (isset($request->$key)) {
                $algorithm->$key = $request->$key;
            }
        }

        return $this;
    }
}
