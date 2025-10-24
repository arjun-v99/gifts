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
            $request->validate([
                'receiver' => 'required|email|max:255'
            ]);

            $user = Auth::guard('sanctum')->user();
            $senderCoinBalance = $user->coin_balance;

            // Assuming gift creation takes 5 coins
            if ($senderCoinBalance < 5) {
                return response()->json(['error' => "You don't have enough credits to do this"], 400);
            }

            $receiver = $request->input('receiver');

            $findReceiverUser = User::where('email', $receiver)->first();

            if ($findReceiverUser === null) {
                return response()->json(['error' => 'The receiver for the gift is not found'], 404);
            }

            $result = $this->imageGeneratorService->createSticker("A Gift for you", 200, 100);

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
                    'receiver_id' => $findReceiverUser->id,
                    'item_name' => $itemName,
                    'file_path' => $result['path'],
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
            Log::error('Error while creating a gift. Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function fetchGifts(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            $sentGifts = Gift::where('sender_id', $user->id)->get();
            $receivedGifts = Gift::where('receiver_id', $user->id)->get();
            return response()->json([
                'status' => 'success',
                'sent_gifts' => $sentGifts,
                'received_gifts' => $receivedGifts
            ], 200);
        } catch (Exception $e) {
            Log::error('Error while loading your gifts. Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function viewGift(Request $request)
    {
        try {
            $giftId = $request->route('giftId');
            $user = Auth::guard('sanctum')->user();

            // Find the gift by ID
            $gift = Gift::find($giftId);

            // If not found
            if (! $gift) {
                return response()->json([
                    'message' => 'Gift not found',
                ], 404);
            }

            // Check ownership
            if ($gift->receiver_id !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized access',
                ], 403);
            }

            return Storage::disk('private')->download($gift->file_path);
        } catch (Exception $e) {
            Log::error('Error while downloading your gift sticker. Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
