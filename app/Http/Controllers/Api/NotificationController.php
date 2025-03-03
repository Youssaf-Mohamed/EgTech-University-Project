<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NotificationController extends Controller
{
    /*
    |==========================================
    |> Get all notifications for the authenticated user
    |==========================================
    */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            $notifications = $user->notifications()->paginate(10);

            return response()->json([
                'status' => true,
                'data' => $notifications,
                'pagination' => [
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /*
    |==========================================
    |> Mark a notification as read
    |==========================================
    */
    public function markAsRead($id)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Find the notification for the authenticated user
            $notification = $user->notifications()->where('id', $id)->firstOrFail();

            $notification->markAsRead();

            return response()->json([
                'status' => true,
                'message' => 'Notification marked as read',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |==========================================
    |> Mark all notifications as read
    |==========================================
    */
    public function markAllAsRead()
    {
        try {
            $user = auth()->user();

            $user->unreadNotifications->markAsRead();

            return response()->json([
                'status' => true,
                'message' => 'All notifications marked as read',
            ]);
        } catch (Exception $e) {
            return response()->json(
                ['status' => false, 'message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
