<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Profile updated successfully'
        ]);
    }

    public function addresses(Request $request)
    {
        $addresses = $request->user()->addresses()->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string',
            'line1' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'is_default' => 'boolean'
        ]);

        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address added successfully'
        ]);
    }

    public function updateAddress(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'label' => 'sometimes|string',
            'line1' => 'sometimes|string',
            'city' => 'sometimes|string',
            'district' => 'sometimes|string',
            'is_default' => 'boolean'
        ]);

        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address updated successfully'
        ]);
    }

    public function destroyAddress(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }
}
