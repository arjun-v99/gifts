<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Gift;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\ImageGeneratorService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GiftController extends Controller
{
    protected $imageGeneratorService;

    // Use dependency injection to inject the service
    public function __construct(ImageGeneratorService $imageGeneratorService)
    {
        $this->imageGeneratorService = $imageGeneratorService;
    }

    public function createGift(Request $request)
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if (! $user || ! ($user instanceof User)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $senderCoinBalance = $user->coin_balance;

            $receiverId = $request->input('receiver_id');
            $receiver = User::findOrFail($receiverId);
            // $user = User::where('email', $request->gifteduser)->first();
            // if (! $user) {
            //     return response()->json(['error' => 'The user you want to gift to was not found'], 404);
            // }


            // Assuming gift creation takes 5 coins
            if ($senderCoinBalance < 5) {

                return response()->json(['error' => "You don't have enough credits to do this"], 400);
            }

            $result = $this->imageGeneratorService->createSticker("A Gift for you", 250, 80);

            $itemName = "My Sticker";
            $cost = 5;
            //  Check the status and respond accordingly
            if ($result['status']) {
                // update users balance

                $user->coin_balance -= 5;
                $user->save();
                // Create gift entry for receiver
                Gift::create([
                    'sender_id' => $user->id,
                    'receiver_id' => $receiver->id,
                    'item_name' => $itemName,
                    'cost' => $cost,
                ]);

                $filePath = $result['path'];

                return response()->json([
                    'status' => 'success',
                    'message' => 'Sticker successfully created and saved.',
                    'file_path' => $filePath,
                    'file_url' => Storage::url($filePath),
                ], 201);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create sticker image.',
                'file_path' => null,
            ], 500);
        } catch (Exception $e) {
            Log::error('Error while loading login view. Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function fetchGifts(Request $request)
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if (! $user || ! ($user instanceof User)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $sentGifts = Gift::where('sender_id', $user->id)->get();
            $receivedGifts = Gift::where('receiver_id', $user->id)->get();
            return response()->json([
                'status' => 'success',
                'sent_gifts' => $sentGifts,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error while loading login view. Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
